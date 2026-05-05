<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'=>'Admin User',
            'email'=>'admin@example.com',
            'username'=>'admin',
            'password'=>Hash::make('password'),
            'gender'=>'Male',
            'image'=>'avatar-01.jpg',
            'status'=>1,
            'designation_id'=>1,
            'remarks'=>'System Admin'
        ]);

        User::create([
            'name'=>'Rahul Sharma',
            'email'=>'rahul@example.com',
            'username'=>'rahul123',
            'password'=>Hash::make('password'),
            'gender'=>'Male',
            'image'=>'avatar-02.jpg',
            'status'=>1,
            'designation_id'=>2,
            'remarks'=>'User'
        ]);

        User::create([
            'name'=>'Priya Das',
            'email'=>'priya@example.com',
            'username'=>'priya123',
            'password'=>Hash::make('password'),
            'gender'=>'Female',
            'image'=>'avatar-03.jpg',
            'status'=>1,
            'designation_id'=>2,
            'remarks'=>'User'
        ]);
    }
}