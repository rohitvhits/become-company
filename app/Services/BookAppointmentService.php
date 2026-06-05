<?php
namespace App\Services;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Model\BulkViewAppointment;
use App\Helpers\Utility;
class BookAppointmentService{

    protected const START_TIME = " 00:00:00";
    protected const END_TIME =" 23:59:59";
    public static function getList($search=[],$paginate=""){
        $query=  BulkViewAppointment::select('id','full_name','phone','email','agency_name','service_name','county','book_date','created_date')->where('del_flag','N');
        if(isset($search['full_name']) && $search['full_name'] !=""){
            $query->where('full_name','LIKE','%'.$search['full_name'].'%');
        }
        if(isset($search['mobile_no']) && $search['mobile_no'] !=""){
            $query->where('phone',$search['mobile_no']);
        }
        if(isset($search['book_date']) && $search['book_date'] !=""){
            $explode = explode('-',$search['book_date']);
            $query->where('book_date','>=',Utility::convertYMD($explode[0]))->where('book_date','<=',Utility::convertYMD($explode[1]));
        }
        if(isset($search['created_date']) && $search['created_date'] !=""){
            $explode = explode('-',$search['created_date']);
            $query->where('created_date','>=',Utility::convertYMD($explode[0]).' '.self::START_TIME)->where('created_date','<=',Utility::convertYMD($explode[1]).' '.self::END_TIME);
        }
        if($paginate !=""){
            return $query->orderBy('id','desc')->get();
        }else{
            return  $query->orderBy('id','desc')->paginate(50);
        }
    }
}