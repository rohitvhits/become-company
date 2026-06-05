<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Agency;
use App\Master;

class BranchListLink extends Model
{
    protected $table = "branch_list_link";

    protected $fillable = [
        'branch_id',
        'agency_id',
        'service_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'del_flag'
    ];

    public function branch()
    {
        return $this->belongsTo(BranchList::class, 'branch_id', 'id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Master::class, 'service_id', 'id');
    }

    public function createdUsers()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
