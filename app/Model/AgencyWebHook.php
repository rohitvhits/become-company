<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyWebHook extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $table = 'agency_webhook';
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
    
    public function updatedUser()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
