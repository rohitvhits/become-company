<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class DocApprovalQuestions extends Model
{
    protected $table = "doc_approval_questions";
    protected $fillable = ['id','question','type','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'del_flag'];

    public function user(){
        return $this->belongsTo(User::class,'created_by','id');
    }
}
