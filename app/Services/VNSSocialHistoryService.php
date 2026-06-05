<?php

namespace App\Services;

use App\Model\VNSSocialHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VNSSocialHistoryService
{


    /**
     * Get all active social history records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSocialHistory()
    {
        return VNSSocialHistory::where('del_flag', 'N')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get social history record by ID
     *
     * @param int $id
     * @return VNSSocialHistory
     */
    public function getSocialHistoryById($id)
    {
        return VNSSocialHistory::where('id', $id)
            ->where('del_flag', 'N')
            ->firstOrFail();
    }

    /**
     * Create a new social history record
     *
     * @param array $data
     * @return VNSSocialHistory
     */
    public function createSocialHistory(array $data)
    {
        try {
            DB::beginTransaction();

            $socialHistory = VNSSocialHistory::create([
                'template_id' => $data['template_id'],
                'name' => $data['name'],
                'default_value' => $data['default_value'] ?? null,
                'created_date' => now(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return $socialHistory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing social history record
     *
     * @param int $id
     * @param array $data
     * @return VNSSocialHistory
     */
    public function updateSocialHistory($id, array $data)
    {
        try {
            DB::beginTransaction();

            $socialHistory = $this->getSocialHistoryById($id);

            $socialHistory->update([
                'template_id' => $data['template_id'],
                'name' => $data['name'],
                'default_value' => $data['default_value'] ?? null,
                'updated_date' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return $socialHistory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Soft delete a social history record
     *
     * @param int $id
     * @return bool
     */
    public function deleteSocialHistory($id)
    {
        try {
            DB::beginTransaction();

            $socialHistory = $this->getSocialHistoryById($id);

            $socialHistory->update([
                'del_flag' => "Y",
                'deleted_date' => now(),
                'deleted_by' => Auth::id(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get social history list with pagination for AJAX
     *
     * @param array $search
     * @param bool $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getList($search, $paginate = true)
    {
        $query = VNSSocialHistory::select(
                'vns_social_history.*',
                'template_master.template_name',
                'users.first_name',
                'users.last_name'
            )
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'vns_social_history.created_by');
            })
            ->leftJoin('template_master', function($join) {
                $join->on('template_master.id', '=', 'vns_social_history.template_id');
            })
            ->where('vns_social_history.del_flag', "N");

        if (isset($search['name']) && $search['name'] != "") {
            $query->where('vns_social_history.name', 'like', "%{$search['name']}%");
        }

        if (isset($search['template_id']) && $search['template_id'] != "") {
            $query->where('vns_social_history.template_id', $search['template_id']);
        }

        if (isset($search['template_name']) && $search['template_name'] != "") {
            $query->where('template_master.template_name', 'like', "%{$search['template_name']}%");
        }

        $query->orderBy('vns_social_history.id', 'desc');

        return $paginate ? $query->paginate(50) : $query->get();
    }

    /**
     * Get social history records by template ID
     *
     * @param int $templateId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHistoryByTemplateId($templateId)
    {
        return VNSSocialHistory::where('template_id', $templateId)
            ->where('del_flag', 'N')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Check if social history name already exists for a template
     *
     * @param string $name
     * @param int $templateId
     * @param int|null $excludeId
     * @return bool
     */
    public function isNameExists($name, $templateId, $excludeId = null)
    {
        $query = VNSSocialHistory::where('name', $name)
            ->where('template_id', $templateId)
            ->where('del_flag', "N");

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get social history count
     *
     * @return int
     */
    public function getSocialHistoryCount()
    {
        return VNSSocialHistory::where('del_flag', 'N')->count();
    }

    /**
     * Restore a soft-deleted social history record
     *
     * @param int $id
     * @return bool
     */
    public function restoreSocialHistory($id)
    {
        try {
            DB::beginTransaction();

            $socialHistory = VNSSocialHistory::where('id', $id)
                ->where('del_flag', "Y")
                ->firstOrFail();

            $socialHistory->update([
                'del_flag' => "N",
                'deleted_date' => null,
                'deleted_by' => null,
                'updated_date' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get template wise social history list
     *
     * @param int $templateId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fetchHistoryByTemplateId($templateId)
    {
        return VNSSocialHistory::select('id','name')
            ->where('del_flag', 'N')
            ->where('template_id', $templateId)
            ->get();
    }
}
