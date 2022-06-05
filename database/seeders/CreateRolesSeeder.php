<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use DB;
use Carbon\Carbon;
class CreateRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
        $roles = [
            ['name' => 'Doctor','guard_name'=>'web','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],
        	['name' => 'SubAdmin','guard_name'=>'web','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],
        	['name' => 'Patient','guard_name'=>'web','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],  
            ['name' => 'Receptionist','guard_name'=>'web','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],      	
        ];
        DB::table('roles')->insert($roles);
        
    }
}
