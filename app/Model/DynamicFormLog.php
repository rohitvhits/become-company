<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class DynamicFormLog extends Model
{
    use SoftDeletes;
    protected $table = 'dynamic_form_logs';
    protected $guarded = ['id'];

    public function userDetails(){
        return $this->hasOne(User::class,"id","created_by");
    }
}
