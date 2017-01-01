<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use App\Exceptions;
use App\Libraries\Rights;
use Config;
use Log;

/**
 * Datņu lejuplādēšanas kontrolieris
 */
class FileController extends Controller
{    
    /**
     * Lejuplādē datni
     * 
     * @param integer $item_id          Ieraksta ID
     * @param integer $list_id          Reģistra ID
     * @param integer $file_field_id    Datnes lauka ID reģistrā
     * @return Response Datne
     * @throws Exceptions\DXCustomException
     */
    public function getFile($item_id, $list_id, $file_field_id)
    {
        Rights::checkFileRights($item_id, $list_id);
        
        $file = $this->getFileData($item_id, $list_id, $file_field_id);
        
        if (!$file) {
            throw new Exceptions\DXCustomException(sprintf(trans('errors.file_record_not_found'),$item_id));
        }

        $file_folder = $this->getFileFolder($file->is_public_file);

        $file_path = $file_folder . $file->file_guid;

        if (!file_exists($file_path)) {
            throw new Exceptions\DXCustomException(sprintf(trans('errors.file_not_found'), $file_path));
        }
        /*
        // This is Laravel approach - but not working with cookie..        
        $headers = array(
            'Content-Type: ' . $this->getFileContentHeader($file->file_name),
            'Content-Disposition: attachment; filename="' . $file->file_name . '";'
        );                
        return response()->download($file_path, $file->file_name, $headers);
        */
        
        header('Content-Description: File Transfer');        
        header('Content-Type: ' . $this->getFileContentHeader($file->file_name));
        header('Content-Disposition: attachment; filename="'.$file->file_name.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        header('Set-Cookie: fileDownload=true; path=/');
        readfile($file_path);
    }
    
    /**
     * Download file from AJAX request
     * 
     * @param ineger $item_id Item ID
     * @param integer $list_id List ID
     * @param integer $file_field_id Field ID
     * @return Response File download
     */
    public function getFile_js($item_id, $list_id, $file_field_id) {
        try {
            $this->getFile($item_id, $list_id, $file_field_id);
        } catch (\Exception $ex) {
            return response(json_encode(['success' => 0, 'error' => $ex->getMessage()]));
        }
    }

    /**
     * Lejuplādē datni pēc norādītā datnes lauka nosaukuma
     * 
     * @param integer $item_id          Ieraksta ID
     * @param integer $list_id          Reģistra ID
     * @param string $field_name        Datnes lauka nosaukums
     * @return Response Datne
     * @throws Exceptions\DXCustomException
     */
    public function getFileByField($item_id, $list_id, $field_name)
    {
        Rights::checkFileRights($item_id, $list_id);
        
        $file_field_id = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', $field_name)->first()->id;

        $file = $this->getFileData($item_id, $list_id, $file_field_id);

        if (!$file) {
            throw new Exceptions\DXCustomException(sprintf(trans('errors.file_record_not_found'),$item_id));
        }
        
        return $this->performFileDownload($file);
    }
    
     /**
     * Lejuplādē PDF datni pēc norādītā datnes lauka nosaukuma
     * 
     * @param integer $item_id          Ieraksta ID
     * @param integer $list_id          Reģistra ID
     * @param integer $field_id          Datnes lauka ID
     * @return Response Datne
     * @throws Exceptions\DXCustomException
     */
    public function getPDFFile($item_id, $list_id, $field_id)
    {
        Rights::checkFileRights($item_id, $list_id);
        
        $file = $this->getFileData($item_id, $list_id, $field_id);

        if (!$file) {
            throw new Exceptions\DXCustomException(sprintf(trans('errors.file_record_not_found'),$item_id));
        }
        
        return $this->performFileDownload($file);
    }
    
    /**
     * Downloads first (according to order index in form) available file from register item
     * 
     * @param integer $item_id Item ID
     * @param integer $list_id List ID
     * @return type Response with file
     * @throws Exceptions\DXCustomException
     */
    public function getFirstFile($item_id, $list_id) {
        
        Rights::checkFileRights($item_id, $list_id);
        
        $file_fields = DB::table('dx_lists_fields as lf')
                       ->select('lf.id')
                       ->join('dx_forms_fields as ff', 'lf.id', '=', 'ff.field_id')
                       ->where('lf.list_id', '=', $list_id)
                       ->where('lf.type_id', '=', \App\Libraries\DBHelper::FIELD_TYPE_FILE)
                       ->orderBy('ff.order_index')
                       ->get();
        
        $file = null;
        foreach($file_fields as $filefield) {
            $file = $this->getFileData($item_id, $list_id, $filefield->id);

            if ($file && $file->file_name) {
                break;
            }
        }
        
        if (!$file) {
            // no file set for list item
            throw new Exceptions\DXCustomException(trans('errors.file_not_set'));
        }
        
        return $this->performFileDownload($file);
    }

    /**
     * Izgūst datnes meta informāciju no datu bāzes
     * 
     * @param integer $item_id          Ieraksta ID
     * @param integer $list_id          Reģistra ID
     * @param integer $file_field_id    Datnes lauka ID reģistrā
     * @return Object Datnes datu bāzes ieraksta rinda
     * @throws Exceptions\DXCustomException
     */
    private function getFileData($item_id, $list_id, $file_field_id)
    {
        $fields_row = $this->getFieldsRow($list_id, $file_field_id);

        $file_row = $this->getFileDataRows($fields_row, $list_id, $item_id);        

        return $file_row;
    }

    /**
     * Izgūst datnes katalogu
     * Datnes var glabāties publiski pieejamā katalogā (no interneta pārlūka) vai arī katalogā, kuram nevar piekļūt no interneta pārlūka
     * 
     * @param integer $is_public Pazīme, vai katalogs ir publisks (1 - publisks, 0 - privāts)
     * @return string Kataloga nosaukums
     * @throws Exceptions\DXCustomException
     */
    public static function getFileFolder($is_public)
    {
        $file_folder = "";
        if ($is_public == 1) {
            // Images can be stored in public access folder
            $file_folder = public_path() . Config::get('assets.public_img_path', '');
        }
        else {
            // Documents are stored in non-public access folder            
            $file_folder = storage_path() . Config::get('assets.private_file_path', '');
        }

        if (!file_exists($file_folder)) {
            throw new Exceptions\DXCustomException("Sistēmas iestatījumos norādītais datņu katalogs '" . $file_folder . "' neeksistē! Lūdzu, sazinieties ar sistēmas uzturētāju!");
        }

        return $file_folder;
    }

    /**
     * Finds file from server disk and downloads it
     * @param object $file File row
     * @return Response File download
     * @throws Exceptions\DXCustomException
     */
    private function performFileDownload($file) {
        $file_folder = $this->getFileFolder($file->is_public_file);

        $file_path = $file_folder . $file->file_guid;

        if (!file_exists($file_path)) {
            throw new Exceptions\DXCustomException(sprintf(trans('errors.file_not_found'), $file_path));
        }

        $headers = array(
            'Expires: 0',
            'Cache-Control: must-revalidate',
            'Content-Type: ' . $this->getFileContentHeader($file->file_name),
            'Content-Disposition: filename="' . $file->file_name . '";'
        );
        
        return response()->download($file_path, $file->file_name, $headers);
    }
    /**
     * Izgūst datnes reģistra lauku rindas objektu
     * 
     * @param integer $list_id          Reģistra ID (tabula dx_lists)
     * @param integer $file_field_id    Datnes lauks ID (tabula dx_lists_fields)
     * @return Object                   Datnes lauku rindas objekts
     * @throws Exceptions\DXCustomException
     */
    private function getFieldsRow($list_id, $file_field_id)
    {
        $sql = "
        SELECT
                o.db_name as table_name,
                lf.db_name as file_field_name,
                o.is_multi_registers,
                lf.is_public_file
        FROM
                dx_lists l
                inner join dx_objects o on l.object_id = o.id
                inner join dx_lists_fields lf on lf.id = :file_field_id
        WHERE
                l.id = :list_id
        ";

        $fields = DB::select($sql, array('list_id' => $list_id, 'file_field_id' => $file_field_id));

        if (count($fields) == 0) {
            throw new Exceptions\DXCustomException("Nekorekts datnes reģistra lauka ID (" . $file_field_id . ")! Lūdzu, sazinieties ar sistēmas uzturētāju!");
        }

        return $fields[0];
    }

    /**
     * Izgūst datnes ierakstus pēc norādītā ieraksta ID
     * 
     * @param Object $fields_row    Datnes lauku rindas objekts
     * @param integer $list_id      Reģistra ID (tabula dx_lists)
     * @param type $item_id         Ieraksta ID
     * @return Array Masīvs ar datnes ierakstu (masīvā tiek ekspektēts 1 ieraksts)
     */
    private function getFileDataRows($fields_row, $list_id, $item_id)
    {       
        $data_tb = DB::table($fields_row->table_name)
                ->select('id', DB::raw($fields_row->file_field_name . " as file_name"), DB::raw(str_replace("_name", "_guid", $fields_row->file_field_name) . " as file_guid"), DB::raw($fields_row->is_public_file . " as is_public_file")
                )
                ->where('id', '=', $item_id);

        if ($fields_row->is_multi_registers == 1) {
            $data_tb->where('multi_list_id', '=', 'list_id');
        }

        return $data_tb->first();
    }

    /**
     * Nosaka datns satura tipu pēc datnes paplašinājuma
     * 
     * @param string $file_name Datnes nosaukums
     * @return string Datnes satura tips
     */
    private function getFileContentHeader($file_name)
    {
        $parts = explode(".", $file_name);
        $extention = strtolower(end($parts));

        $header_row = DB::table('dx_files_headers')->where('extention', '=', $extention)->first();

        $content_type = 'application/force-download'; // noklusētais satura tips

        if ($header_row) {
            $content_type = $header_row->content_type;
        }

        return $content_type;
    }

}
