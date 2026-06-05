<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmmacareReferalTable extends Model
{
    use SoftDeletes;
    protected $table = 'emmacare_referal_table';
    protected $guarded = ['id'];

    public function userDetaials(){
        return $this->belongsTo(User::class,'created_by','id');
    }
}
