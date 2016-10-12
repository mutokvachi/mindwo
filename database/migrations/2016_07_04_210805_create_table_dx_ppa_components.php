<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDxPpaComponents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('dx_ppa_texts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->nullable()->comment = "Nosaukums";
            $table->text('html_description')->nullable()->comment = "Apraksts";
            $table->boolean('is_page')->nullable()->default(0)->comment = "Ir lapa";
            $table->boolean('is_cover')->nullable()->default(0)->comment = "Ir pirmā lapa";
            $table->boolean('is_begin')->nullable()->default(0)->comment = "Ir jāievieto sākumā";
            $table->string('code', 50)->nullable()->comment = "Kods";
            $table->integer('order_index')->nullable()->default(0)->comment = "Secība";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_ppa_components', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->nullable()->comment = "Nosaukums";
            $table->text('html_description')->nullable()->comment = "Apraksts";
            $table->boolean('is_generate')->nullable()->default(0)->comment = "Ir apraksta ģenerēšana";
            $table->text('html_decomposition')->nullable()->comment = "Dekompozīcijas apraksts";
            $table->text('html_interface')->nullable()->comment = "Saskarnes apraksts";
            $table->integer('order_index')->nullable()->default(0)->comment = "Secība";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_ppa_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->nullable()->comment = "Nosaukums";
            $table->text('html_description')->nullable()->comment = "Apraksts";
            $table->integer('component_id')->nullable()->unsigned()->comment = "Komponente";
            $table->integer('order_index')->nullable()->default(0)->comment = "Secība";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('component_id');
            $table->foreign('component_id')->references('id')->on('dx_ppa_components');
        });
        
        Schema::table('dx_lists_groups', function (Blueprint $table) {
            $table->integer('module_id')->nullable()->unsigned()->comment = "Modulis";            
            
            $table->index('module_id');
            $table->foreign('module_id')->references('id')->on('dx_ppa_modules');
        });
        
        DB::table('dx_ppa_components')->insert([
            ['title' => 'Publiskais portāls', 'is_generate' => 0, 'order_index'=>10, 'html_description' => '<p>E-rokasgrāmatas publiskais portāls nodrošina e-rokasgrāmatas publiski pieejamās informācijas attēlošanu.</p>'],
            ['title' => 'Administrācija', 'is_generate' => 1, 'order_index'=>20, 'html_description' => '<p>Administrācijas vietne pieejama tikai autorizētiem lietotājiem, kuriem ir tiesības ievadīt publiskā portāla saturu.</p>'],
            ['title' => 'Lietotne', 'is_generate' => 0, 'order_index'=>30, 'html_description' => '<p>Lejuplādējama un Windows operētājsistēmas datoros instalējama lietotne, kura nodrošina sekojošu funkcionalitāti: rokasgrāmatas attēlošana, meklēšana, marķēšana, piezīmju veidošana, veidlapu pievienošana un anotāciju veidošana.</p>'],
            ['title' => 'Anotācija', 'is_generate' => 0, 'order_index'=>40, 'html_description' => '<p>PDF formāta datne, kurā definēti aizpildāmie lauki un klasifikatoru izvēlnes.</p>'],
        ]);
        
        DB::table('dx_ppa_modules')->insert([
            ['title' => 'Portāla saturs', 'component_id' => 2, 'order_index'=>10, 'html_description' => '<p>Modulis nodrošina iespēju pārvaldīt portāla publiski pieejamo saturu.</p>'],
            ['title' => 'Portāla konfigurācija', 'component_id' => 2, 'order_index'=>20, 'html_description' => '<p>Modulis nodrošina iespēju konfigurēt portāla iestatījumus.</p>'],
            ['title' => 'Sistēmas konfigurācija', 'component_id' => 2, 'order_index'=>30, 'html_description' => '<p>Modulis nodrošina iespēju būvēt visu sistēmu - veidot visa veida reģistrus.</p>'],
            ['title' => 'Lietotāju pārvaldība', 'component_id' => 2, 'order_index'=>40, 'html_description' => '<p>Modulis nodrošina iespēju pārvaldīt lietotājus, lomas un tiesības</p>'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists_groups', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn(['module_id']);
        });
        
        Schema::dropIfExists('dx_ppa_texts');
        Schema::dropIfExists('dx_ppa_modules');
        Schema::dropIfExists('dx_ppa_components');
        
    }
}
