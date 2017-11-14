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
        DB::table('users')->insert([
            'email' => 'leo.rudin@gmx.ch',
            'password' => bcrypt('secret'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
