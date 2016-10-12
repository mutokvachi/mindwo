<?php

namespace App\Libraries\Processes;

use DB;

/**
 * Klase priekš darbinieku integrācijas. To izpilda caur laravel darbiem (jobs)
 */
class Process_DEPARTMENT extends Process
{

    /**
     * Izpildot procesu ielādē sistēmā jaunākos departmenta datus
     * 
     * @return void
     */
    protected function work()
    {
        $this->retriveDepartments();
    }

    /**
     * Iegūst datus no ārējā servera
     * 
     * @return void
     */
    private function retriveDepartments()
    {
        // Pazīme vai vēl ir pieejamas lapas ar departmentiem
        $has_next_page = true;

        // Nākošās ielādējamās lapas numurs
        $page_number = 1;

        // Ierakstu skaits lapā
        $count_per_page = 500;

        $department_data = array();

        $this->process->get_method = 'get_organizations';
        // 22 AS Latvenergo
        // 3107 AS Sadales tīkls
        // 2340 SIA Liepājas Enerģija
        // 3619 Elektrum Lietuva UAB
        // 3839 AS Augstsprieguma tīkls
        // 4380 Elektrum Eesti OU
        // 4440 AS Latvijas elektriskie tīkli
        // 5503 AS Enerģijas publiskais tirgotājs
        while ($has_next_page) {
            $request_args = array(
                'bg_territory_code' => 'LV',
                'root_org_id_list' => array('root_org_id_list_item' => 22),
                'page_number' => $page_number,
                'count_per_page' => $count_per_page,
                'interface_name' => 'LEPORT'
            );

            $iter_data = $this->getSOAPWebServerData($request_args)->organizations->organization;

            $department_data = array_merge($department_data, $iter_data);
            
            // Pārbauda vai esam pēdējā lapā
            if (count($iter_data) < $count_per_page) {
                $has_next_page = false;
            }

            $page_number++;
        }

        $this->processData($department_data);
    }

    /**
     * Apstrādā izgūtos datus no web servisa 
     * 
     * @param array $department_data departmenta dati, kas izgūti no web servisa
     * 
     * @return void
     */
    private function processData($department_data)
    {
        $departments_by_lvl = array();

        $max_lvl = 0;

        // Sakārto departamentus pa līmeņiem
        foreach ($department_data as $new_department_data) {
            $departments_by_lvl[$new_department_data->org_level][] = $new_department_data;

            if ($new_department_data->org_level > $max_lvl) {
                $max_lvl = $new_department_data->org_level;
            }
        }

        // Secīgi saglabā departamentus
        for ($index = 0; $index <= $max_lvl; $index++) {
            if (array_key_exists($index, $departments_by_lvl)) {
                $this->updateDepartment($departments_by_lvl[$index]);
            }
        }
    }

    /**
     * Saglabā datus datu bāzē
     * 
     * @param array $department_data Dati no servera
     * 
     * @return void
     */
    private function updateDepartment($department_data)
    {
        foreach ($department_data as $new_department_data) {
            $department = \App\Models\Department::where('code', $new_department_data->organization_code)->first();

            // Izveido jaunu departmentu, ja tāds neeksistē
            if (!$department) {
                $department = new \App\Models\Department();
            }

            // Uzstāda jaunos datus un saglabā
            $this->setDepartmentData($department, $new_department_data);
        }
    }

    /**
     * Atjauno datus ekistējošam vai jaunam departmentam un saglabā tos
     * 
     * @param \App\Models\Department $department departmenta objekts, kurā uzstāda datus
     * @param array $new_department_data jaunākie departmenta dati
     * 
     * @return void
     */
    private function setDepartmentData(\App\Models\Department $department, $new_department_data)
    {
        $department->external_id = (int) $new_department_data->organization_id;
        $department->code = (string) $new_department_data->organization_code;
        $department->title = (string) $new_department_data->organization_name;
        $department->source_id = $this->getSource($new_department_data->root_org_id, $new_department_data->root_org_name);
        
        $parent_id = $this->getParentDepartment($new_department_data->parent_org_id);

        if ($parent_id) {
            $department->parent_id = $parent_id;
        }

        // Saglabā izveides laiku
        if (!$department->created_time) {
            $department->created_time = DB::raw('NOW()');
        }

        if ($department->isDirty()) {
            $department->modified_time = DB::raw('NOW()');

            // Šajā metodē saglabā datus, tikai, ja ir izmaiņas
            try {
                $department->save();
            } catch (\Illuminate\Database\QueryException $e) {
                throw $e;
            }
        }
    }

    /**
     * Iegūst departamentu, ja netiek atrasts, tad mēģina iegūt datus no ārējās sistēmas
     * 
     * @param int $external_id Arējās sistēmas departmenta identifikators
     * @return int|boolean Atrastā departamenta identifikators vai false vērtība
     */
    private function getParentDepartment($external_id)
    {
        $department = \App\Models\Department::where('external_id', $external_id)->first();

        if ($department) {
            return $department->id;
        } else {
            return false;
        }
    }

    /**
     * Iegūst datu avout pēc dotā koda.
     * @param string $code Datu avota kods - ārējās sistēmas ID
     * @param string $title Datu avota nosaukums
     * @return int Datu avota identifikators
     * @throws \Exception Kļūda par neatrastu datu avotu
     */
    private function getSource($code, $title)
    {
        $source = \App\Models\Source::where('code', $code)->first();

        if (!$source) {
            $source = new \App\Models\Source;
            $source->code = $code;
            $source->title = $title;
            $source->save();
        }

        if ($source) {
            return $source->id;
        } else {
            throw new \Exception('Sistēmā netika atrasts datu avots "' . $title . '" ar kodu "' . $code . '". Lai varētu veikt datu migrāciju, nepieciešams reģistrēt šo datu avotu sistēmā.');
        }
    }
}