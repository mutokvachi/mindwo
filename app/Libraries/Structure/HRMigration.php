<?php

namespace App\Libraries\Structure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Config;
use App;
use DB;

/**
 * Migration class for HR modules structure creation
 */
abstract class HRMigration extends Migration
{
    /**
     * HR role ID
     *
     * @var integer
     */
    public $RoleHR_id = 0;

    public $RoleHR_emails_id = 0;

    /**
     * Migration equivalent for up
     */
    abstract protected function hr_up();
    
    /**
     * Migration equivalent for down
     */
    abstract protected function hr_down();
    
    /**
     * Creates structure - checks if education option is turned on
     */
    public function up()
    {
        if (!$this->isHR()) {
            return;
        }
        
        $this->hr_up();
    }
    
    /**
     * Destroy structure - checks if education option is turned on
     */
    public function down()
    {
        if (!$this->isHR()) {
            return;
        }
        
        $this->hr_down();
    }
    
    /**
     * Checks if language is EN and HR role exists 
     * @return boolean
     */
    private function isHR() {

        if (App::getLocale() != 'en') {
            return false;
        }
        
        $hr_role = DB::table('dx_roles')->where('title', '=', 'HR')->first();

        if (!$hr_role) {
            return false;
        }

        $this->RoleHR_id = $hr_role->id;

        $hr_email = DB::table('dx_roles')->where('title', '=', 'HR emails')->first();

        if ($hr_email) {
            $this->RoleHR_emails_id = $hr_email->id;
        }

        return true;
    }
}