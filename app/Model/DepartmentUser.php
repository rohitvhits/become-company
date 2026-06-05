<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DepartmentUser extends Model
{
    protected $table = "department_user";

    protected $fillable = [
        'department_id',
        'user_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'del_flag'
    ];
}
