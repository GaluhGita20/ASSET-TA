<?php

namespace Database\Seeders;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder2 extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'name'   => 'perubahan-perencanaan',
                'actions' => ['view', 'create', 'edit', 'delete', 'approve'],
            ],
        ];

        foreach ($permissions as $permission) {
            foreach ($permission['actions'] as $action) {
                $permissionName = $permission['name'] . '.' . $action;
                Permission::updateOrCreate(['name' => $permissionName]);
            }
        }
    }

    
}