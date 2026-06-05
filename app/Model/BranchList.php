<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Agency;

class BranchList extends Model
{
    protected $table = "branch_list";

    protected $fillable = [
        'branch_name',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'del_flag'
    ];

    public function createdUsers()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function branchLinks()
    {
        return $this->hasMany(BranchListLink::class, 'branch_id', 'id')->where('del_flag', 'N');
    }
}
