<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsMembersAddInvoiceId extends Migration
{
     private $table_name = "edu_subjects_groups_members";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->integer('invoice_id')->unsigned()->nullable()->comment = 'Rēķins';

            $table->index('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('edu_invoices');
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
            $table->dropForeign(['invoice_id']);
            $table->dropIndex(['invoice_id']);
            $table->dropColumn(['invoice_id']);
        });
    }
}