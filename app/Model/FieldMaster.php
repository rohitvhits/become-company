<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldMaster extends Model
{
    use SoftDeletes;
    protected $table = 'field_masters';
    protected $guarded = ['id'];

    protected $casts = [
        'options' => 'array',
        'show_in_portal' => 'boolean'
    ];

}
