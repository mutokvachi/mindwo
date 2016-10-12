<?php

namespace App\Libraries\Processes;

use DB;
use Intervention\Image\ImageManager;

/**
 * Klase priekš darbinieku integrācijas. To izpilda caur laravel darbiem (jobs)
 */
class Process_EMPLOYEE extends Process
{

    /**
     * Darbinieku attēla glabāšanas ustādījumi.
     * Lai saglabātu attēlus dažādās dimensijas un attiecīgās mapēs
     * jāpievieno jauni masīvi ar atslēgām "folder", "height" un "width".
     *
     * Jāņem vērā malu attiecības!
     * Mapei jābūt jau izveidotai un ar attiecīgām rakstīšanas tiesībām!
     * 
     * @var array
     */
    private $imageOptions = array(
        array(
            'folder' => '120x160', // mapes nosaukums
            'height' => 160, // attēla augstums
            'width' => 120, // attēla platums
        ),
    );

    /**
     * Izpildot procesu ielādē sistēmā jaunākos lietotāju datus
     * 
     * @return void
     */
    protected function work()
    {
        // Palielinēt, ja konkrētu attēlu apstrādei izrādas par īsu.
        // ini_set('memory_limit', '512M');


        $this->process->get_method = 'get_employees';

        // Pazīme vai vēl ir pieejamas lapas ar darbiniekiem
        $has_next_page = true;

        // Nākošās ielādējamās lapas numurs
        $page_number = 1;

        // Darbieku skaits lapā
        $count_per_page = 250;

        while ($has_next_page) {
            $request_args = array(
                'page_number' => $page_number,
                'count_per_page' => $count_per_page,
                'add_inactive' => 'N',
                'add_absences' => 'Y',
                'add_phones' => 'Y',
                'interface_name' => 'LEPORT',
                'last_update_from' => '1900-09-29T04:49:45',
            );

            $employees_data = $this->getSOAPWebServerData($request_args)->employees;
            $this->processData($employees_data);

            // Pārbauda vai esam pēdējā lapā
            if (count($employees_data->employee) < $count_per_page) {
                $has_next_page = false;
            }

            $page_number++;
        }
        
        // Atjauninam pazīmi struktūrvienību tabulā - vai ir kāds darbinieks piesaistīts struktūrvienībai
        DB::update('update in_departments as d set is_employees = 1 where exists (select id from in_employees where department_id = d.id)');
        DB::update('update in_departments as d set is_employees = 0 where not exists (select id from in_employees where department_id = d.id)');
    }

    /**
     * Saglabā izgūtos datus no web servisa 
     * 
     * @param array $employees_data darbinieku dati, kas izgūti no web servisa
     * 
     * @return void
     */
    private function processData($employees_data)
    {
        foreach ($employees_data->employee as $new_employee_data) {
            $employee = \App\Models\Employee::where('code', $new_employee_data->person_id)->first();
            
            // Izveido jaunu darbinieku, ja tāds neeksistē
            if (!$employee) {
                $employee = new \App\Models\Employee();
            }

            DB::beginTransaction();

            // Uzstāda jaunos datus un saglabā
            $this->setEmployeeData($employee, $new_employee_data);

            DB::commit();
        }
    }

