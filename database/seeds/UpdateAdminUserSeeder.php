<?php

use App\User;
use Illuminate\Database\Seeder;

class UpdateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::find(1)->update([
            'role' => "Raw",
            'role_multi' => "Raw,Clean,Type,SFX,Check"
        ]);

        
    }
}
