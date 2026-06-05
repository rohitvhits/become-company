<?php

namespace App\Services;

use App\Model\VNSProcedureResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VNSProcedureResultService
{
    /**
     * Get all active procedure results
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProcedureResults()
    {
        return VNSProcedureResult::where('del_flag', 'N')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get procedure result by ID
     *
     * @param int $id
     * @return VNSProcedureResult
     */
    public function getProcedureResultById($id)
    {
        return VNSProcedureResult::where('id', $id)
            ->where('del_flag', 'N')
            ->firstOrFail();
    }

    /**
     * Create a new procedure result
     *
     * @param array $data
     * @return VNSProcedureResult
     */
    public function createProcedureResult(array $data)
    {
        try {
            DB::beginTransaction();

            $procedureResult = VNSProcedureResult::create([
                'vns_procedure_id' => $data['vns_procedure_id'],
                'name' => $data['name'],
                'created_date' => now(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return $procedureResult;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing procedure result
     *
     * @param int $id
     * @param array $data
     * @return VNSProcedureResult
     */
    public function updateProcedureResult($id, array $data)
    {
        try {
            DB::beginTransaction();

            $procedureResult = $this->getProcedureResultById($id);

            $procedureResult->update([
                'vns_procedure_id' => $data['vns_procedure_id'],
                'name' => $data['name'],
                'updated_date' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return $procedureResult;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Soft delete a procedure result
     *
     * @param int $id
     * @return bool
     */
    public function deleteProcedureResult($id)
    {
        try {
            DB::beginTransaction();

            $procedureResult = $this->getProcedureResultById($id);

            $procedureResult->update([
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
     * Get procedure results list with pagination for AJAX
     *
     * @param array $search
     * @param bool $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getList($search, $paginate = true)
    {
        $query = VNSProcedureResult::select(
                'vns_procedure_results.*',
                'vns_procedure.procedure_name',
                'users.first_name',
                'users.last_name'
            )
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'vns_procedure_results.created_by');
            })
            ->leftJoin('vns_procedure', function($join) {
                $join->on('vns_procedure.id', '=', 'vns_procedure_results.vns_procedure_id');
            })
            ->where('vns_procedure_results.del_flag', "N");

        if (isset($search['name']) && $search['name'] != "") {
            $query->where('vns_procedure_results.name', 'like', "%{$search['name']}%");
        }

        if (isset($search['vns_procedure_id']) && $search['vns_procedure_id'] != "") {
            $query->where('vns_procedure_results.vns_procedure_id', $search['vns_procedure_id']);
        }

        if (isset($search['procedure_name']) && $search['procedure_name'] != "") {
            $query->where('vns_procedure.procedure_name', 'like', "%{$search['procedure_name']}%");
        }

        $query->orderBy('vns_procedure_results.id', 'desc');

        return $paginate ? $query->paginate(50) : $query->get();
    }

    /**
     * Get procedure results by VNS Procedure ID
     *
     * @param int $vnsProcedureId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResultsByProcedureId($vnsProcedureId)
    {
        return VNSProcedureResult::where('vns_procedure_id', $vnsProcedureId)
            ->where('del_flag', 'N')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Check if result name already exists for a procedure
     *
     * @param string $name
     * @param int $vnsProcedureId
     * @param int|null $excludeId
     * @return bool
     */
    public function isResultNameExists($name, $vnsProcedureId, $excludeId = null)
    {
        $query = VNSProcedureResult::where('name', $name)
            ->where('vns_procedure_id', $vnsProcedureId)
            ->where('del_flag', "N");

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get procedure result count
     *
     * @return int
     */
    public function getProcedureResultCount()
    {
        return VNSProcedureResult::where('del_flag', 'N')->count();
    }

    /**
     * Restore a soft-deleted procedure result
     *
     * @param int $id
     * @return bool
     */
    public function restoreProcedureResult($id)
    {
        try {
            DB::beginTransaction();

            $procedureResult = VNSProcedureResult::where('id', $id)
                ->where('del_flag', "Y")
                ->firstOrFail();

            $procedureResult->update([
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
     * Get procedure wise result list
     *
     * @return int
     */
    public function fetchResultByProcedureId($procedureId)
    {
        return VNSProcedureResult::select('id','name')->where('del_flag', 'N')->where('vns_procedure_id',$procedureId)->get();
    }
}
