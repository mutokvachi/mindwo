<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests new item opening form for all registers with stadart forms
 */
class FormNewTest extends TestCase
{   
    /**
     * Standart form ID
     * 
     * Standart forms have route "/form"
     */
    const FORM_TYPE_STANDART = 1;
    
    /**
     * Perform test.
     *
     * @return void
     */
    public function test()
    {    
        $this->startSession();
         
        $user = \App\User::find(1); // superadmin
        
        $forms = DB::table('dx_forms as f')
                ->where('f.form_type_id', '=', self::FORM_TYPE_STANDART)
                ->whereExists(function($query) use ($user) {
                        $query->select(DB::raw(1))                          
                          ->from('dx_users_roles as ur')   
                          ->join('dx_roles_lists as rl', 'ur.role_id', '=', 'rl.role_id')
                          ->where('ur.user_id', '=', $user->id)
                          ->whereRaw('rl.list_id = f.list_id')
                          ->where('rl.is_new_rights', '=', 1);
                    })
                ->get();
        
        foreach($forms as $frm) {
            $this->actingAs($user)
                 ->json('POST', '/form', ['list_id' => $frm->list_id, 'item_id' => 0, '_token' => csrf_token()])
                 ->seeJson([
                     'success' => 1,
                 ]);
        }
    }
}
