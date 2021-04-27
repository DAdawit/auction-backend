<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'name'=>'admin',
            'email'=>'admin@gmail.com',
            'role'=>'admin',
            'password'=>'password'
        ]);
        DB::table('users')->insert([
            'name'=>'s_admin',
            'email'=>'sadmin@gmail.com',
            'role'=>'s_admin',
            'password'=>bcrypt('password')
        ]);
        DB::table('users')->insert([
            'name'=>'dawit',
            'email'=>'dawit@gmail.com',
            'role'=>'user',
            'password'=>bcrypt('password')
        ]);
        DB::table('users')->insert([
            'name'=>'kasu',
            'email'=>'kasu@gmail.com',
            'role'=>'user',
            'password'=>bcrypt('password')
        ]);
        DB::table('users')->insert([
            'name'=>'girma',
            'email'=>'girma@gmail.com',
            'role'=>'user',
            'password'=>bcrypt('password')
        ]);
        DB::table('users')->insert([
            'name'=>'lala',
            'email'=>'lala@gmail.com',
            'role'=>'user',
            'password'=>bcrypt('password')
        ]);
        DB::table('categories')->insert([
           'category_name'=>'car'
        ]);
        DB::table('categories')->insert([
            'category_name'=>'home'
        ]);
        DB::table('categories')->insert([
            'category_name'=>'book'
        ]);

    }
}
