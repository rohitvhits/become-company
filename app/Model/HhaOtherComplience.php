<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class HhaOtherComplience extends Model
{
    public $timestamps = false;
    protected $table = 'hha_other_complience';
    protected $guarded = ["id"];

    public static  function insertData($data)
    {
        $insertData = $data;
        $inserId = new HhaOtherComplience($insertData);
        $inserId->save();
        $Insert = $inserId->id;

        return $Insert;
    }
    public static  function updateData($data, $where)
    {
        $insert = HhaOtherComplience::where($where)->update($data);
        return $insert;
    }
    public static  function getDataByMedicalID($medicalId)
    {
        $query = HhaOtherComplience::where('medical_id', $medicalId)->where('del_flag', 'N')->first();
        return $query;
    }
}