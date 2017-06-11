<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Webpatser\Uuid\Uuid;
use DB;
use Auth;
use App\Exceptions;
use Illuminate\Support\Facades\File;

// Uzmanību: ja uzrādas, ka netiek lietoti, tas nekas. Jo varbūt šos izmanto kāds skripts, kas ievietots SVS
use Config;
use Input;
use Log;

/**
 * SVS definēto PHP skriptu izpildes kontrolieris
 */
class CustomPHPController extends Controller
{    
    /**
     * Izpildāmā PHP skripta rinda (no tabulas dx_custom_php)
     * @var Array 
     */
    private $script_row = null;
    
    /**
     * PHP skripta validācijas kļūdas paziņojums
     * @var string 
     */
    private $err_info = "";
    
    /**
    * Izpilda norādīto PHP skriptu (ar eval metodi)
    *
    * @param   Request     $request    POST/GET pieprasījuma objekts
    * @param   mixed       $id         Skripta identifikators (dx_custom_php lauks id) vai arī unikāls url (dx_custom_php lauks url)
    * @return  Response                Skripta izpildes rezultāts, kāds ir definēts pašā skriptā
    */ 
    public function executePHP(Request $request, $id)
    {
        $this->setScriptByID($request, $id);
        
        $this->checkRights($request);
        
        $this->validateScript();
        
        return eval($this->script_row->php_code);
    }
    
    /**
     * Uzstāda skripta objektu
     * 
     * @param type $request POST/GET pieprasījuma objekts
     * @param type $id Skripta identifikators (dx_custom_php lauks id) vai arī unikāls url (dx_custom_php lauks url)
     * @throws Exceptions\DXCustomException
     */
    private function setScriptByID($request, $id)
    {        
        $fld_name = 'url';
        if (is_numeric($id))
        {
            $fld_name = 'id';
        }
        
        $this->script_row = DB::table('dx_custom_php')
                    ->where($fld_name, '=', $id)
                    ->first();

        if ($this->script_row == null)
        {
            throw new Exceptions\DXCustomException("Norādītais izpildāmais skripts '" . $request->fullUrl() . "' nav atrodams!");
        }
    }
    
    /**
     * Pārbauda, vai ir tiesības izpildīt skriptu
     * 
     * @param type $request POST/GET pieprasījuma objekts
     * @throws Exceptions\DXCustomException
     */
    private function checkRights($request) {
        $rights_row = DB::table('dx_users_roles')
                  ->where('user_id', '=', Auth::user()->id)
                  ->where('role_id', '=', $this->script_row->role_id)
                  ->first();
        
        if ($rights_row == null)
        {
            throw new Exceptions\DXCustomException("Nav nepieciešamo tiesību, lai izpildītu skriptu '" . $request->fullUrl() . "'!");
        }
    }
    
    /**
     * Validē PHP skripta sintaksi
     */
    private function validateScript() {
        
        $scriptPath = base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'script_' . Uuid::generate(4) . '.php';
        
        File::put($scriptPath, "<?php ");
        File::append($scriptPath, $this->script_row->php_code);
        
        $process = new Process('php -l ' . $scriptPath);
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                throw new Exceptions\DXCustomException("Nav iespējams pārbaudīt skripta '" . $this->script_row->title . "' sintaksi! Sistēmas kļūda: " . $buffer . ". Lūdzu, sazinieties ar sistēmas uzturētāju.");
            } else {
                if (strpos($buffer, 'No syntax errors detected') === false) {
                    throw new Exceptions\DXCustomException("SVS ievietotais PHP skripta sintakse '" . $this->script_row->title . "' ir kļūdaina! Lūdzu, sazinieties ar sistēmas uzturētāju.");
                }                
            }
        });
        
        File::delete($scriptPath);
    }
    
}