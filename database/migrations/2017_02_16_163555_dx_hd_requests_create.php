<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_hd_requests');
        
        Schema::create('dx_hd_requests', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('reporter_empl_id')->comment = "Pieteicējs";
            $table->datetime('request_time')->comment = "Pieteikuma laiks";
            $table->integer('request_type_id')->unsigned()->comment = "Problēmas veids";
            $table->text('description')->nullable()->comment = "Problēmas apraksts";
            
            $table->string('file_name', 1000)->nullable()->comment = "Datne";
            $table->string('file_guid', 50)->nullable()->comment = "Datnes GUID";
            
            $table->integer('priority_id')->nullable()->unsigned()->comment = "Prioritāte";
            
            $table->integer('inner_type_id')->nullable()->unsigned()->comment = "Pieteikuma tips";
            
            $table->integer('responsible_empl_id')->nullable()->comment = "Atbildīgais";
            
            $table->integer('status_id')->nullable()->unsigned()->comment = "Izpildes statuss";
            $table->datetime('status_time')->nullable()->comment = "Statusa laiks";
            
            $table->integer('dx_item_status_id')->nullable()->comment = "Darbplūsmas statuss";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('request_type_id');            
            $table->foreign('request_type_id')->references('id')->on('dx_hd_request_types');
            
            $table->index('inner_type_id');            
            $table->foreign('inner_type_id')->references('id')->on('dx_hd_inner_types');
            
            $table->index('priority_id');            
            $table->foreign('priority_id')->references('id')->on('dx_hd_priorities');
            
            $table->index('status_id');            
            $table->foreign('status_id')->references('id')->on('dx_hd_statuses');
            
            $table->index('responsible_empl_id');            
            $table->foreign('responsible_empl_id')->references('id')->on('dx_users');
            
            $table->index('dx_item_status_id');            
            $table->foreign('dx_item_status_id')->references('id')->on('dx_item_statuses');
            
            $table->index('reporter_empl_id');            
            $table->foreign('reporter_empl_id')->references('id')->on('dx_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_hd_requests');
    }
}
