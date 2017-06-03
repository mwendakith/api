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
            ['user_type_id' => '1', 'name' => 'administrator', 'email' => 'admin@gmail.com', 'password' => bcrypt('loremipsum')],
            ['user_type_id' => '1', 'name' => 'joel', 'email' => 'joelkith@gmail.com', 'password' => bcrypt('loremipsum')],
            ['user_type_id' => '1', 'name' => 'joshua', 'email' => 'baksajoshua09@gmail.com', 'password' => bcrypt('joshua-eid-password')],
		    ['user_type_id' => '1', 'name' => 'tim', 'email' => 'tngugi@gmail.com', 'password' => bcrypt('tim-eid-password')],

		]);
    }
}
