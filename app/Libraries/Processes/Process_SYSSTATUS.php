<?php

namespace App\Libraries\Processes;

use DB;

/**
 * Klase priekš sistēmas statusu atjaunošanas. To izpilda caur laravel darbiem (jobs)
 */
class Process_SYSSTATUS extends Process
{

    /**
     * Izpildot procesu ielādē sistēmā jaunākos sistēmu datus
     * 
     * @return void
     */
    protected function work()
    {
        $incidents = $this->getSOAPWebServerData()->incidents;
        $this->processData($incidents);
    }

    /**
     * Saglabā izgūtos datus no web servisa 
     * 
     * @param array $incidents_data incidentu dati, kas izgūti no web servisa
     * 
     * @return void
     */
    private function processData($incidents_data)
    {
        foreach ($incidents_data as $new_incident_data) {
            $system = \App\Models\System::where('name', $new_incident_data->SUBSYSTEM)->first();

            // Sistēma nav reģistrēta vietnē, izveido jaunu
            if (!$system) {
                $system = new \App\Models\System;
                $system->name = $new_incident_data->SUBSYSTEM;
                $system->save();
            }

            DB::beginTransaction();

            // Saglabā incidenta datus
            $this->saveIncident($system, $new_incident_data);

            DB::commit();
        }
    }

    /**
     * Reģistrē sistēmas incidentu
     * 
     * @param \App\Models\System $system sistēmas objekts, kurā reģistrē incidentu
     * @param array $new_incident_data incidenta dati
     * 
     * @return void
     */
    private function saveIncident(\App\Models\System $system, $new_incident_data)
    {
        $incident_created = (new \DateTime($new_incident_data->MISSFUNCTION_START));

        // Mēģina atrast incidentu
        $incident = \App\Models\Incident::where('system_id', $system->id)
                ->where('created_time', $incident_created)
                ->first();

        // ja incidents nav atrasts, tad reģistrē jaunu incidentu
        if (!$incident) {
            $incident = new \App\Models\Incident;

            $incident->system_id = $system->id;
        }

        $incident->created_time = $incident_created;

        if ($new_incident_data->MISSFUNCTION_END) {
            $incident->solved_time = (new \DateTime($new_incident_data->MISSFUNCTION_END));
            $incident->is_crash = 1;
        } else {
            $incident->is_crash = 0;
        }
        $incident->details = (string) $new_incident_data->SUMMARY;

        if (!$incident->created_time) {
            $incident->created_time = DB::raw('NOW()');
        }

        // Pārbauda vai ir veiktas kādas izmaiņas
        if ($incident->isDirty()) {
            $incident->modified_time = DB::raw('NOW()');
            
            $incident->save();
        }
    }
}