<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInFaqSectionSource extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_faq_section_source', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('faq_section_id')->unsigned()->comment = "Nodaļas identifikators";
            $table->integer('source_id')->nullable()->comment = "Datu avots";

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->index('source_id');
            $table->foreign('source_id')->references('id')->on('in_sources');
            $table->index('faq_section_id');
            $table->foreign('faq_section_id')->references('id')->on('in_faq_section');
        });

        $section_id = DB::table('in_faq_section')->insertGetId(
                ['section_name' => 'Galvenie jautājumi', 'is_active' => 1]
                , 'id');

        DB::table('in_faq_question')->insert([
            ['question' => 'Ko darīt, ja paliek skumji?', 'answer' => 'Nepieciešamības gadījumā apskaut un samīļot blakus esošos kolēģus.', 'faq_section_id' => $section_id, 'is_active' => 1],
            ['question' => 'Kā rīkoties gadījumos, kad līst lietus?', 'answer' => 'Uzvārīt karstu tasi tējas un priecāties, ka šobrīd lasi šo jautājumu (cerams iekštelpās pie datora) nevis atrodies arā lietū.', 'faq_section_id' => $section_id, 'is_active' => 1],           
        ]);
        
        $section_id = DB::table('in_faq_section')->insertGetId(
                ['section_name' => 'Neloģiskie jautājumi', 'is_active' => 1]
                , 'id');

        DB::table('in_faq_question')->insert([
            ['question' => 'Būt vai nebūt?', 'answer' => 'Apēst saldējumu.', 'faq_section_id' => $section_id, 'is_active' => 1],
            ['question' => 'Kas tas?', 'answer' => 'Kur, kas?', 'faq_section_id' => $section_id, 'is_active' => 1],
            ['question' => 'Kā man iet?', 'answer' => 'Man iet labi. Un tev?', 'faq_section_id' => $section_id, 'is_active' => 1]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_faq_section_source', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
            $table->dropForeign(['faq_section_id']);
        });
        Schema::dropIfExists('in_faq_section_source');
    }

}
