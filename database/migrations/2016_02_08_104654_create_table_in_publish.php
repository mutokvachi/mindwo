<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInPublish extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_publish_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->nullable()->comment = "Nosaukums";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('in_publish', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('publish_type_id')->nullable()->unsigned()->comment = "Tips";
            $table->string('nr', 50)->nullable()->comment = "Izdevuma numurs";
            $table->date('pub_date')->nullable()->comment = "Izdevuma datums";
            $table->string('prev_file_name', 500)->nullable()->comment = "Attēla datne";
            $table->string('prev_file_guid', 50)->nullable();
            $table->string('file_name', 500)->nullable()->comment = "PDF datne";
            $table->string('file_guid', 50)->nullable();
            $table->integer('order_index')->default(0)->comment = "Secība";  
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('publish_type_id');
        });
        
        Schema::table('in_publish', function (Blueprint $table) {
            $table->foreign('publish_type_id')->references('id')->on('in_publish_types');
        });
        
        DB::table('in_publish_types')->insert([
            ['title' => 'Latvenergo vēstis'],
            ['title' => 'Enerģijas ziņas'],
            ['title' => 'Elektrodrošība'],
            ['title' => 'Energo efektivitāte']
        ]);
        
        DB::table('in_publish')->insert([
            ['publish_type_id' => 1, 'nr' => '1', 'pub_date' => '2016-01-05', 'prev_file_name' => '8ca5e5cc-1c77-4a13-9bbe-4d18e069f851.jpg', 'prev_file_guid' => '8ca5e5cc-1c77-4a13-9bbe-4d18e069f851.jpg', 'file_name' => 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', 'file_guid' => 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf'],
            ['publish_type_id' => 2, 'nr' => '2', 'pub_date' => '2016-01-05', 'prev_file_name' => '5b8601e9-cc90-431d-8133-c9f365a78eb0.jpg', 'prev_file_guid' => '5b8601e9-cc90-431d-8133-c9f365a78eb0.jpg', 'file_name' => 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', 'file_guid' => 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf'],
            ['publish_type_id' => 2, 'nr' => '3', 'pub_date' => '2016-02-05', 'prev_file_name' => '4c97fccc-4de6-4b5d-8e23-44e11431d74e.JPG', 'prev_file_guid' => '4c97fccc-4de6-4b5d-8e23-44e11431d74e.JPG', 'file_name' => 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', 'file_guid' => 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_publish');
        Schema::dropIfExists('in_publish_types');
    }
}
