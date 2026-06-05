<?php
namespace App\Services;

use App\Model\WriteDocument;

class WriteDocumentService
{
    protected const COMMON_DATEYMD='Y-m-d H:i:s';
    public function save($data)
    {
        $auth = auth()->user();
        $data['created_at'] = date(self::COMMON_DATEYMD);
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";
   
        $insert = new WriteDocument($data);
        return $insert->save();
    }

    public function update($data, $where)
    {
        $auth = auth()->user();
        $data['updated_at'] = date(self::COMMON_DATEYMD);
        $data['updated_by'] = $auth['id'];
        return WriteDocument::where($where)->update($data);
    }
    
    public function softDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date(self::COMMON_DATEYMD);
        $data['deleted_by'] = $auth['id'];
        
        return WriteDocument::where($where)->update($data);
    }
    
}