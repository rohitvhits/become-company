<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryReply extends Model
{
    use SoftDeletes;
    protected $table = 'enquiry_reply';
    protected $guarded = ['id'];

    public function userDetails(){
        return $this->hasOne(User::class,'id','created_by')->where('delete_flag','N');
    }
}
