<?php
namespace App\Services;

use App\Model\AnnouncementUser;
use Illuminate\Support\Facades\Auth;
use App\User;

class AnnouncementUserService
{

    public static function save($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
        $insert = new AnnouncementUser($data);
        $insert->save();
        return $insert;
    }

    public function getUserWiseAnnouncement($user_id){
       return AnnouncementUser::where('deleted_flag','N')->where('user_id',$user_id)->where('mark_as_read',0)->first();
    }

    public function markAsReadUser($announcement_id){
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $data['mark_as_read'] = 1;
        $update = AnnouncementUser::where('id', $announcement_id)->update($data);
        return $update;
    }

    public function getyAnnouncementData(){
        return AnnouncementUser::with('announcementDetail:id,title,description')->where('announcement_user.del_flag','N')->where('user_id',auth()->user()->id)->where('mark_as_read',0)->orderBy('announcement_user.id','desc')->first();
    } 
    
    public function getAnnouncementOfUser($from_date,$to_date,$perPage,$page){
        $query = AnnouncementUser::with('announcementDetail:id,title,description','userDetails:id,first_name,last_name')->select('announcement_id','user_id','mark_as_read','id')->where('announcement_user.del_flag','N')->where('mark_as_read',0)->where('user_id',auth()->user()->id);
        if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
        $query = $query->orderBy('announcement_user.created_date','desc')->paginate($perPage, ['*'], 'page', $page);
        return $query;
    }

    public function getAnnouncementOfUserId($from_date,$to_date,$perPage,$page,$user_id){        
        $query = AnnouncementUser::with('announcementDetail:id,title,description','userDetails:id,first_name,last_name')->select('announcement_id','user_id','mark_as_read','id')->where('announcement_user.del_flag','N')->where('mark_as_read',0);

        if(!empty($user_id)){
            $query->where('user_id',$user_id);
        } 
        if(!empty($from_date) && !empty($to_date)){
			$query->whereBetween('created_date', [$from_date.' 00:00:00', $to_date.' 23:59:59']);
		}
        $query = $query->orderBy('announcement_user.created_date','desc')->paginate($perPage, ['*'], 'page', $page);
        return $query;
    }
}