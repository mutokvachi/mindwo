<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupTitleToDxFormsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // izveido db lauku
        Schema::table('dx_forms_fields', function (Blueprint $table) {
            $table->string('group_label', 100)->nullable()->comment = "Lauku grupas virsraksts";
        });
        
        // pievieno lauku CMSā
        $list = App\Libraries\DBHelper::getListByTable('dx_forms_fields');
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'group_label',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => 'Lauku grupas virsraksts',
            'title_form' => 'Lauku grupas virsraksts',
            'max_lenght' => 100,
            'hint' => 'Ja norādīts, tad virs lauka tiek ielikts virsraksts un zem tā pelēka līnija, tādā veidā iesākot lauku grupu.',
        ]);
        
        App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         // dzešam lauku no db un arī cms
        App\Libraries\DBHelper::dropField('dx_forms_fields', 'group_label');
    }
}
