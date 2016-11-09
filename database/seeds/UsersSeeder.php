<?php

use Illuminate\Database\Seeder;

/**
 * Class UsersSeeder
 *
 * Seeder for populating dx_users table with demo data
 */
class UsersSeeder extends Seeder
{
    /**
     * Create 10 users and add Public access rights for them.
     *
     * @return void
     */
    public function run()
    {
		$users = factory(App\User::class, 10)->create()->each(function($user) {
			$user->roles()->save(factory(App\Models\UserRoles::class)->make());
		});
    }
}
