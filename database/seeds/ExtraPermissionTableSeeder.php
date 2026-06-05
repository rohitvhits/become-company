<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ExtraPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'filter-assign-user',
         ];
      
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
