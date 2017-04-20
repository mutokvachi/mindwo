<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests all registers grids/views first load
 */
class GridTest extends TestCase
{
    /**
     * Perform test.
     *
     * @return void
     */
    public function test()
    {
        $this->startSession();
        
        $user = factory(App\User::class)->create()->find(1); // superadmin

        $views = DB::table('dx_views')->get();
        
        foreach($views as $view) {
            $this->actingAs($user)
                 ->visit('/skats_' . $view->id)
                 ->see(trans('grid.lbl_actions'));
        }
    }
}
