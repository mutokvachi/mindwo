<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxEmailsTemplatesCreate extends Migration
{
    private $table_name = "dx_emails_templates";
    
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

            $table->string('title', 250)->comment = trans('db_' . $this->table_name.'.title');
            $table->string('code', 50)->comment = trans('db_' . $this->table_name.'.code');
            $table->string('mail_subject', 250)->comment = trans('db_' . $this->table_name.'.mail_subject');
            $table->text('mail_text')->nullable()->comment = trans('db_' . $this->table_name.'.mail_text');
            $table->string('title_bg_color', 20)->comment = trans('db_' . $this->table_name.'.title_bg_color');
            $table->string('title_fore_color', 20)->comment = trans('db_' . $this->table_name.'.title_fore_color');
          
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
