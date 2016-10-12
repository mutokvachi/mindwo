<?php

namespace App\Libraries\Processes;

use DB;

/**
 * Klase priekš dokumentu integrācijas no DIVS sistēmas. To izpilda caur laravel darbiem (jobs)
 */
class Process_LOTUSNOTES extends Process
{
    /**
     * Izpildot procesu ielādē sistēmā jaunākos dokumentu datus
     * 
     * @return void
     */
    protected function work()
    {
        foreach (\App\Models\DocumentsLotus::all() as $sys) {

            // Norāda no sākot kura dokumenta ielādēt nākošos
            $start_counter = 1;

            // Skaits cik dokumentus lejupielādēt katrā reizē
            $download_count = 1000;

            // Pazīme vai ir sasniegtas beigas un ielādēti visi dokumenti
            $has_more = true;

            while ($has_more) {
                // Aizpilda url ar parametriem
                $url = $this->setUrlParameters($sys->json_url, $start_counter, $download_count);

                // Iegūst datus no servera
                $doc_data = $this->getRESTServerData($url);

                // Izprocesē dokumentus
                $this->processData($doc_data, $sys->id);

                // Palielinām ielādes pozīciju
                $start_counter += $download_count;

                // Pārbauda vai ir pēdējie dokumenti un esam sasnieguši galu, ja ir atnākuši mazāk kā pieprasīts maksimālais
                if (count($doc_data['viewentry']) < $download_count) {
                    $has_more = false;
                }
            }
        }
    }

    /**
     * Aizpilda url ar parametriem
     * 
     * @param string $url Url, kurā uzstādīs parametrus
     * @param string $start_counter Parametrs, kurā norāda no sākot kura dokumenta ielādēt nākošos
     * @param string $download_count Skaits cik dokumentus lejupielādēt katrā reizē
     * 
     * @return string Url ar ievietotajiem parametriem
     */
    private function setUrlParameters($url, $start_counter, $download_count)
    {
        if ($p = strpos($url, '?') !== false) {
            $url = substr($url, 0, $p); // Tīra saite.
        }

        $url .= "?readviewentries&outputformat=JSON&start=$start_counter&count=$download_count";

        return $url;
    }

    /**
     * Saglabā izgūtos datus no web servisa 
     * 
     * @param array $doc_data Dokumentu dati, kas izgūti no web servisa
     * @param int $sys_id Sistēmas identifikators, no kuras nāk dokumenti
     * 
     * @return void
     */
    private function processData($doc_data, $sys_id)
    {
        foreach ($doc_data['viewentry'] as $new_doc_data) {
            $doc = \App\Models\Document::where('unid', $new_doc_data['@unid'])->first();

            // Izveido jaunu dokumentu, ja tāds neeksistē
            if (!$doc) {
                $doc = new \App\Models\Document();
            }

            DB::beginTransaction();

            // Uzstāda jaunos datus un saglabā
            $this->setDocData($doc, $new_doc_data, $sys_id);

            DB::commit();
        }
    }

    /**
     * Atjauno datus ekistējošam vai jaunam dokumenta un saglabā tos
     * 
     * @param \App\Models\Document $doc dokumenta objekts, kurā uzstāda datus
     * @param array $new_doc_data jaunākie dokumenta dati
     * @param int $sys_id Sistēmas identifikators, no kuras nāk dokuments
     * 
     * @return void
     */
    private function setDocData(\App\Models\Document $doc, $new_doc_data, $sys_id)
    {
        $doc->unid = $new_doc_data['@unid'];
        $doc->noteid = (string) $new_doc_data['@noteid'];
        $doc->siblings = (integer) $new_doc_data['@siblings'];       
        $doc->doc_system_id = (integer)$sys_id;

        foreach ($new_doc_data['entrydata'] as $entrydata) {
            switch ($entrydata['@name']) {
                case '$9':
                    // Uzstāda dokumenta datumu
                    $date_str = substr($entrydata['datetime'][0], 0, 8);
                    $date = \DateTime::createFromFormat('Ymd', $date_str);
                    $doc->doc_date = $date->format('Y-m-d');
                    break;
                case '$7':
                    // Uzstāda dokumenta nodaļu  
                    $doc->doc_department_id = $this->getDocDepartment($entrydata);
                    break;
                case '$8':
                    // Uzstāda dokumenta numuru
                    $doc->doc_nr = (string) $entrydata['text'][0];
                    break;
                case '$3':
                    // Uzstāda dokumenta versiju
                    $doc->version = number_format($entrydata['text'][0], 2);
                    break;
                case 'DocName':
                    // Uzstāda dokumenta pilno nosaukumu
                    $doc->doc_title = (string) $entrydata['text'][0];
                    break;
                case 'DocTypeNickName':
                    // Uzstāda dokumenta tipu
                    $doc->doc_kind_id = $this->getDocKind($entrydata['text'][0]);
                    break;
            }
        }
        
         if (!$doc->created_time) {
            $doc->created_time = DB::raw('NOW()');
        }

        // Pārbauda vai ir veiktas kādas izmaiņas
        if ($doc->isDirty()) {
            $doc->modified_time = DB::raw('NOW()');
            
            $doc->imported_time = DB::raw('NOW()');

            // Šajā metodē jebkurā gadījumā saglabā datus, tikai, ja ir izmaiņas
            $doc->save();
        }
    }

    /**
     * Iegūst dokumenta tipu, ja neeksistē, tad izveido jaunu
     * 
     * @param integer $title Dokumenta tipa nosaukums
     * 
     * @return integer Atgriež dokumenta tipa id, ja neatrod, tad atgriež null vērtību
     */
    private function getDocKind($title)
    {
        $doc_kind = \App\Models\DocumentKind::where('title', $title)->first();

        // Ja neeksistē
        if (!$doc_kind) {
            $doc_kind = new \App\Models\DocumentKind();
            $doc_kind->title = $title;
            $doc_kind->save();
        }
        return $doc_kind->id;
    }

    /**
     * Iegūst departmentu, ja neeksistē, tad izveido jaunu
     * 
     * @param array $entrydata Saraksts ar departamenta datiem (neapstrādāts)
     * 
     * @return integer Dokumenta departmenta identifikators
     */
    private function getDocDepartment($entrydata)
    {
        $name = '';

        // JSON divos struktūras veidos tiek definēts nodaļas nosaukums
        if (array_key_exists('textlist', $entrydata)) {
            $name = $entrydata['textlist']['text'][0][0];
        } else {
            $name = $entrydata['text'][0];
        }

        $department = \App\Models\DocumentDepartment::where('name', $name)->first();

        // Ja neeksistē
        if (!$department) {
            $department = new \App\Models\DocumentDepartment();

            $department->name = $name;

            $department->save();
        }

        return $department->id;
    }
}