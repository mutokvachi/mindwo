<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCustomJs extends Migration
{
    // Array with JS scripts to update
    private $arr_upd = [
        ['js_id' => 21, 'file_name' => '2016_11_01_criteria_field.js'],
        ['js_id' => 2, 'file_name' => '2016_11_01_operation_field.js'],
        ['js_id' => 3, 'file_name' => '2016_11_01_view_form.js'],
        ['js_id' => 5, 'file_name' => '2016_11_01_wf_step.js'],
        ['js_id' => 6, 'file_name' => '2016_11_01_form.js'],
        ['js_id' => 11, 'file_name' => '2016_11_01_configuration.js'],
    ];
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {       
        
        $dir = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "updates" . DIRECTORY_SEPARATOR;
        $tmp_dir = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR;
        
        foreach($this->arr_upd as $upd) {
            // save backup
            $tmp_file = $tmp_dir . $upd['file_name'];
            $content_row = DB::table('dx_forms_js')->where('id', '=', $upd["js_id"])->first();
            
            if (!$content_row) {
                continue;
            }
            
            $content = $content_row->js_code;
            $bytes_written = File::put($tmp_file, $content);
            if ($bytes_written === false)
            {
                throw new Exception("Can't write to file " . $tmp_file);
            }
            
            // update with new value
            $file_js = $dir . $upd['file_name'];
            $content = File::get($file_js);
            
            DB::table('dx_forms_js')->where('id', '=', $upd["js_id"])->update(['js_code' => $content]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tmp_dir = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR;
        
        foreach($this->arr_upd as $upd) {          
            
            $content_row = DB::table('dx_forms_js')->where('id', '=', $upd["js_id"])->first();
            
            if (!$content_row) {
                continue;
            }
            
            // update with backup value
            $file_js = $tmp_dir . $upd['file_name'];
            $content = File::get($file_js);
            
            DB::table('dx_forms_js')->where('id', '=', $upd["js_id"])->update(['js_code' => $content]);
        }
    }
}
