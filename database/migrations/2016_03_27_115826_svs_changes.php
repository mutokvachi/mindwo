<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SvsChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        $file_js = storage_path() . "\\app\\updates\\2016_03_28_alternate_url.js";
        
        $content = File::get($file_js);
        DB::table('dx_forms_js')->where('id', '=', 14)->update(['js_code' => $content]);        
        
        $this->createFeedField(189);
        $this->createFeedField(208);
        
        Schema::table('in_publish', function (Blueprint $table) {
            $table->string('nr', 144)->change();
        });
        
        DB::table('dx_lists_fields')->where('id', '=', 1313)->update(['max_lenght'=>144]);
        
        DB::table('dx_views_fields')->whereIn('field_id', [1421, 1291, 1178, 546])->delete();
        
        DB::table('dx_views_fields')->where('id', '=', 1036)->update(['is_hidden' => 1]);
        
        DB::table('dx_views_fields')->where('id', '=', 1040)->update(['order_index' => 25, 'sort_type_id' => 1]);
        DB::table('dx_views_fields')->where('id', '=', 1038)->update(['sort_type_id' => 1]);
        
        DB::table('dx_lists_fields')->where('id', '=', 1390)->update(['type_id' => 3, 'rel_list_id' => 119, 'rel_display_field_id' => 726]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->undoFeedField(189);
        $this->undoFeedField(208);
        
        Schema::table('in_publish', function (Blueprint $table) {
            $table->string('nr', 50)->change();
        });
        
        DB::table('dx_lists_fields')->where('id', '=', 1313)->update(['max_lenght'=>50]);
        
        DB::table('dx_lists_fields')->where('id', '=', 1390)->update(['type_id' => 5, 'rel_list_id' => null, 'rel_display_field_id' => null]);
        
    }
    
    private function createFeedField($list_id){
        $fld_id = DB::table('dx_lists_fields')->insertGetId(
            ['list_id' => $list_id, 'db_name' => 'is_static', 'type_id' => 7, 'title_list' => 'Nerādīt ziņu plūsmā', 'title_form' => 'Nerādīt ziņu plūsmā', 'hint' => 'Norādot vērtību "Jā", ziņa netiks rādīta ziņu plūsmā, bet to varēs atrast ar meklētāju. Šo lauku izmanto, lai veidotu, piemēram, statiskus rakstus, kas tiek iekļauti kā bloki portāla lapās.']
        );
        
        $form_row = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
        
        DB::table('dx_forms_fields')->insert(['list_id'=>$list_id, 'form_id'=>$form_row->id, 'field_id'=>$fld_id,'order_index'=>2]);
    }
    
    private function undoFeedField($list_id) {
        $form_row = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
        $fld_row = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'is_static')->first();
        
        DB::table('dx_forms_fields')->where('list_id', '=', $list_id)->where('form_id', '=', $form_row->id)->where('field_id', '=', $fld_row->id)->delete();
        DB::table('dx_lists_fields')->where('id', '=', $fld_row->id)->delete();
    }
}
