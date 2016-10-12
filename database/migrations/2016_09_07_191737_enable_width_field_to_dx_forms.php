<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnableWidthFieldToDxForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // pievienojam CMSā db jau eksistējosu lauku bet kas nebija pievienots UI
        $list = App\Libraries\DBHelper::getListByTable('dx_forms');
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'width',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_INT,
            'title_list' => 'Platums, %',
            'title_form' => 'Platums, %',
            'hint' => 'Ja 0, tad noklusētais platums. Citādi norāda % no ekrāna platuma.',
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
        // dzešama tikai no cms
        App\Libraries\DBHelper::removeFieldCMS('dx_forms', 'width');
    }
}
