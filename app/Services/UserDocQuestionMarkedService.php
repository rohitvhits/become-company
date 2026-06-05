<?php

namespace App\Services;

use App\Model\UserDocQuestionMarked;

class UserDocQuestionMarkedService
{

    public static function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date('Y-m-d H:i:s');
        if (isset($auth['id'])) {
            $data['created_by'] = $auth['id'];
        }
        $insert = new UserDocQuestionMarked($data);
        $insert_id = $insert->save();
        return $insert_id;
    }
    public static function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $auth['id'];

        $update = UserDocQuestionMarked::where($where)->update($data);
        return $update;
    }
    public static function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by'] = $auth['id'];

        $update = UserDocQuestionMarked::where($where)->update($data);
        return $update;
    }

    public static function getAllQuesions()
    {
        $query = UserDocQuestionMarked::where('del_flag','N')->orderBy('id', 'desc')->get();
        return  $query;
    }
}
