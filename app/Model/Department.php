<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Department extends Model
{
    protected $table = "departments";

    protected $fillable = [
        'name',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'del_flag'
    ];

    /**
     * Get users assigned to this department
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'department_user', 'department_id', 'user_id')->withPivot('del_flag')->wherePivot('del_flag', 'N');
    }

    /**
     * Get all departments with user count
     */
    public static function getAllWithUserCount()
    {
        return self::withCount('users')->orderBy('name', 'asc')->get();
    }

    /**
     * Get users assigned to this department
     */
    public function createdUsers()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

}
