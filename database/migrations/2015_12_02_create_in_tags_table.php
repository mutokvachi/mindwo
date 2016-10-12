<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->string('link', 500);
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // Fill default web text values
        DB::table('in_tags')->insert([
            ['name' => 'Aktualitātes', 'link' => 'http://latvenergo'],
            ['name' => 'Jauni darbinieki', 'link' => 'http://latvenergo'],
            ['name' => 'Elektrum', 'link' => 'http://latvenergo'],
            ['name' => 'Video', 'link' => 'http://latvenergo'],
            ['name' => 'Attēli', 'link' => 'http://latvenergo'],
            ['name' => 'Latvenergo', 'link' => 'http://latvenergo'],
            ['name' => 'Sadales tīkls', 'link' => 'http://latvenergo'],
            ['name' => 'Nozares ziņas', 'link' => 'http://latvenergo'],
            ['name' => 'Bonusi', 'link' => 'http://latvenergo'],
            ['name' => 'Vakances', 'link' => 'http://latvenergo'],
            ['name' => 'Piedāvājumi darbiniekiem', 'link' => 'http://latvenergo'],
            ['name' => 'Pasākumi', 'link' => 'http://latvenergo'],
            ['name' => 'Standarti', 'link' => 'http://latvenergo'],
            ['name' => 'Mediji', 'link' => 'http://latvenergo'],
            ['name' => 'Pateicības', 'link' => 'http://latvenergo'],
            ['name' => 'Attīstība', 'link' => 'http://latvenergo']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_tags');
    }
}