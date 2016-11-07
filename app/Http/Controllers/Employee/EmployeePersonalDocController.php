<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Log;

/**
 * Employee's personal documents controller
 */
class EmployeePersonalDocController extends Controller
{

    protected $document_path;

    public function __construct()
    {
        $this->document_path = storage_path(config('assets.private_file_path'));
    }

    public function testView()
    {
        return view('pages.employees_doc_test');
    }

    public function getPersonalDocsByCountry($country_id)
    {
        $country = \App\Models\Country::find($country_id);

        return json_encode($country->personalDocs()->get());
    }

    public function getEmployeeDocs($user_id)
    {
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

    public function getView($user_id, $is_disabled)
    {
        $user = \App\User::find($user_id);

        if (!$user) {
            return '0';
        }

        return view('profile.personal_docs', [
                    'user' => $user,
                    'is_disabled' => ($is_disabled == 1 ? true : false)
                ])->render();
    }

    public function save(Request $request)
    {
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

            $emp_pers_doc = $this->saveEmpPersDoc($request, $counter, $user_id, $emp_pers_doc, $input_row);

            $result[] = [
                'id' => $emp_pers_doc->id,
                'doc_id' => $emp_pers_doc->doc_id,
                'file_name' => $emp_pers_doc->file_name
            ];
        }

        return json_encode($result);
    }

    private function deleteOldDocument($file_guid)
    {
        $file_path = $this->document_path . DIRECTORY_SEPARATOR . $file_guid;

        if ($file_guid && file_exists($file_path)) {
            unlink($file_path);
        }
    }

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

        $emp_pers_doc->user_id = (int) $user_id;
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

    private function saveFile($file)
    {
        $file_path = tempnam($this->document_path, 'emp');

        $file_name = pathinfo($file_path, PATHINFO_FILENAME) . '.' . pathinfo($file_path, PATHINFO_EXTENSION);

        $file->move($this->document_path, $file_name);

        return $file_name;
    }
}
