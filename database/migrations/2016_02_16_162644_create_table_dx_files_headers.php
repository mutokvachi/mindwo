<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDxFilesHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_files_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('extention', 5)->nullable()->comment = "Paplašinājums";
            $table->string('content_type', 500)->nullable()->comment = "Satura tips";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->unique('extention');
        });
        
        DB::table('dx_files_headers')->insert([
            ['extention' => 'xls', 'content_type' => 'application/vnd.ms-excel'],
            ['extention' => 'xlsx', 'content_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            ['extention' => 'pdf', 'content_type' => 'application/pdf'],
            ['extention' => 'docx', 'content_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            ['extention' => 'doc', 'content_type' => 'application/msword'],
            ['extention' => 'odt', 'content_type' => 'application/vnd.oasis.opendocument.text'],
            ['extention' => 'jpg', 'content_type' => 'image/jpeg'],
            ['extention' => 'png', 'content_type' => 'image/png'],
            ['extention' => 'gif', 'content_type' => 'image/gif'],
            ['extention' => 'txt', 'content_type' => 'text/plain']
        ]);        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_files_headers');
    }
}