    /**
     * Atjauno datus ekistējošam vai jaunam darbiniekam un saglabā tos
     * 
     * @param \App\Models\Employee $employee darbinieka objekts, kurā uzstāda datus
     * @param array $new_employee_data jaunākie darbinieka dati
     * 
     * @return void
     */
    private function setEmployeeData(\App\Models\Employee $employee, $new_employee_data)
    {
        $employee->code = (string) $new_employee_data->person_id;

        $employee->employee_name = (string) $new_employee_data->first_name . ' ' . $new_employee_data->last_name;
        $employee->office_address = (string) $new_employee_data->room_location;
        $employee->office_cabinet = (string) $new_employee_data->room_number;
        $employee->email = (string) $new_employee_data->email_address;
        $employee->position = (string) $new_employee_data->position_name;
        $employee->birth_date = (new \DateTime($new_employee_data->date_of_birth))->format('Y-m-d');

        // Uzstāda prombūtnes datus
        $this->setAbsences($employee, $new_employee_data);

        if ( ! empty($new_employee_data->img_large)) {
            $employee->picture_guid = $this->processEmployeeImage($new_employee_data->img_large);
        }

        // No web servisa netiek precizēti tālruņa numuri, kas ir mobīlais, parastais vai fakss
        // Ja telefona numuri netiek izšķirti, tad NEPIECIEŠAMS PALIELINĀT LAUKA 'phone' IZMĒRU! Tagad ir tikai 50 simboli.
        $employee->phone = (string) $new_employee_data->phone_numbers;

        // Iegūst menedžera id, ja neatrod, tad uzstāda null vērtību
        $employee->manager_id = $this->getManager($new_employee_data->supervisor_id);

        // Iegūst departamentu
        $department = $this->getDepartment($new_employee_data->structure_code);

        if ($department != null) {
            // Uzstāda departmentu
            $employee->department_id = $department->id;

            // Uzstāda datu avotu
            $employee->source_id = $department->source_id;
        }

        // Ja jauns lietotājs, tad saglabā uzsākšanas datumu, ja lietotājs neaktīvs, tad uzstāda beigu datumu
        if (!$employee->id) {
            $employee->start_date = $new_employee_data->last_max_update_date;
        } else if ($employee->is_deleted === 'N') {
            $employee->end_date = $new_employee_data->last_max_update_date;
        }

        // Saglabā izveides laiku
        if (!$employee->created_time) {
            $employee->created_time = DB::raw('NOW()');
        }

        // Pārbauda vai ir veiktas kādas izmaiņas
        if ($employee->isDirty()) {
            $employee->modified_time = DB::raw('NOW()');

            // Šajā metodē jebkurā gadījumā saglabā datus, tikai, ja ir izmaiņas
            try {
                // Saglabā izmaiņu vēsturi, ja ir veiktas izmaiņas
                $this->saveHistoryChanges($employee, $new_employee_data->last_max_update_date, $new_employee_data->structure_name);
                $employee->save();
                
            } catch (\Illuminate\Database\QueryException $e) {
                throw $e;
            }
        }
    }

    /**
     * Saglabā izmaiņu vēsturi, ja ir veiktas izmaiņas darbinieka datos
     * 
     * @param \App\Models\Employee $employee Darbinieka dati
     * @param DateTime $last_modified Datums, kad pēdējo reizi labots
     * @param string $new_department_title Jaunā departamenta nosaukums
     * 
     * @return void
     */
    private function saveHistoryChanges(\App\Models\Employee $employee, $last_modified, $new_department_title)
    {
        if (!$employee->isDirty('source_id') && !$employee->isDirty('position') && !$employee->isDirty('department_id')) {
            return;
        }

        // Iegūst datu bāzē saglabātos datus (pirms izmaiņām)
        $original_employee = $employee->getOriginal();

        $history_entry = new \App\Models\EmployeesHistory();

        $history_entry->employee_id = $employee->id;

        $history_entry->new_source_id = $employee->source_id;
        $history_entry->new_position = $employee->position;
        $history_entry->new_department = $new_department_title;
        $history_entry->new_department_id = $employee->department_id;

        $history_entry->valid_from = $last_modified;

        // Uzstāda vecos datus, ja darbinieku atjaunojam nevis izveidojam
        if ($employee->id) {
            $history_entry->old_source_id = $original_employee['source_id'];
            $history_entry->old_position = $original_employee['position'];
            $history_entry->old_department = \App\Models\Department::find($original_employee['department_id'])->title;
            $history_entry->old_department_id = $original_employee['department_id'];

            // Pēdējam vēstures ierakstam uzstāda spēkā līdz datumu (-1 diena)
            $this->updatePreviousHistoryEntry($employee->id, $last_modified);
        } else {
            $employee->save();
            $history_entry->employee_id = $employee->id;
        }

        $history_entry->save();
    }

    /**
     * Pēdējam vēstures ierakstam uzstāda spēkā līdz datumu (-1 diena)
     * 
     * @param integer $employee_id Darbinieka identifikators
     * @param DateTime $last_modified Datums, kad pēdējo reizi veiktas izmaiņas
     * 
     * @return void
     */
    private function updatePreviousHistoryEntry($employee_id, $last_modified)
    {
        // Iegūst iepriekšējo vēstures audita ierakstu, lai uzstādītu spēkā līdz datumu
        $old_history_entry = \App\Models\EmployeesHistory::where('valid_to', null)
                ->where('employee_id', $employee_id)
                ->first();

        // Spēkā līdz datums ir jaunais datums -1 diena
        $valid_to_date = (new \DateTime($last_modified))->sub(new \DateInterval('P1D'));

        // Ja tomēr līdz datums izrādās lielāks, tad neatņemam vienu dienu
        if ($valid_to_date > $old_history_entry->valid_from) {
            $old_history_entry->valid_to = $last_modified;
        } else {
            $old_history_entry->valid_to = $valid_to_date;
        }

        $old_history_entry->save();
    }

