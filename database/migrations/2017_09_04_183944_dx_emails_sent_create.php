<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxEmailsSentCreate extends Migration
{
    private $table_name = "dx_emails_sent";
    
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists($this->table_name);
        
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id'); 
    
            $table->integer('template_id')->unsigned()->comment = trans('db_' . $this->table_name.'.template_id');
            $table->string('mail_subject', 250)->comment = trans('db_' . $this->table_name.'.mail_subject');
            $table->integer('user_id')->comment = trans('db_' . $this->table_name.'.user_id');
            $table->datetime('sent_time')->nullable()->comment = trans('db_' . $this->table_name.'.sent_time');
            $table->text('mail_text')->comment = trans('db_' . $this->table_name.'.mail_text');
            
            $table->index('template_id');            
            $table->foreign('template_id')->references('id')->on('dx_emails_templates');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
            
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
        Schema::dropIfExists($this->table_name);
    }
}
