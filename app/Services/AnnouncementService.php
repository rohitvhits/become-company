<?php
namespace App\Services;

use App\Model\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementService
{
    public function getData()
    {
        $where = 'del_flag ="N"';
        $query = Announcement::whereRaw($where)->orderBy('id', 'desc')->paginate(50);
        return $query;
    }

    public function save($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = auth()->user()->id;
        $insert = new Announcement($data);
        $insert->save();

        return $insert;
    }

    public function getDetailById($id)
    {
        $query = Announcement::where('del_flag','N')->where('id', $id)->first();
        return $query;
    }

    public function update($data, $where)
    {
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $update = Announcement::where($where)->update($data);
        return $update;
    }

    public function delete($id)
    {
        $data = [
            'deleted_date'=>date('Y-m-d H:i:s'),
            'deleted_by'=>Auth::id(),
            'del_flag'=>"Y",
        ];
        $response = Announcement::where('id',$id)->update($data);
        return $response;
    }
}