    /**
     * Uzstāda prombūtnes datus
     * 
     * @param \App\Models\Employee $employee darbinieka objekts, kurā uzstāda datus
     * @param array $new_employee_data jaunākie darbinieka dati
     * 
     * @return void
     */
    private function setAbsences(\App\Models\Employee $employee, $new_employee_data)
    {
        if (isset($new_employee_data->absences->absences_item)) {
            if (is_array($new_employee_data->absences->absences_item)) {
                foreach ($new_employee_data->absences->absences_item as $absence) {
                    $employee->left_from = (new \DateTime($absence->date_from))->format('Y-m-d');
                    $employee->left_to = (new \DateTime($absence->date_to))->format('Y-m-d');
                    $employee->left_reason_id = $this->getAbsences($absence->time_category_desc);
                }
            } else {
                $employee->left_from = (new \DateTime($new_employee_data->absences->absences_item->date_from))->format('Y-m-d');
                $employee->left_to = (new \DateTime($new_employee_data->absences->absences_item->date_to))->format('Y-m-d');
                $employee->left_reason_id = $this->getAbsences($new_employee_data->absences->absences_item->time_category_desc);
            }
        } else {
            $employee->left_from = null;
            $employee->left_to = null;
            $employee->left_reason_id = null;
        }
    }

    /**
     * Iegūst prombūtnes kategorijas id, ja neatrod, tad izveido jaunu
     * 
     * @param integer $absence_cat Prombūtnes kategorijas nosaukums no web servisa
     * 
     * @return integer Atgriež prombūtnes kategorijas id
     */
    private function getAbsences($absence_cat)
    {
        $absence = \App\Models\LeftReasons::where('title', $absence_cat)->first();

        if (!$absence) {
            $absence = new \App\Models\LeftReasons;
            $absence->title = $absence_cat;
            $absence->created_time = DB::raw('NOW()');

            $absence->save();
        }

        return $absence->id;
    }

    /**
     * Iegūst menedžera id, ja neatrod, tad atgriež null vērtību
     * 
     * @param integer $employee_code Menedžera kods (ārējais id) no web servisa
     * 
     * @return integer|null Atgriež menedžera darbinieka id, ja neatrod, tad atgriež null vērtību
     */
    private function getManager($employee_code)
    {
        $manager = \App\Models\Employee::where('code', $employee_code)->first();

        if ($manager) {
            return $manager->id;
        } else {
            return null;
        }
    }

    /**
     * Iegūst departmentu, ja neeksistē, tad izveido jaunu
     * 
     * @param string $department_code Departmenta kods no ārējās sistēmas
     * 
     * @return \App\Models\Department|null Departments vai NULL
     */
    private function getDepartment($department_code)
    {
        $department = \App\Models\Department::where('code', $department_code)->first();

        // Ja neeksistē
        if (!$department) {
            return null;
        }

        return $department;
    }

    /**
     * Iegūst un apstrādā darbinieka attēlu.
     * 
     * @param  string $imageLink Saite uz attēlu.
     * 
     * @return mixed Atgriež attēla ID, ja viss kārtībā - false, ja nē.
     */
    private function processEmployeeImage($imageLink)
    {
        /**
         * Ja nav ieslēgts allow_url_fopen, tad čau.
         *
         * TODO: jāiestrādā drošāki datu iegūšanas paņēmieni.
         */
        if (!ini_get('allow_url_fopen')) {
            return false;
        }

        $imageID = $this->getImageID($imageLink);
        $downloadRequired = false;

        foreach ($this->imageOptions as $imageOptions) {
            if (!file_exists("public/img/avatar/$imageOptions[folder]/$imageID.jpg")) {
                $downloadRequired = true;
            }
        }

        if ($downloadRequired) {
            // Apstrādājam attēlu ar Intervention Image.
            $manager = new ImageManager;
            $img = $manager->make(file_get_contents($imageLink));

            // Uzticamies, ka masīvs ir nodefinēts pareizi.
            foreach ($this->imageOptions as $imageOptions) {
                if (!file_exists("public/img/avatar/$imageOptions[folder]/$imageID.jpg")) {
                    $img->fit($imageOptions['width'], $imageOptions['height']);
                    $img->save("public/img/avatar/$imageOptions[folder]/$imageID.jpg");
                }
            }
        }

        return $imageID;
    }

    /**
     * Atgriež attēla ID.
     * 
     * @param  string $imageLink Saite uz attēlu.
     * 
     * @return mixed Atgriež attēla ID, ja viss kārtībā - false, ja nē.
     */
    private function getImageID($imageLink)
    {
        $components = explode('/', $imageLink);

        if ($components) {
            return $components[count($components) - 1];
        }

        return false;
    }
}