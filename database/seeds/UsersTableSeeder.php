<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('administrators')->insert([
		    ['user_type_id' => '1', 'name' => 'administrator', 'email' => 'admin@gmail.com', 'password' => bcrypt('loremipsum')]

		]);
    }
}
