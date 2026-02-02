<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{

    public function run()
    {
        Designation::create(['name' => 'Executive Director']);
        Designation::create(['name' => 'Senior General Manager']);
        Designation::create(['name' => 'Deputy General Manager']);
        Designation::create(['name' => 'Assistant General Manager']);
        Designation::create(['name' => 'Senior Manager']);
        Designation::create(['name' => 'Manager']);
        Designation::create(['name' => 'Deputy Manager']);
        Designation::create(['name' => 'Assistant  Manager']);
        Designation::create(['name' => 'Senior Executive']);
        Designation::create(['name' => 'Executive']);
        Designation::create(['name' => 'Junior Executive']);
        Designation::create(['name' => 'Management Trainee']);
        Designation::create(['name' => 'Production Officer']);
        Designation::create(['name' => 'APO']);
    }
}
