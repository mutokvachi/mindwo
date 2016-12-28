<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAddNotifyFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_views', function (Blueprint $table) {
            $table->boolean('is_email_sending')->default(false)->nullable()->comment = trans('db_dx_views.is_email_sending_list');
            $table->text('email_receivers')->nullable()->comment = trans('db_dx_views.email_receivers_list');
            $table->integer('role_id')->nullable()->comment = trans('db_dx_views.role_id_list');
            $table->integer('field_id')->nullable()->comment = trans('db_dx_views.field_id_list');
            
            $table->index('role_id');
            $table->foreign('role_id')->references('id')->on('dx_roles');
            
            $table->index('field_id');
            $table->foreign('field_id')->references('id')->on('dx_lists_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_views', function (Blueprint $table) {                       
            $table->dropForeign(['role_id']);
            $table->dropForeign(['field_id']);
            $table->dropColumn(['role_id', 'field_id', 'is_email_sending', 'email_receivers']);
        });
    }
}
