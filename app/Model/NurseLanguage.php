<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Language;

class NurseLanguage extends Model
{
    use SoftDeletes;
    protected $table = 'nurse_language';
    protected $guarded = ['id'];


    public function languages()
    {
        return $this->hasMany(Language::class, 'id', 'language_id');
    }
}
