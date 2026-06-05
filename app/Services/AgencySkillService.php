<?php

namespace App\Services;

use App\Model\AgencySkill;

class AgencySkillService
{
    protected const COMMONYMD = "Y-m-d H:i:s";

    public function save($data)
    {
        $auth = auth()->user();
        $data['created_date'] = date(self::COMMONYMD);
        $data['created_by'] = $auth['id'];
        $data['del_flag'] = "N";

        $insert = new AgencySkill($data);
        return $insert->save();
    }

    public function update($data, $where)
    {
        $data['updated_date'] = date(self::COMMONYMD);

        if (isset(auth()->user()->id)) {
            $data['updated_by'] = auth()->user()->id;
        }

        return AgencySkill::where($where)->update($data);
    }

    public function SoftDelete($data, $where)
    {
        $auth = auth()->user();
        $data['deleted_date'] = date(self::COMMONYMD);
        $data['deleted_by'] = $auth['id'];

        return AgencySkill::where($where)->update($data);
    }

    public function getSkillByAgencyId($agencyId)
    {
        return AgencySkill::where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->pluck('skill_id');
    }

    /************** use for cronjob function name syncDueSkill() */
    public function getAllDueSkillList()
    {
        return AgencySkill::where('del_flag', 'N')
            ->orderBy('last_sync_date', 'asc')
            ->first();
    }

    public function runningDueSkill()
    {
        return AgencySkill::where('current_status', 'running')->where('del_flag', 'N')
            ->orderBy('last_sync_date', 'asc')
            ->first();
    }

    public function getSkillByWithDeletedAgencyId($agencyId)
    {
        return AgencySkill::where('agency_id', $agencyId)
            ->pluck('skill_id', 'id');
    }
}
