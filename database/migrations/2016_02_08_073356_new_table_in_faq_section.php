<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInFaqSection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_faq_section', function (Blueprint $table) {
            $table->increments('id');

            $table->boolean('is_active')->nullable()->comment = "Ir aktīva nodaļa";
            $table->string('section_name', 1000)->comment = "Nodaļas nosaukums";            

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_faq_section');
    }
}
