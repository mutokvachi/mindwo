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

    public function testView()
    {
        return view('pages.employees_doc_test');
    }

    public function getPersonalDocsByCountry($country_id)
    {
        $country = \App\Models\Country::find($country_id);

        return json_encode($country->personalDocs()->get());
    }

    public function getEmployeeDocs($employee_id)
    {
        $employee = \App\Models\Employee::find($employee_id);

        if (!$employee) {
            return null;
        }

        $docs = $employee->employeePersonalDocs()->with('personalDocument')->get();
        
        return json_encode($docs);
    }

    public function save(Request $request)
    {
        $data = json_decode($request->input('data'), true);

        $employee_id = $data['employee_id'];
        $input_rows = $data['rows'];

        $employee = \App\Models\Employee::find($employee_id);

        if (!$employee) {
            return '0';
        }

        $existing_saved_emp_doc_ids = [];

        foreach ($input_rows as $input_row) {
            if ($input_row['id'] && $input_row['id'] > 0) {
                $existing_saved_emp_doc_ids[] = $input_row['id'];
            }
        }

        // Delete old rows which are removed
        \App\Models\Employee\EmployeePersonalDocument::whereNotIn('id', $existing_saved_emp_doc_ids)->delete();

        // Update existing and insert new
        foreach ($input_rows as $input_row) {

            $emp_pers_doc = '';

            if ($input_row['id'] && $input_row['id'] > 0) {
                $emp_pers_doc = \App\Models\Employee\EmployeePersonalDocument::find($input_row['id']);
            } else {
                $emp_pers_doc = new \App\Models\Employee\EmployeePersonalDocument();
            }

            $this->saveEmpPersDoc($employee_id, $emp_pers_doc, $input_row);
        }

        return '1';
    }

    private function saveEmpPersDoc($employee_id, $emp_pers_doc, $input_row)
    {
        $emp_pers_doc->employee_id = (int) $employee_id;
        $emp_pers_doc->doc_id = (int) $input_row['document_type'];
        $emp_pers_doc->publisher = $input_row['publisher'];

        $emp_pers_doc->save();
    }
}
