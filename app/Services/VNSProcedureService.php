<?php

namespace App\Services;

use App\Model\VNSProcedure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VNSProcedureService
{
    /**
     * Get all active procedures
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProcedures()
    {
        return VNSProcedure::where('del_flag', 'N')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get procedure by ID
     *
     * @param int $id
     * @return VNSProcedure
     */
    public function getProcedureById($id)
    {
        return VNSProcedure::where('id', $id)
            ->where('del_flag', 'N')
            ->firstOrFail();
    }

    /**
     * Create a new procedure
     *
     * @param array $data
     * @return VNSProcedure
     */
    public function createProcedure(array $data)
    {
        try {
            DB::beginTransaction();

            $procedure = VNSProcedure::create([
                'procedure_name' => $data['procedure_name'],
               
                'created_date' => now(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return $procedure;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing procedure
     *
     * @param int $id
     * @param array $data
     * @return VNSProcedure
     */
    public function updateProcedure($id, array $data)
    {
        try {
            DB::beginTransaction();

            $procedure = $this->getProcedureById($id);

            $procedure->update([
                'procedure_name' => $data['procedure_name'],
               
                'updated_date' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return $procedure;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Soft delete a procedure
     *
     * @param int $id
     * @return bool
     */
    public function deleteProcedure($id)
    {
        try {
            DB::beginTransaction();

            $procedure = $this->getProcedureById($id);

            $procedure->update([
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
     * Check if procedure name already exists
     *
     * @param string $procedureName
     * @param int|null $excludeId
     * @return bool
     */
    public function isProcedureNameExists($procedureName, $excludeId = null)
    {
        $query = VNSProcedure::where('procedure_name', $procedureName)
            ->where('del_flag',"N");

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get procedures by template type
     *
     * @param string $templateType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProceduresByTemplateType($templateType)
    {
        return VNSProcedure::where('template_type', $templateType)
            ->where('del_flag', "N")
            ->orderBy('procedure_name', 'asc')
            ->get();
    }

    /**
     * Search procedures
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchProcedures(array $filters)
    {
        $query = VNSProcedure::where('del_flag', "N");

        if (!empty($filters['procedure_name'])) {
            $query->where('procedure_name', 'like', '%' . $filters['procedure_name'] . '%');
        }

        if (!empty($filters['template_type'])) {
            $query->where('template_type', $filters['template_type']);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * Get procedures list with pagination for AJAX
     *
     * @param array $search
     * @param bool $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getList($search, $paginate = true)
    {
        $query = VNSProcedure::select('vns_procedure.*', 'users.first_name', 'users.last_name', 'template_master.template_name')
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'vns_procedure.created_by');
            })
            ->leftJoin('template_master', function($join) {
                $join->on('template_master.id', '=', 'vns_procedure.template_type');
            })
            ->where('vns_procedure.del_flag', "N");

        if (isset($search['procedure_name']) && $search['procedure_name'] != "") {
            $query->where('vns_procedure.procedure_name', 'like', "%{$search['procedure_name']}%");
        }

        if (isset($search['template_type']) && $search['template_type'] != "") {
            $query->where('vns_procedure.template_type', 'like', "%{$search['template_type']}%");
        }

        $query->orderBy('vns_procedure.id', 'desc');

        return $paginate ? $query->paginate(50) : $query->get();
    }

    /**
     * Get procedure count
     *
     * @return int
     */
    public function getProcedureCount()
    {
        return VNSProcedure::where('del_flag', 0)->count();
    }

    /**
     * Restore a soft-deleted procedure
     *
     * @param int $id
     * @return bool
     */
    public function restoreProcedure($id)
    {
        try {
            DB::beginTransaction();

            $procedure = VNSProcedure::where('id', $id)
                ->where('del_flag', "Y")
                ->firstOrFail();

            $procedure->update([
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

    public function getProcedueListByTemplateTypeID(){
        return VNSProcedure::select('id','procedure_name')->where('del_flag','N')->orderBy('id','asc')->get();
    }
    
}
