<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests birthday search page
 */
class BirthdaysSearchPageTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * Perform test.
     *
     * @return void
     */
    public function test()
    {
        $this->startSession();
        
        // get some non-systemic user
        $some_user = DB::table('dx_users')->whereNotIn('id', Config::get('dx.empl_ignore_ids'))->first();
        
        // set birthday to today
        DB::table('dx_users')->where('id', '=', $some_user->id)->update(['birth_date' => date('Y-m-d')]);
        
        $user = \App\User::find(1); // superadmin
        
        $this->actingAs($user)
             ->visit('/dzimsanas_dienas_sodien')
             ->see($some_user->display_name);        
    }
}
