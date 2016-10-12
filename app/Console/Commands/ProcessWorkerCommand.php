<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\Processes;

/**
 * Inicializē klasi ProcessFactory, kurā tiek izveidoti darbi, kas izpildīs procesus.
 * Darbi tiek  un pievienoti darbu sarakstam.
 * Darbi no saraksta tiek izpildīti ar konsoles komandu.
 */
class ProcessWorkerCommand extends Command
{
    /**
     * @var string Komandas paraksts
     */
    protected $signature = 'process_worker_command';

    /**
     * @var string Konsoles komandas apraksts
     */
    protected $description = 'Izpilda ieplānoto procesu';

    /**
     * Inicializē konsoles komandu
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Izpilda konsoles komandu. 
     * Inicializē ProcessFactory klases objektu, kurš izveido darbu un ievieto darbu sarakstā.
     * Darbos tiek izpildīti procesi, kas mantojas no App\Libraries\Processes\Process klases.
     *
     * @return void
     */
    public function handle()
    {
        // Iegūst visus procesus no datu bāzes
        $processes = \App\Process::all();
        
        // Pārbauda un ja nepieciešams izpilda procesus
        foreach ($processes as $process){
            Processes\ProcessFactory::initializeProcess($process->code, true);
        }
    }
}