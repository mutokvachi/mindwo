<?php

namespace App\Libraries\Structure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Config;

/**
 * Migration class for education process modules structure creation
 */
abstract class EduMigration extends Migration
{
    const ROLE_MAIN = 74;
    const ROLE_ORG = 75;
    const ROLE_TEACH = 76;
    const ROLE_STUD = 77;
    const ROLE_SUPPORT = 78;
    
    /**
     * Migration equivalent for up
     */
    abstract protected function edu_up();
    
    /**
     * Migration equivalent for down
     */
    abstract protected function edu_down();
    
    /**
     * Creates structure - checks if education option is turned on
     */
    public function up()
    {
        if (!$this->isEdu()) {
            return;
        }
        
        $this->edu_up();
    }
    
    /**
     * Destroy structure - checks if education option is turned on
     */
    public function down()
    {
        if (!$this->isEdu()) {
            return;
        }
        
        $this->edu_down();
    }
    
    /**
     * Checks if education modules option is on 
     * @return boolean
     */
    private function isEdu() {
        return Config::get('dx.is_edu_modules', false);
    }
}