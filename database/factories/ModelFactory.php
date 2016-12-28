<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Illuminate\Support\Facades\Hash;

/**
 * Model factory for dx_users table
 */
$factory->define(App\User::class, function(Faker\Generator $faker)
{
	$gender = $faker->numberBetween(1, 2);
	$firstName = $faker->firstName(($gender == 1) ? 'male' : 'female');
	$lastName = $faker->lastName;
	$mobile = $faker->e164PhoneNumber;
	
	$img_folder = public_path('img/');
	$thumb_folder = public_path('formated_img/small_avatar/');
	$picture_guid = Webpatser\Uuid\Uuid::generate(4).".jpg";

	//copy("https://unsplash.it/300/300/?random", "$img_folder/$picture_guid");
	copy("http://loremflickr.com/300/300", "$img_folder/$picture_guid");
	
	$image = new \App\Libraries\Image\Image;
	$image->resize($img_folder, $picture_guid, 120, 120, $thumb_folder);
	
	return [
		'login_name' => $faker->userName,
		'passw' => 'S0mep@ss',
		'password' => Hash::make('S0mep@ss'),
		'email' => $faker->email,
		'display_name' => "$firstName $lastName",
		'position_title' => $faker->jobTitle,
		'remember_token' => str_random(10),
		'picture_name' => "$firstName-$lastName.jpg",
		'picture_guid' => $picture_guid,
		'birth_date' => $faker->dateTimeBetween('-30 years', '-20 years')->format('Y-m-d'),
		'valid_from' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
		'department_id' => $faker->numberBetween(1, 4),
		'description' => $faker->realText(500),
		'phone' => $faker->e164PhoneNumber,
		'mobile' => $mobile,
		'fax' => $faker->e164PhoneNumber,
		'is_blocked' => 0,
		'source_id' => $faker->numberBetween(1, 4),
		'manager_id' => 1,
		'office_address' => $faker->address,
		'office_cabinet' => $faker->numberBetween(1, 50),
		'first_name' => $firstName,
		'last_name' => $lastName,
		'person_code' => $faker->randomNumber(8),
		'is_leader' => 0,
		'gender_id' => $gender,
		'country_id' => $faker->numberBetween(1, 8),
		'employment_status_id' => $faker->numberBetween(1, 3),
		'position_id' => $faker->numberBetween(1, 7),
		'team_id' => $faker->numberBetween(3, 4),
		'job_type_id' => $faker->numberBetween(1, 2),
		'reporting_manager_id' => 1,
		'location_country_id' => $faker->numberBetween(1, 8),
		'timezone_id' => 1,
		'join_date' => $faker->dateTimeBetween('-2 years', '-1 year')->format('Y-m-d'),
		'prob_term_date' => $faker->dateTimeBetween('-1 year', '-6 months')->format('Y-m-d'),
		'location_type_id' => $faker->numberBetween(1, 2),
		'location_city' => $faker->city,
		'reg_addr_country_id' => $faker->numberBetween(1, 8),
		'reg_addr_city' => $faker->city,
		'reg_addr_street' => $faker->streetAddress,
		'reg_addr_zip' => $faker->postcode,
		'curr_addr_country_id' => $faker->numberBetween(1, 8),
		'curr_addr_city' => $faker->city,
		'curr_addr_street' => $faker->streetAddress,
		'curr_addr_zip' => $faker->postcode,
		'workphone1' => $faker->e164PhoneNumber,
		'workphone2' => $faker->e164PhoneNumber,
		'other_email' => $faker->email,
		'skype' => $faker->userName,
		'viber' => $mobile,
		'whatsapp' => $mobile,
		'telegram' => $mobile,
		'emergency_contacts' => $faker->phoneNumber,
	];
});