<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes;
    protected $table = 'enquiry';
    protected $guarded = ['id'];

    public function usersDetail(){
        return $this->hasOne(User::class,'id','created_by')->where('delete_flag','N');
    }
}
