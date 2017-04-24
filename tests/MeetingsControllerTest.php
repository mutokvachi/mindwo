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
        
        $user = factory(App\User::class)->create()->find(1); // superadmin
        
        $this->actingAs($user)
             ->visit('/meetings/1')
             ->see('Jauna sapulce');
        
    }
}
