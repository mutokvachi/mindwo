<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Rights;
use Illuminate\Support\Facades\File;
use DB;
use App\Exceptions;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use App\Libraries\FieldsImport;
use App\Libraries\DBHistory;
use Log;

/**
 * Provides functionality for data importing from Excel files to any register
 */
class ImportController extends Controller
{

    /**
     * Filed name in HTML form for file input
     */
    const FILE_FIELD_NAME = "import_file";

    /**
     * Supported field types. ID's from table dx_field_types
     * @var type 
     */
    private $supported_fields = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17, 18];

    /**
     * Register ID
     * 
     * @var integer
     */
    private $list_id = 0;

    /**
     * Count of imported (inserted) rows
     * 
     * @var integer 
     */
    private $import_count = 0;
    
    /**
     * Count of updated rows
     * @var integer 
     */
    private $update_count = 0;

    /**
     * Array with register fields 
     * 
     * @var array 
     */
    private $list_fields = null;

    /**
     * DB table object for register (dx_objects row)
     * 
     * @var object 
     */
    private $list_object = null;

    /**
     * Array for Excel row values saving into db
     * 
     * @var array 
     */
    private $save_arr = [];

    /**
     * Current time for data audit info
     * 
     * @var DateTime 
     */
    private $time_now = null;

    /**
     * Array with Excel collumns not matched
     * 
     * @var array 
     */
    private $arr_not_match = [];
    
    /**
     * String with skiped Excel rows numbers delimited by coma - because of duplicate records
     * 
     * @var string
     */
    private $duplicate = "";

    /**
     * String with skiped Excel rows numbers delimited by coma - because of missing parent records (from the same register)
     * @var string 
     */
    private $dependency = "";
    
    /**
     * Excel rows counter
     * @var integer 
     */
    private $row_nr = 0;
    
    /**
     * Array for holding rows which depends on the same Excel data.
     * For example, employee depends on manager - manager must be imported first.
     * 
     * @var array 
     */
    private $dep_arr = [];
    
    /**
     * File importing object
     * 
     * @var object
     */
    private $file_import = null;
    
    /**
     * Imports uploaded file data into database
     * 
     * @param \Illuminate\Http\Request $request
     * @return type
     */
    public function importData(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id'
        ]);

        $this->list_id = $request->input('list_id');

        //check rights on the list
        \App\Libraries\Helper::checkSaveRights($this->list_id);
        
        //validate file
        if (!$request->hasFile(self::FILE_FIELD_NAME)) {
            throw new Exceptions\DXCustomException(trans("errors.import_file_not_provided"));
        }
        
        // prepared uploaded data (unzips or converts to CSV if needed)
        $this->file_import = \App\Libraries\FilesImport\FileImportFactory::build_file($request->file(self::FILE_FIELD_NAME));              
        
        //Load list fields
        $this->getListFields();

        //Set db table for list
        $this->list_object = \App\Libraries\DBHelper::getListObject($this->list_id);
        $this->list_object->table_name = $this->list_object->db_name; // in order to work history logic for updates
        
        //Sets current time for audit info
        $this->time_now = date('Y-n-d H:i:s');                
               
        //Import data from CSV file
        $this->importCSVData($this->file_import->tmp_dir . DIRECTORY_SEPARATOR . $this->file_import->csv_file);

        $this->cleanFiles();
        
        return response()->json([
            'success' => 1, 
            'imported_count' => $this->import_count, 
            'not_match' => $this->arr_not_match, 
            'duplicate' => $this->duplicate,
            'dependency' => $this->dependency,
            'updated_count' => $this->update_count
        ]);
    }
    
    /**
     * Delete tmp folder and tmp files
     */
    private function cleanFiles() {
        
        File::deleteDirectory($this->file_import->tmp_dir);
        
        $files = File::allFiles(storage_path() . DIRECTORY_SEPARATOR . 'exports');
        foreach ($files as $file)
        {
            if ($file != '.gitignore') {
                File::delete($file);
            }
        }
    }

    /**
     * Import data from CSV into database 
     * 
     * @param string $file_path Full path to temporary uploaded CSV file
     */
    private function importCSVData($file_path)
    {        
        Excel::load($file_path, function($reader)
        {
            // Loop through all rows
            $reader->each(function($row)
            {
                $this->row_nr++;
                $this->processRow($row, $this->row_nr);    
            });
        });
        
        $this->importDependentData();
    }
    
    /**
     * Maps Excel colums with register fields and inserts data in db
     * @param object $row JSON array with Excel data
     * @param integer $row_nr Excel row counter
     */
    private function processRow($row, $row_nr) {
        $this->clearSaveArray();
                
        try {
            $row->each(function($val, $title)
            {
                $fld = $this->getFieldObj($title);
                $this->prepareValue($fld, $val);
            });
        }
        catch (Exceptions\DXImportLookupException $e) {
            if (!isset($this->dep_arr[$row_nr])) {
                $this->dep_arr[$row_nr] = $row; // hold, will try to import later again 
            }
            $this->clearSaveArray();
        }

        if (count($this->save_arr) > 0) {
            $this->saveData();                   
        }    
    }
    
    /**
     * Imports recursively dependent rows
     */
    private function importDependentData() {        
        if (count($this->dep_arr) == 0 ) {
            return;
        }
        
        $dep_rows = "";
        $ok_count = 0;
        foreach($this->dep_arr as $key => $row) {
            
            $this->processRow($row, $key);
            
            if (count($this->save_arr) > 0) {                
                unset($this->dep_arr[$key]);
                $ok_count++;
            }
            else {
                if (strlen($dep_rows) > 0) {
                    $dep_rows .= ", ";
                }
                $dep_rows .= ($key + 1);
            }
        }
        
        if ($ok_count == 0 && count($this->dep_arr) > 0) {
            $this->dependency = $dep_rows;
            return; // will skip rows, because dont have related records (from the same register)                        
        }
        
        return $this->importDependentData();
    }

    /**
     * Save parsed row in database
     */
    private function saveData() {
        $this->addHistory();

        DB::transaction(function ()
        {
            try {
                
                if (isset($this->save_arr["id"]) && $this->save_arr["id"] > 0) {
                    $id = $this->save_arr["id"];
                    $data_row = DB::table($this->list_object->db_name)->where('id', '=', $id)->first();
                    if ($data_row) {
                        // update by ID field
                        unset($this->save_arr["id"]);
                        
                        $arr_data = [];
                        foreach($this->save_arr as $key => $val) {
                            $arr_data[":" . $key] = $val;
                        }
                        
                        $history = new DBHistory($this->list_object, $this->list_fields, $arr_data, $id);
                        $history->makeUpdateHistory();
                        
                        if ($history->is_update_change) {
                            DB::table($this->list_object->db_name)->where('id', '=', $id)->update($this->save_arr);
                            $this->update_count++;
                        }
                    }
                    else {
                        // insert with provided ID
                        DB::table($this->list_object->db_name)->insert($this->save_arr);
                        $history = new DBHistory($this->list_object, null, null, $id);
                        $history->makeInsertHistory();
                        $this->import_count++;
                    }
                }
                else {
                    // insert without ID field
                    $id = DB::table($this->list_object->db_name)->insertGetId($this->save_arr);
                    $history = new DBHistory($this->list_object, null, null, $id);
                    $history->makeInsertHistory();
                    $this->import_count++;
                }                
            }
            catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    // skip record
                    if (strlen($this->duplicate) > 0) {
                        $this->duplicate .= ", ";
                    }
                    $this->duplicate .= ($this->row_nr+1);                            

                }
                else {
                    throw $e;
                }
            }
        }); 
    }
    
    /**
     * Clears saving array for the next row
     * Adds last modify info if needed
     */
    private function clearSaveArray()
    {
        unset($this->save_arr); // break references
        $this->save_arr = array(); // re-initialize to empty array
    }

    /**
     * Adds history audit info to saving array
     */
    private function addHistory()
    {
        if ($this->list_object->is_history_logic) {
            $this->save_arr["created_user_id"] = Auth::user()->id;
            $this->save_arr["created_time"] = $this->time_now;
            $this->save_arr["modified_user_id"] = Auth::user()->id;
            $this->save_arr["modified_time"] = $this->time_now;
        }
    }

    /**
     * Prepares Excel cell value for inserting
     * 
     * @param object $fld Field object
     * @param mixed $val Cell value from Excel
     * @return void
     */
    private function prepareValue($fld, $val)
    {
        if (!$fld) {
            return;
        }

        $fld_save = FieldsImport\FieldImportFactory::build_field($val, $fld, $this->file_import->tmp_dir);

        $this->save_arr = array_merge($this->save_arr, $fld_save->getVal());
    }

    /**
     * Load register's fields into array
     */
    private function getListFields()
    {
        $this->list_fields = DB::table('dx_lists_fields as lf')
                ->select(
                        'lf.list_id',
                        'lf.db_name', 
                        'ft.sys_name as type_sys_name', 
                        'lf.max_lenght', 
                        'lf.is_required', 
                        'lf.default_value', 
                        'lf.title_form', 
                        'lf.title_list',
                        'lf.rel_list_id',
                        'lf_rel.db_name as rel_field_name',
                        'o_rel.db_name as rel_table_name',
                        'o_rel.is_history_logic as rel_table_is_history_logic',
                        'lf.is_public_file',
                        'lf.id as field_id'
                        )
                ->leftJoin('dx_field_types as ft', 'lf.type_id', '=', 'ft.id')
                ->leftJoin('dx_lists_fields as lf_rel', 'lf.rel_display_field_id', '=', 'lf_rel.id')
                ->leftJoin('dx_lists as l_rel', 'lf.rel_list_id', '=', 'l_rel.id')
                ->leftJoin('dx_objects as o_rel', 'l_rel.object_id', '=', 'o_rel.id')
                ->where('lf.list_id', '=', $this->list_id)
                ->whereIn('lf.type_id', $this->supported_fields)
                ->get();

        $this->addFormatedTitles();
    }

    /**
     * Finds register field by field title
     * 
     * @param string $title Field title
     * @return mixed Null if noting found or field object
     */
    private function getFieldObj($title)
    {

        foreach ($this->list_fields as $field) {

            if ($field->title_list_f == $title || $field->title_form_f == $title) {
                return $field;
            }
        }

        $this->addNotMatch($title);
        return null;
    }

    /**
     * Adds not matched Excel column to the array
     * @param type $title
     */
    private function addNotMatch($title)
    {

        foreach ($this->arr_not_match as $item) {
            if ($item == $title) {
                return;
            }
        }

        array_push($this->arr_not_match, $title);
    }

    /**
     * Creates new attributies to fields array - formated titles (replaced utf8 and special chars)
     * Because Excel object renames collumns so that removes utf-8 and special symbols
     */
    private function addFormatedTitles()
    {
        foreach ($this->list_fields as $field) {
            $field->title_list_f = $this->formatFieldTitle($field->title_list);
            $field->title_form_f = $this->formatFieldTitle($field->title_form);
        }
    }

    /**
     * Formats register field title according to Excel formating
     * @param string $title Register field title
     * @return string Formated title
     */
    private function formatFieldTitle($title)
    {
        $val = mb_strtolower($title);
        $val = $this->toASCII($val);
        $val = trim($val);
        $val = str_replace(" ", "_", $val);

        return $val;
    }

    /**
     * Converts unicode string to asci string - replaces special characters with latin characters
     * @param string $str Unicode string
     * @return string ASCII string
     */
    private function toASCII($str)
    {
        $arr = [
            'ā' => 'a',
            'č' => 'c',
            'ē' => 'e',
            'ģ' => 'g',
            'ī' => 'i',
            'ķ' => 'k',
            'ļ' => 'l',
            'ņ' => 'n',
            'š' => 's',
            'ū' => 'u',
            'ž' => 'z',
            '  ' => ' ',
            '#' => '',
            '.' => '',
            '-' => '_',
        ];

        return strtr($str, $arr);
    }

    /**
     * Validates if user have rights to insert new items in the register
     * 
     * @param integer $list_id Register id
     * @throws Exceptions\DXCustomException
     */
    private function checkSaveRights()
    {
        $right = Rights::getRightsOnList($this->list_id);

        if ($right == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
        else {
            if ($right->is_new_rights == 0) {
                throw new Exceptions\DXCustomException(trans('errors.no_rights_to_insert'));
            }
        }
    }
}
