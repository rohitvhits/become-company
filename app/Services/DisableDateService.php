<?php
namespace App\Services;
use App\Model\DisableDate;

class DisableDateService{

    protected const DATE_FORMAT_YMDHIS = 'Y-m-d H:i:s';
    public function disableDateList()
    {
        $query = DisableDate::select('disable_date.id','disable_dates','time','users.first_name','users.last_name','disable_date.created_at')
        ->leftjoin('users',function($join){
            $join->on('users.id','=','disable_date.created_by');
            $join->where('users.delete_flag','N');
        })->where('deleted_flag','N');
        $query = $query->orderBy('disable_date.id','desc')->paginate(50);
        return $query;
    }

    public  function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date(self::DATE_FORMAT_YMDHIS);
        $data['created_by'] = $auth['id'];
        $insert = new DisableDate($data);
        $insert->save();
        return $insert->id;
    }
    public  function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date(self::DATE_FORMAT_YMDHIS);
        $data['updated_by'] = $auth['id'];
        return DisableDate::where($where)->update($data);
    }

    public  function softDelete($where)
    {
        $auth = auth()->user();
        $data['deleted_at'] = date(self::DATE_FORMAT_YMDHIS);
        $data['deleted_by'] = $auth['id'];
        $data['deleted_flag'] = 1;
        return DisableDate::where($where)->update($data);
    }

    public function getDetailById($id)
    {
        $query = DisableDate::where('id', $id);
        $query = $query->first();
        return $query;
    }

    public function disableDateAllData($type="")
    {
        $query = DisableDate::where('deleted_flag', 'N')->whereNull('time');
        if($type !=""){
            $query->whereRaw('(type="'.$type.'" OR type IS NULL)');
        }
        $query = $query->pluck('disable_dates');

        return $query;
    }

    public function disableDateAllDataWithTime($type="")
    {
        $query = DisableDate::where('deleted_flag', 'N')->whereNotNull('time');
        if($type !=""){
            $query->whereRaw('(type="'.$type.'" OR type IS NULL)');
        }
        $query = $query->pluck('disable_dates','time');

        return $query;
    }
}
