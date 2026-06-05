<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPasswordData extends Model
{
    protected $table = 'user_pass_data';
    protected $fillable = ['id', 'user_id', 'password', 'created_at', 'created_by', 'updated_at',  'updated_by', 'deleted_at', 'deleted_by'];
}
