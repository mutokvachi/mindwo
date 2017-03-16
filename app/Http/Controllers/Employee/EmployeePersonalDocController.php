<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Libraries\Rights;

/**
 * Employee's personal documents controller
 */
class EmployeePersonalDocController extends Controller
{

    /**
     * Path where all docuemtns are stored
     * @var type 
     */
    protected $document_path;

    /**
     * Parameter if user has required rights to access control
     * @var boolean 
     */
    public $has_access;

    /**
     * Constructs employee documents class. Sets needed parameters
     */
    public function __construct()
    {
        $this->document_path = storage_path(config('assets.private_file_path'));

        $this->getAccess();
    }

    /**
     * Get rights and check if user has access to employees documents tab
     */
    private function getAccess()
    {
        $list = \App\Libraries\DBHelper::getListByTable('in_employees_personal_docs');

        if (!$list) {
            $this->has_access = false;
            return;
        }

        $list_rights = Rights::getRightsOnList($list->id);

        if ($list_rights && $list_rights->is_edit_rights && $list_rights->is_edit_rights == 1) {
            $this->has_access = true;
        } else {
            $this->has_access = false;
        }
    }

    /**
     * Validate if user has access to employee document tab. If not then request is aborted.
     */
    private function validateAccess()
    {
        if (!$this->has_access) {
            abort(403, trans('errors.no_rights_on_register'));
        }
    }

    /**
     * Test view fors testing purposes
     * @return \Illuminate\View\View Created view
     */
    public function testView()
    {
        $this->validateAccess();

        return view('pages.employees_doc_test');
    }

    /**
     * Search personal documents types which are assigned to specified country 
     * @param integer $country_id Country ID
     * @return App\Employee\PersonalDocument Collection of personal documents types
     */
    public function getPersonalDocsByCountry($country_id)
    {
        $this->validateAccess();

        $country = \App\Models\Country::find($country_id);

        return json_encode($country->personalDocs()->get());
    }

    /**
     * Gets documents and document's data which are registeres to specified user
     * @param Integer $user_id User ID
     * @return App\Employee\EmployeePersonalDocument Collection of personal documents and information about them
     */
    public function getEmployeeDocs($user_id)
    {
        $this->validateAccess();

        $user = \App\User::find($user_id);

        if (!$user) {
            return null;
        }

        $docs = $user->employeePersonalDocs()->with('personalDocument')->get();

        foreach ($docs as $doc) {
            if ($doc->valid_to) {
                $date = date_format(new \DateTime($doc->valid_to), config('dx.txt_date_format'));
                $doc->valid_to = $date;
            }
        }

        return json_encode($docs);
    }

    /**
     * Saves data
     * @param Request $request Data request
     * @return string Result
     */
    public function save(Request $request)
    {
        $this->validateAccess();

        $data = json_decode($request->input('data'), true);

        $user_id = $data['user_id'];
        $input_rows = $data['rows'];

        $user = \App\User::find($user_id);

        if (!$user) {
            return '0';
        }

        $country = \App\Models\Country::find($request->input('doc_country_id'));
        if (!$country) {
            return '0';
        }

        $user->doc_country_id = $country->id;
        $user->save();

        $existing_saved_emp_doc_ids = [];

        foreach ($input_rows as $input_row) {
            if ($input_row['id'] && $input_row['id'] > 0) {
                $existing_saved_emp_doc_ids[] = $input_row['id'];
            }
        }

        // Delete old rows which are removed
        \App\Models\Employee\EmployeePersonalDocument::whereNotIn('id', $existing_saved_emp_doc_ids)->delete();

        $result = [];

        // Update existing and insert new
        for ($counter = 0; $counter < count($input_rows); $counter++) {
            $input_row = $input_rows[$counter];

            if ($input_row['id'] && $input_row['id'] > 0) {
                $emp_pers_doc = \App\Models\Employee\EmployeePersonalDocument::find($input_row['id']);
            } else {
                $emp_pers_doc = new \App\Models\Employee\EmployeePersonalDocument();
            }

            $emp_pers_doc = $this->saveEmpPersDoc($request, $counter, $user->id, $emp_pers_doc, $input_row);

            $result[] = [
                'id' => $emp_pers_doc->id,
                'doc_id' => $emp_pers_doc->doc_id,
                'file_name' => $emp_pers_doc->file_name
            ];
        }

        return json_encode($result);
    }

    /**
     * Deletes document from server
     * @param string $file_guid File name
     */
    private function deleteOldDocument($file_guid)
    {
        $file_path = $this->document_path . DIRECTORY_SEPARATOR . $file_guid;

        if ($file_guid && file_exists($file_path)) {
            unlink($file_path);
        }
    }

    /**
     * Saves employee's document models data 
     * @param Request $request Data rquest
     * @param integer $counter Counter which counts which is this document in current stack of documents which are being saved
     * @param integer $user_id User ID to which document is saved
     * @param \App\Models\Employee\EmployeePersonalDocument $emp_pers_doc Model which is being edited and saved
     * @param array $input_row Data New data which will be set and saved
     * @return \App\Models\Employee\EmployeePersonalDocument Saved model
     */
    private function saveEmpPersDoc(Request $request, $counter, $user_id, $emp_pers_doc, $input_row)
    {
        $file = $request->file('file' . $counter);

        if ($file) {
            // Deletes file from directory
            $this->deleteOldDocument($emp_pers_doc->file_guid);

            $file_guid = $this->saveFile($file);
            $emp_pers_doc->file_name = $file->getClientOriginalName();
            $emp_pers_doc->file_guid = $file_guid;
        } elseif ($input_row['file_remove']) {
            // Deletes file from directory
            $this->deleteOldDocument($emp_pers_doc->file_guid);

            $emp_pers_doc->file_name = null;
            $emp_pers_doc->file_guid = null;
        }

        $emp_pers_doc->user_id = $user_id;
        $emp_pers_doc->doc_id = (int) $input_row['document_type'];
        $emp_pers_doc->publisher = $input_row['publisher'];
        $emp_pers_doc->doc_nr = $input_row['doc_nr'];

        $valid_to = check_date($input_row['valid_to'], config('dx.date_format'));

        if (strlen($valid_to) == 0) {
            $valid_to = null;
        }

        $emp_pers_doc->valid_to = $valid_to;

        $emp_pers_doc->save();

        return $emp_pers_doc;
    }

    /**
     * Saves file on server's file system
     * @param \Illuminate\Http\UploadedFile $file Uploaded file
     * @return string Generated unique file name
     */
    private function saveFile($file)
    {
        $file_path = tempnam($this->document_path, 'emp');

        $file_name = pathinfo($file_path, PATHINFO_FILENAME) . '.' . pathinfo($file_path, PATHINFO_EXTENSION);

        $file->move($this->document_path, $file_name);

        return $file_name;
    }
}