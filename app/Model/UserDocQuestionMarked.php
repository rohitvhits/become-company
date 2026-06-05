<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserDocQuestionMarked extends Model
{
    protected $table = "user_doc_question_marked";
    public $timestamps = false;
    protected $fillable = ['id','question_id','user_id','created_date', 'updated_date', 'deleted_date', 'created_by', 'updated_by', 'deleted_by', 'del_flag'];
}
