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
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'active' => true,
            'is_admin' => true,
            'role' => "Raw",
            'role_multi' => "Raw,Clean,Type,SFX,Check"
        ]);

        // \App\User::create([
        //     'username' => 'raw',
        //     'email' => 'raw@admin.com',
        //     'password' => \Illuminate\Support\Facades\Hash::make('password'),
        //     'active' => true,
        //     'role' => "Raw",
        //     'role_multi' => "Raw"
        // ]);

        // \App\User::create([
        //     'username' => 'clean',
        //     'email' => 'clean@admin.com',
        //     'password' => \Illuminate\Support\Facades\Hash::make('password'),
        //     'active' => true,
        //     'role' => "Clean",
        //     'role_multi' => "Clean"
        // ]);

        // \App\User::create([
        //     'username' => 'type',
        //     'email' => 'type@admin.com',
        //     'password' => \Illuminate\Support\Facades\Hash::make('password'),
        //     'active' => true,
        //     'role' => "Type",
        //     'role_multi' => "Type"
        // ]);

        // \App\User::create([
        //     'username' => 'sfx',
        //     'email' => 'sfx@admin.com',
        //     'password' => \Illuminate\Support\Facades\Hash::make('password'),
        //     'active' => true,
        //     'role' => "SFX",
        //     'role_multi' => "SFX"
        // ]);

        // \App\User::create([
        //     'username' => 'check',
        //     'email' => 'check@admin.com',
        //     'password' => \Illuminate\Support\Facades\Hash::make('password'),
        //     'active' => true,
        //     'role' => "Check",
        //     'role_multi' => "Check"
        // ]);
    }
}
