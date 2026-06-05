<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    protected $table = 'country';
    protected $fillable = ['id', 'name', 'status', 'delflag', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'blocked_at', 'unblocked_at', 'blocked_by', 'unblocked_by'];

    public static function getAllData($name, $status)
    {
        $temp = 'delflag = "N" ';
        if ($name != '') {
            $temp .= ' and name LIKE "%' . $name . '%"';
        }
        if ($status != '') {
            $temp .= ' and status  LIKE "%' . $status . '%"';
        }
        $query = Country::whereRaw($temp)->orderBy('id', 'desc')->where('delflag', 'N')->paginate(10);
        return $query;
    }
}
