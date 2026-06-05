<?php

namespace App\Services;

use App\Model\AgencyNote;

class AgencyNoteService
{
    public function getByAgency($agencyId)
    {
        return AgencyNote::where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getActiveByAgency($agencyId)
    {
        return AgencyNote::where('agency_id', $agencyId)
            ->where('del_flag', 'N')
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function addNote($data)
    {
        $auth = auth()->user();
        $data['created_by']      = $auth->id;
        $data['created_by_name'] = $auth->first_name . ' ' . $auth->last_name;
        $note = new AgencyNote($data);
        $note->save();
        return $note;
    }

    public function softDelete($id)
    {
        return AgencyNote::where('id', $id)->update(['del_flag' => 'Y','deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => auth()->user()->id]);
    }

    public function toggleActive($id)
    {
        $note = AgencyNote::find($id);
        if (!$note) return null;
        $note->is_active = $note->is_active ? 0 : 1;
        $note->save();
        return $note;
    }
}
