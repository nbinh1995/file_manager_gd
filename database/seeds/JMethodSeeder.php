<?php

use Illuminate\Database\Seeder;
use App\Models\JMethod;

class JMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(JMethod::class, 10)->create();
    }
}
