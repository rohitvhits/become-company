<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            'permission-list',
            'permission-create',
            'permission-edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
