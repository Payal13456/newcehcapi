<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'employee-list',
           'employee-create',
           'employee-edit',
           'employee-delete',
           'hospital-list',
           'hospital-create',
           'hospital-edit',
           'hospital-delete',  
           'patient-list',
           'patient-create',
           'patient-edit',
           'patient-delete',
           'notification-list',
           'notification-create',
           'notification-edit',
           'notification-delete',
           'category-list',
           'category-create',
           'category-edit',
           'category-delete',
           'blog-list',
           'blog-create',
           'blog-edit',
           'blog-delete',
           'faq-list',
           'faq-create',
           'faq-edit',
           'faq-delete',
           'plan-list',
           'plan-create',
           'plan-edit',
           'plan-delete',
           'promocode-list',
           'promocode-create',
           'promocode-edit',
           'promocode-delete',
           'policy-list',
           'policy-create',
           'policy-edit',
           'policy-delete',
           'appointment-list',
           'appointment-create',
           'appointment-edit',
           'appointment-delete',
           'schedule-list',
           'schedule-create',
           'schedule-edit',
           'schedule-delete'
        ];

        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
