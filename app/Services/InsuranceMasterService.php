<?php
namespace App\Services;

use App\Model\InsuranceMaster;
use Illuminate\Support\Facades\Auth;

class InsuranceMasterService
{
    public function getData($insurance_name)
    {
        $where = 'del_flag ="N"';

        if ($insurance_name != '') {
            $where .= ' and insurance_name LIKE "%' . $insurance_name . '%"';
        }

        $query = InsuranceMaster::whereRaw($where)->orderBy('id', 'desc')->paginate(50);
        return $query;
    }

    public function save($data)
    {
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by'] = auth()->user()->id;
        $insert = new InsuranceMaster($data);
        $insert->save();

        return $insert;
    }

    public function getDetailById($id)
    {
        $query = InsuranceMaster::where('del_flag','N')->where('id', $id)->first();
        return $query;
    }

    public function update($data, $where)
    {
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $update = InsuranceMaster::where($where)->update($data);
        return $update;
    }

    public function delete($id)
    {
        $data = [
            'deleted_date'=>date('Y-m-d H:i:s'),
            'deleted_by'=>Auth::id(),
            'del_flag'=>"Y",
        ];
        $response = InsuranceMaster::where('id',$id)->update($data);
        return $response;
    }

    /******Use for Cronjob and other module */
    public function getInsuranceMasterList()
    {
        $query = InsuranceMaster::where('del_flag','N')->orderBy('insurance_name','asc')->get();

        return $query;
    }

    public function getDetailsByInsuranceName($name){
        return InsuranceMaster::select('id', 'insurance_name')
                    ->where('insurance_name', 'LIKE', '%' . $name . '%')
                    ->where('del_flag', 'N')
                    ->first();
    }

    public function saveWithCron($data)
    {
        return InsuranceMaster::create($data);
    }
}