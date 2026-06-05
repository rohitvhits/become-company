<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormGroup extends Model
{
    use SoftDeletes;
    protected $table = 'form_group';
    protected $guarded = ['id'];

}
