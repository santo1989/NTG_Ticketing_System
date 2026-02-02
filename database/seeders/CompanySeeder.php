<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //1
        Company::create([
            'name' => 'TIL - Head Office'
        ]);
        //2
        Company::create([
            'name' => 'TIL - Factory'
        ]);

        //3
        Company::create([
            'name' => 'FAL - Factory'
        ]);
        //4
        Company::create([
            'name' => 'NCL - Factory'
        ]);
        //5
        Company::create([
            'name' => 'TIL - Fabric'
        ]);
        //6
        Company::create([
            'name' => 'NCL - Fabric'
        ]);
        //7
        Company::create([
            'name' => 'FAL - Head Office'
        ]);
        //8
        Company::create([
            'name' => 'NCL - Head Office'
        ]);
    }
}
