<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InTestsAddFields extends Migration
{
    private $table_name = "in_tests";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->integer('test_type_id')->unsigned()->nullable()->comment = trans($this->table_name.'.test_type_id');
            $table->datetime('available_till')->nullable()->comment = trans($this->table_name.'.available_till');
            $table->integer('fill_minutes_limit')->nullable()->default(0)->comment = trans($this->table_name.'.fill_minutes_limit');
            $table->integer('perform_count_limit')->nullable()->default(0)->comment = trans($this->table_name.'.perform_count_limit');
            $table->boolean('is_template')->nullable()->default(false)->comment = trans($this->table_name.'.is_template');
            $table->boolean('is_avail_public')->nullable()->default(false)->comment = trans($this->table_name.'.is_avail_public');
            $table->text('finish_msg')->nullable()->comment = trans($this->table_name.'.finish_msg');
            
            $table->index('test_type_id');            
            $table->foreign('test_type_id')->references('id')->on('in_tests_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropForeign(['test_type_id']);
            $table->dropColumn(['test_type_id']);
            $table->dropColumn(['available_till']);
            $table->dropColumn(['fill_minutes_limit']);
            $table->dropColumn(['perform_count_limit']);
            $table->dropColumn(['is_template']);
            $table->dropColumn(['is_avail_public']);
            $table->dropColumn(['finish_msg']);
        });
    }
}
