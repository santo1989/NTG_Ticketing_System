<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //before running this seeder, make sure to off the gate key  in AuthServiceProvider.php

        // 1 - Admin
        Role::create(['name' => 'Admin']);

        // 2 - General (client)
        Role::create(['name' => 'General']);

        // 3 - Supervisor
        Role::create(['name' => 'Supervisor']);

        // 4 - Support
        Role::create(['name' => 'Support']);

        // 5 - Client (alias for ticket users)
        Role::create(['name' => 'Client']);
    }
}
