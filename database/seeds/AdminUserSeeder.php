<?php

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'name' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'active' => true,
            'is_admin' => true,
        ]);
    }
}
