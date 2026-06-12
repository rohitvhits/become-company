<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\GenerateAgencyToken;

class GenerateAgencyTokenHelper
{
    public static function insert($data)
    {
        $insert_data = $data;
        $inser_id = new GenerateAgencyToken($insert_data);
        $inser_id->save();

        return $inser_id->id;
    }

    public static function update($data, $where)
    {
        return GenerateAgencyToken::where($where)->update($data);
    }

    public static function getData($id)
    {
        $temp = ' agency_token.delete_flag="N"';

        if ($id != '') {
            $temp .= ' and agency_token.agency_id ="' . $id . '"';
        }

        return GenerateAgencyToken::select('agency_token.*', 'agency.agency_name')
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'agency_token.agency_id');
                $join->where('agency_token.delete_flag', 'N');
            })
            ->whereRaw($temp)
            ->orderBy('agency_token.id', 'desc')
            ->paginate(50);
    }

    public static function getDataExport($id)
    {
        $temp = ' agency_token.delete_flag="N"';

        if ($id != '') {
            $temp .= ' and agency_token.agency_id ="' . $id . '"';
        }

        return GenerateAgencyToken::select('agency_token.*', 'agency.agency_name')
            ->leftjoin('agency', function ($join) {
                $join->on('agency.id', '=', 'agency_token.agency_id');
                $join->where('agency_token.delete_flag', 'N');
            })
            ->whereRaw($temp)
            ->orderBy('agency_token.id', 'desc')
            ->get();
    }

    public static function checkToken($token)
    {
        return GenerateAgencyToken::where('token', $token)
            ->where('delete_flag', 'N')
            ->first();
    }

    public static function getDetailsById($id)
    {
        return GenerateAgencyToken::where('id', $id)
            ->where('delete_flag', 'N')
            ->first();
    }

    public static function checkTokenAccess($token)
    {
        return GenerateAgencyToken::leftjoin('agency', function ($join) {
            $join->on('agency.id', '=', 'agency_token.agency_id');
            $join->where('show_hub', '=', 1);
        })
            ->where('token', $token)
            ->where('agency_token.delete_flag', 'N')
            ->first();
    }
}
