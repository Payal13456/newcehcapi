<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        $this->call(CreateDepartmentSeeder::class);
        $this->call(CreateRolesSeeder::class);
        $this->call(CreateSuperAdminSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(StateTableDataSeeder::class);
        // \App\Models\User::factory(10)->create();
    }
}
