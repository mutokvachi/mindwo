<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomTabsToForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // izveido db lauku
        Schema::table('dx_forms_tabs', function (Blueprint $table) {
            $table->boolean('is_custom_data')->default(0)->nullable()->comment = "Ir datu lauki";
        });
        
        // pievieno lauku CMSā
        $list = App\Libraries\DBHelper::getListByTable('dx_forms_tabs');
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'is_custom_data',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
            'title_list' => 'Ir datu lauki',
            'title_form' => 'Ir datu lauki',
            'default_value' => 0,
            'hint' => 'Pazīme norāda, vai sadaļā tiks attēloti tā paša reģistra datu lauki vai arī cits saistītais reģistrs. Pēc noklusēšanas būs cits saistītais reģistrs.',
        ]);
        
        App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id);
        
        // papildus update - noņemam obligātumu dažiem laukiem
        DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', 'grid_list_id')->update(['is_required' => 0]);
        DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', 'grid_list_field_id')->update(['is_required' => 0]);
        
        // pievienojam CMSā db jau eksistējosu lauku bet kas nebija pievienots
        $list = App\Libraries\DBHelper::getListByTable('dx_forms_fields');
        
        $list_rel = App\Libraries\DBHelper::getListByTable('dx_forms_tabs');
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'tab_id',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
            'title_list' => 'Sadaļa',
            'title_form' => 'Sadaļa',
            'hint' => 'Norāda, kurā sadaļā lauku attēlot. Pēc noklusēšanas attēlo formas galvenajā laukumā.',
            'rel_list_id' => $list_rel->id,
            'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_rel->id)->where('db_name', '=', 'title')->first()->id,
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
        // atliekam atpakaļ obligātumu
        $list = App\Libraries\DBHelper::getListByTable('dx_forms_tabs');
        
        DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', 'grid_list_id')->update(['is_required' => 1]);
        DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', 'grid_list_field_id')->update(['is_required' => 1]);
        
        // dzešam lauku no db un arī cms
        App\Libraries\DBHelper::dropField('dx_forms_tabs', 'is_custom_data');
        
        // dzešama tikai no cms
        App\Libraries\DBHelper::removeFieldCMS('dx_forms_tabs', 'tab_id');
    }
}
