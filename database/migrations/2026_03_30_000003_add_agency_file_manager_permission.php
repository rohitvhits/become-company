<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddAgencyFileManagerPermission extends Migration
{
    public function up()
    {
        // Create the permission
        $permission = Permission::firstOrCreate(
            ['name' => 'agency-file-manager'],
            ['guard_name' => 'web']
        );

        // Assign to all existing roles so admins and agency users can access
        $roles = Role::all();
        foreach ($roles as $role) {
            if (!$role->hasPermissionTo('agency-file-manager')) {
                $role->givePermissionTo($permission);
            }
        }
    }

    public function down()
    {
        $permission = Permission::where('name', 'agency-file-manager')->first();
        if ($permission) {
            // Remove from all roles first
            $permission->roles()->detach();
            $permission->delete();
        }
    }
}
