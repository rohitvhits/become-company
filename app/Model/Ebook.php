<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ebook extends Model
{
    use SoftDeletes;
    protected $table = 'ebook';
    protected $guarded = ['id'];
}
