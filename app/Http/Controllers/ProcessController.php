<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\Processes;

/**
 * Proceu kontrolierī startē forsēti procesus, neatkarīgi no grafikā noteiktā laika
 */
class ProcessController extends Controller
{

    /**
     * Izsauc procesu forsēti, neņemot vērā grafiku.
     * 
     * @param string $code Procesa kods, kuru nepieciešams startēt
     * 
     * @return void
     */
    public function forceProcess($code)
    {
        Processes\ProcessFactory::initializeProcess($code, true);
    }

    /**
     * Testa funkcija, kas izgūst json datus
     * 
     * @param string $readviewentries Parametrs netiek izmantots
     * @param string $outputformat Parametrs netiek izmantots
     * @param string $Start Parametrs netiek izmantots
     * @param string $Count Parametrs netiek izmantots
     * 
     * @return string JSON dati
     */
    public function testRESTResponse($readviewentries, $outputformat, $Start, $Count)
    {
        $jsonRes = storage_path('app/test/test_rest_response.json');
        return \Illuminate\Support\Facades\File::get($jsonRes);
    }
}