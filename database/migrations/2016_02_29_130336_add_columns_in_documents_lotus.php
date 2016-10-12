<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInDocumentsLotus extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_documents_lotus', function (Blueprint $table) {
            $table->integer('source_id')
                            ->nullable()
                            ->unsigned()
                            ->index()
                            ->foreign()->references('id')->on('in_sources')->onDelete('cascade')
                    ->comment = "Nodaļa";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_documents_lotus', function (Blueprint $table) {
            $table->dropForeign(['source_id']);   
            $table->dropColumn(['source_id']);
        });
    }
}