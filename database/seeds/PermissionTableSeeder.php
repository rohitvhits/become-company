<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'user-view',
            'user-export',
            'user-send-invitation',

            'calendar-list',

            'agency-list',
            'agency-add',
            'agency-edit',
            'agency-delete',
            'agency-view',
            'agency-export',
            'agency-add-user',
            'agency-generate-token',
            'agency-add-domain',
            'agency-edit-domain',
            'agency-delete-domain',

            'doctor-list',
            'doctor-add',
            'doctor-edit',
            'doctor-delete',
            'doctor-export',

            'language-list',
            'language-add',
            'language-edit',
            'language-delete',

            'template-list',
            'template-add',
            'template-edit',
            'template-delete',
            'template-view',
            'template-copy',
            'template-status',
            'template-document-type',
            'template-singer',

            'location-list',
            'location-add',
            'location-edit',
            'location-delete',
            'location-schedule',

            'task-list',
            'task-add',
            'task-status',
            'task-view',
            'task-export',
            'task-edit',
            'task-delete',

            'appointments-list',
            'appointments-add',
            'appointments-export',
            'appointments-archive',
            'appointments-view',
            'appointments-edit',
            'appointments-delete',
            'appointments-schedule',

            'appointments-pending',
            'appointments-upcomming',
            'appointments-cancel',
            'appointments-refused',
            'appointments-archived',

            'request-list',

            'field-master-list',
            'field-master-create',
            'field-master-edit',
            'field-master-delete',
            'field-master-show',

            'form-setup-list',
            'form-setup-create',
            'form-setup-edit',
            'form-setup-delete',
            'form-setup-show',
            'form-setup-template-link',

            'agency-create-form',
            'agency-form-setup',
            'agency-create-form-add-new-field',
            'agency-create-form-add-custom-field',
            'agency-create-form-show',
            'agency-create-form-delete',
            'agency-form-setup-show',
            'agency-form-setup-add-new-field',
            'agency-form-setup-add-custom-field',
            'agency-form-wise-field-show',
            'agency-form-wise-field-delete',

            'advance-form-list',
            'agency-all-form-list',
            'agency-all-form-create',
            'agency-all-form-edit',
            'agency-all-form-download',

            'agency-all-form-move-to-esign',
            'agency-all-form-mark-as-completed',
            'invoice-upload-list',
            'invoice-upload-add',
            'invoice-upload-delete',
            'hha-get-patient-changes-v2',

            'employee-dashboard',
            'rating-master-list',
            'rating-master-create',
            'rating-master-edit',
            'rating-master-delete',
            'rating-master-show',

            'form-group-list',
            'form-group-create',
            'form-group-edit',
            'form-group-delete',
            'form-group-show',
            
            'service-request-list',
            'service-request-create',
            'service-request-edit',
            'service-request-delete',

         ];
      
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
