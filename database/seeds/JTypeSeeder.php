<?php

use Illuminate\Database\Seeder;
use App\Models\JType;

class JTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(JType::class, 10)->create();
    }
}
