<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableDxConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('config_name', 200)->nullable();
            $table->string('config_hint', 1000)->nullable();
            $table->integer('field_type_id')->nullable();
            $table->string('val_varchar', 500)->nullable();  
            $table->text('val_script')->nullable();
            $table->integer('val_integer')->nullable();
            $table->date('val_date')->nullable();
            $table->string('val_file_name', 500)->nullable(); 
            $table->string('val_file_guid', 500)->nullable(); 
            $table->integer('val_yesno')->nullable();
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('config_name');
        });
        
        DB::table('dx_config')->insert([
            ['config_name' => 'PORTAL_LOGO_FILE', 'config_hint' => 'Portāla logo datne. Logo tiek attēlots lapu augšējā kreisajā stūrī', 'field_type_id' => 12, 'val_file_name' => 'leport_logo.gif', 'val_file_guid' => 'leport_logo.gif']
        ]);
        
        DB::table('dx_config')->insert([
            ['config_name' => 'PORTAL_NAME', 'config_hint' => 'Portāla nosaukums. Tiek likts priekšā visiem lapu nosaukumiem, piemēram, Nosaukums :: Lapas nosaukums', 'field_type_id' => 1, 'val_varchar' => 'Latvenergo']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_config');
    }
}
