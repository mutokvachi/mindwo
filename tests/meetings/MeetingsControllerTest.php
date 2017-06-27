<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests meetings controller
 */
class MeetingsControllerTest extends TestCase
{
    /**
     * Perform test.
     *
     * @return void
     */
    public function test()
    {
        $this->startSession();
        
        $user = \App\User::find(1); // superadmin
        /*
        $this->actingAs($user)
             ->visit('/meetings/1')
             ->see('Jauna sapulce');
        */
    }
}
