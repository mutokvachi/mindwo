<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFieldsAddRegNrLogic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->boolean('is_fields_synchro')->nullable()->comment = "Vai saistītie lauki savstarpēji jāsinhronizē";
            $table->boolean('is_manual_reg_nr')->nullable()->comment = "Vai reģistrācijas numurs jāģenerē manuāli uz pogas nospiešanu";
            $table->integer('reg_role_id')->nullable()->comment = "Loma, kurai tiesības manuāli reģistrēt";
            
            $table->index('reg_role_id');
            $table->foreign('reg_role_id')->references('id')->on('dx_roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->dropColumn(['is_fields_synchro']);
            $table->dropColumn(['is_manual_reg_nr']);
            
            $table->dropForeign(['reg_role_id']);
            $table->dropColumn(['reg_role_id']);
        });
    }
}
