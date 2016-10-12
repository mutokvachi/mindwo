<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTabsConfigToDxForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('dx_forms', function (Blueprint $table) {
            $table->boolean('is_vertical_tabs')->default(0)->nullable()->comment = "Ir vertikālās sadaļas";
        });
        
        $list = App\Libraries\DBHelper::getListByTable('dx_forms');
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'is_vertical_tabs',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
            'title_list' => 'Ir vertikālās sadaļas',
            'title_form' => 'Ir vertikālās sadaļas',
            'default_value' => 0,
            'hint' => 'Pazīme norāda, vai formā pievienotās sadaļas attēlot vertikālā vai horizontālā novietojumā. Pēc noklusēšanas ir horizontāls novietojums',
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
        App\Libraries\DBHelper::dropField('dx_forms', 'is_vertical_tabs');
    }
}
