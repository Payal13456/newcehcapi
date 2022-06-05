<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class CreateDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
    {
        $roles = [
            ['role_id' => '2','dept_name'=>'Receptionist','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],
        	['role_id' => '2','dept_name'=>'Optometric','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],
        	['role_id' => '2','dept_name'=>'Manager','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],  
          ];
        DB::table('departments')->insert($roles);
        
    }
}
