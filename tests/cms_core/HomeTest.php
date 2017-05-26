<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests if login page is loaded
 */
class HomeTest extends TestCase
{
    /**
     * Perform test.
     *
     * @return void
     */
    public function test()
    {
        $this->visit('/')
             ->see(trans('index.about_title'));
    }
}
