<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

/**
 * Creates table fro user profile's comment tab
 */
class CreateUserProfileNotes extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_employees_notes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('note', 2000)->nullable()->comment = "Note";
            $table->boolean('is_hr')->nullable()->comment = "Is HR note";

            $table->integer('user_id')->nullable()->comment = "User";
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');

            $table->datetime('created_time')->nullable();
            $table->integer('created_user_id')->nullable()->comment = "User who created";
            $table->foreign('created_user_id')->references('id')->on('dx_users')->onDelete('cascade');

            $table->datetime('modified_time')->nullable();
            $table->integer('modified_user_id')->nullable()->comment = "User who last modified";
            $table->foreign('modified_user_id')->references('id')->on('dx_users')->onDelete('cascade');
        });

        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'in_employees_notes', 'title' => 'User notes', 'is_history_logic' => 1]);

        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "User notes";
        $list_gen->form_title = "User note";
        $list_gen->doMethod();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('in_employees_notes');

        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod();

        Schema::dropIfExists('in_employees_notes');
    }
}
