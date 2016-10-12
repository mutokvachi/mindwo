<?php

namespace App\Libraries\Processes;

use Illuminate\Support\Facades\Bus;
use App\Jobs;
use DB;

/**
 * Izveido darbu, kurā izpilda procesu, ja tas ir nepieciešams (skatoties pēc laika vai ja izpilda manuāli)
 */
class ProcessFactory
{
    /**
     * Inicializē procesa klasi pēc padotā procesa koda
     *
     * @param string $process_code Processa kods, kuru nepieciešams izpildīt
     * @param boolean @force Pazīme vai process forsēti jāsāk, neskatoties vai nav pagājis noteiktais laiks kopš pēdējās izpildes
     * @return void
     */
    public static function initializeProcess($process_code, $force = false)
    {
        // Pārtaisa uz lielajiem burtiem procesa nosaukumu
        $process_code = strtoupper($process_code);
        
        // Pārbauda vai vajag izpildīt pēc grafika vai arī ja ir forsēta izpilde
        if (!$force && !self::checkShouldRun($process_code)) {
            return;
        }

        // Sagatavo klases pilno nosaukumu
        $class = "App\\Libraries\\Processes\\Process_" . $process_code;

        // Pārabuda vai klase eksistē un vai tā manto procesu klasi
        if (!is_subclass_of($class, "App\\Libraries\\Processes\\Process")) {
            throw new \Exception('Process "' . $process_code . '" neeksistē vai ir nekorekti definēts.');
        }

        // Izveido procesa klasi
        $process = new $class($process_code);
        
        // Nosūta procesu izpildei
        Bus::dispatch(new Jobs\ExecuteProcess($process));
    }

    /**
     * Pārbauda vai pēc grafika process ir jāizpilda
     * 
     * @param string $process_code Processa kods, kuru nepieciešams izpildīt
     * @return boolean Pazīme vai procesu nepieciešams izpildīt
     */
    private static function checkShouldRun($process_code)
    {
        // Iegūst atbilstošā procesa datus
        $shouldRun = DB::table('in_processes AS p')
                ->select(DB::raw(1))
                ->whereRaw('p.code = \'' . $process_code . '\' AND '
                        . 'p.schedule_to >= HOUR(NOW()) AND '
                        . 'p.schedule_from <= HOUR(NOW()) AND '
                        . '(p.schedule_every_minutes < TIMESTAMPDIFF(MINUTE, p.last_executed_time, NOW()) OR p.last_executed_time IS NULL)')
                ->first();

        if ($shouldRun) {
            return true;
        } else {
            return false;
        }
    }
}