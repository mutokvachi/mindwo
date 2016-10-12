<?php

namespace App\Libraries\Processes;

use DB;
use GuzzleHttp\Client as HttpClient;

/**
 * Bāzes abstraktā klase priekš procesiem, kurus izpilda ar laravel darbiem (jobs)
 */
abstract class Process
{
    /**
     * @var \App\Process Satur atbilstošo procesa.
     */
    protected $process;

    /**
     * @var \App\ProcessLog Satur atbilstošā procesa audita ierakstu.
     */
    private $process_log_entry;

    /**
     * Funkcijā atrodas visa loģika, kas tiek izpildīta, kad tiek izpildīts šis process
     * 
     * @return void
     */
    abstract protected function work();

    /**
     * Klases konstruktors iegūst procesa datus no datu bāzes un izveido audita ierakstu
     *
     * @param string $process_code Kods pēc kura identificē procesu datu bāzē. Nepieciešams visās klasēs.
     * 
     * @return void
     */
    public function __construct($process_code)
    {
        // Iegūst atbilstošo procesu
        $this->process = \App\Process::where('code', $process_code)->first();

        if (!$this->process) {
            throw new \Exception('Process "' . $process_code . '" nav reģistrēts datu bāzē.');
        }

        // Reģistrē pēdējo procesa uzsākšanas laiku
        $this->process->last_executed_time = DB::raw('NOW()');
        $this->process->save();

        // Izveido audita ierakstu un reģistrē izveidošanas laiku
        $this->process_log_entry = new \App\ProcessLog();
        $this->process_log_entry->register_time = DB::raw('NOW()');

        // Saglabā audita ierakstu
        $this->process->processLogs()->save($this->process_log_entry);
    }

    /**
     * Startē procesu un auditē visas darbības.
     * 
     * @return void
     */
    public function execute()
    {   
        $this->logStart();

        try {
            $this->work();

            $this->logEnd(true);
        } catch (\Exception $e) {            
            $this->logEnd(false, $e->getMessage());
            
            $this->sendErrorEmail();
        }
    }

    /**
     * Auditā ieraksta, ka process ir sākts pildīt
     * 
     * @return void
     */
    private function logStart()
    {
        $this->process_log_entry->start_time = DB::raw('NOW()');
        $this->process_log_entry->save();
    }

    /**
     * Auditā ieraksta, ka process ir izpildīts, uzstāda pazīmi vai tas izpildīts veiksmīgi un ja norādīts, tad saglabā paziņojumu
     * 
     * @param boolean $is_success Pazīme vai process izpildījies veiksmīgi
     * @param string $msg Ja nepieciešams, tad saglabā paziņojumu
     * 
     * @return void 
     */
    private function logEnd($is_success, $msg = '')
    {
        $this->process_log_entry->end_time = DB::raw('NOW()');
        $this->process_log_entry->is_success = $is_success;

        if ($msg !== '') {
            $this->process_log_entry->message = $msg;
        }

       $this->process_log_entry->save();
    }

    /**
     * Izsūta e-pastu atbildīgajam darbiniekam, kad procesā ir notikusi kļūda, ar kļūdas paziņojumu un datiem par procesu
     * 
     * @return void
     */
    private function sendErrorEmail()
    {
        try {
            // Iegūst atbilstošā procesa atbildīgā darbinieka e-pastu
            $employee_email = $this->process->employee->email;

            \Illuminate\Support\Facades\Mail::send('emails.process_error'
                    , ["process_name" => $this->process->name, "process_log_entry" => $this->process_log_entry]
                    , function ($message) use ($employee_email) {
                $message->to($employee_email);
            });
        } catch (\Exception $e) {
            
        }
    }

    /**
     * Izgūst datus no SOAP web servera
     * 
     * @param array $request_args Papildus parametri SOAP metodei
     * @return object Objekts ar datiem no web servera
     */
    protected function getSOAPWebServerData($request_args = array())
    {
        $soapOptions = array(
            'trace' => 1,
            'exceptions' => true,
        );

        // Izveidojam pieslēgumu serverim
        $client = new \SoapClient($this->process->url, $soapOptions);

        // Parametri metodes izsaukšanai
        $args = json_decode($this->process->arguments, true);
        
        if (empty($args)) {
            $args = array();
        }

        // Apvieno DB argumentus un metodei iesūtītos argumentus
        $args = array_merge($args, $request_args);

        // Izgūstam datus no servera XML formātā
        $soapXMLResult = $client->__soapCall($this->process->get_method, array('parameters' => $args));

        return $soapXMLResult;
    }

    /**
     * Izgūst datus no REST servera
     * 
     * @param string $url REST Url
     * 
     * @return array Masīvs ar datiem no REST servera
     */
    protected function getRESTServerData($url)
    {
        $client = new \GuzzleHttp\Client();
        
        $res = $client->request('GET', $url);
        
        return json_decode($res->getBody(), true);
    }
}