<?php

use Illuminate\Database\Seeder;

class UserTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_types')->insert([
		    ['id' => '1', 'user_type' => 'Super Administrator', 'description' => 'This is the root user of the system.'],
		    ['id' => '2', 'user_type' => 'Administrator', 'description' => 'This is for the Administrator.'],
		    ['id' => '3', 'user_type' => 'National', 'description' => 'This is for national level.'],
		    ['id' => '4', 'user_type' => 'Partner', 'description' => 'This is for partner level.'],
		    ['id' => '5', 'user_type' => 'County', 'description' => 'This is for county level.'],
		    ['id' => '6', 'user_type' => 'Subcounty', 'description' => 'This is for subcounty level.'],
		    ['id' => '7', 'user_type' => 'Site', 'description' => 'This is for site level.'],
		    ['id' => '8', 'user_type' => 'Lab', 'description' => 'This is for lab level.']

		]);
    }
}
