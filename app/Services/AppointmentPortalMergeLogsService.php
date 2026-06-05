<?php

namespace App\Services;

use App\Model\AppointmentPortalMergeLogs;
use App\Model\Patient;

class AppointmentPortalMergeLogsService
{
    public function save($data)
    {
        $auth = auth()->user();

        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by']   = $auth['id'];
        $data['del_flag']     = 'N';

        $insert = new AppointmentPortalMergeLogs($data);
        $insert->save();

        return $insert->id;
    }

    public function softDelete($data, $where)
    {
        $auth = auth()->user();

        $data['deleted_date'] = date('Y-m-d H:i:s');
        $data['deleted_by']   = $auth['id'];

        return AppointmentPortalMergeLogs::where($where)->update($data);
    }

    public function checkAnyExistingMergeAppointmentId($recordId,$mergeId){
       
        return AppointmentPortalMergeLogs::where('main_patient_id',$recordId)->where('merge_patient_id',$mergeId)->where('del_flag','N')->first();
    }

    public function getActiveRecordList($recordId){
        return AppointmentPortalMergeLogs::select('appointment_portal_merge_logs.id','appointment_portal_merge_logs.created_date','appointment_portal_merge_logs.merge_patient_id','appointment_portal_merge_logs.merge_depth','users.first_name','users.last_name')
            ->leftjoin('users',function($join){
                $join->on('users.id','=','appointment_portal_merge_logs.created_by');
            })->where(function($query) use ($recordId){
                $query->where('appointment_portal_merge_logs.main_patient_id',$recordId)
                      ->orWhere('appointment_portal_merge_logs.root_patient_id',$recordId)
                      ->orWhereRaw('FIND_IN_SET(?, appointment_portal_merge_logs.merge_path)', [$recordId]);
            })->where('appointment_portal_merge_logs.del_flag','N')->orderBy('appointment_portal_merge_logs.merge_depth','asc')->paginate(50);
    }

    public function getDeletedRecordList($recordId){
        return AppointmentPortalMergeLogs::select('appointment_portal_merge_logs.id','appointment_portal_merge_logs.created_date','appointment_portal_merge_logs.main_patient_id','appointment_portal_merge_logs.merge_patient_id','users.first_name','users.last_name')
            ->leftjoin('users',function($join){
                $join->on('users.id','=','appointment_portal_merge_logs.created_by');
            })->whereRaw('appointment_portal_merge_logs.del_flag = "N" and (appointment_portal_merge_logs.merge_patient_id ="'.$recordId.'" OR appointment_portal_merge_logs.main_patient_id ="'.$recordId.'")')->paginate(50);
    }

    public function getDetailsById($id){
        return AppointmentPortalMergeLogs::where('id',$id)->where('del_flag','N')->first();
    }
    
    public function getMergePatientDetailsById($id){
        return AppointmentPortalMergeLogs::where('merge_patient_id',$id)->where('del_flag','N')->get();
    }

    public function getMainPortalIds($id){
        return AppointmentPortalMergeLogs::where('main_patient_id',$id)->where('del_flag','N')->get();
    }

    /**
     * Get the root (ultimate parent) appointment ID for a given appointment
     * Handles nested merge chains (e.g., A -> B -> C returns C)
     *
     * @param int $appointmentId
     * @return int|null The root appointment ID or null if not merged
     */
    public function getRootAppointment($appointmentId)
    {
        // First check if this appointment is a merged child
        $mergeLog = AppointmentPortalMergeLogs::where('merge_patient_id', $appointmentId)
            ->where('del_flag', 'N')
            ->first();

        if (!$mergeLog) {
            // Not merged, so it's either a root or standalone
            return $appointmentId;
        }

        // If root_patient_id is set, use it (optimized path)
        if ($mergeLog->root_patient_id) {
            return $mergeLog->root_patient_id;
        }

        // Fallback: traverse the chain (for legacy data)
        $currentId = $mergeLog->main_patient_id;
        $visited = [$appointmentId]; // Prevent infinite loops
        $maxDepth = 100; // Safety limit
        $depth = 0;

        while ($depth < $maxDepth) {
            $parentLog = AppointmentPortalMergeLogs::where('merge_patient_id', $currentId)
                ->where('del_flag', 'N')
                ->first();

            if (!$parentLog) {
                // Reached the root
                return $currentId;
            }

            if (in_array($parentLog->main_patient_id, $visited)) {
                // Circular reference detected, return current
                return $currentId;
            }

            $visited[] = $parentLog->main_patient_id;
            $currentId = $parentLog->main_patient_id;
            $depth++;
        }

        return $currentId;
    }

    /**
     * Get all children (merged appointments) for a given appointment
     * Returns both direct and nested children
     *
     * @param int $appointmentId
     * @param bool $directOnly If true, only return direct children
     * @return \Illuminate\Support\Collection
     */
    public function getAllChildren($appointmentId, $directOnly = false)
    {
        if ($directOnly) {
            // Get only direct children
            return AppointmentPortalMergeLogs::where('main_patient_id', $appointmentId)
                ->where('del_flag', 'N')
                ->get();
        }

        // Get all descendants (nested children)
        $allChildren = collect();
        $toProcess = [$appointmentId];
        $processed = [];
        $maxDepth = 100; // Safety limit

        $depth = 0;
        while (!empty($toProcess) && $depth < $maxDepth) {
            $currentId = array_shift($toProcess);

            if (in_array($currentId, $processed)) {
                continue;
            }

            $processed[] = $currentId;

            $children = AppointmentPortalMergeLogs::where('main_patient_id', $currentId)
                ->where('del_flag', 'N')
                ->get();

            foreach ($children as $child) {
                $allChildren->push($child);
                $toProcess[] = $child->merge_patient_id;
            }

            $depth++;
        }

        return $allChildren;
    }

    /**
     * Check if merging child into parent would create a circular reference
     * Prevents scenarios like: A->B, then trying B->A
     *
     * @param int $childId The appointment to be merged
     * @param int $parentId The appointment to merge into
     * @return bool True if circular, false if safe
     */
    public function wouldCreateCircularMerge($childId, $parentId)
    {
        if ($childId === $parentId) {
            return true; // Self-merge
        }

        // Check if parent is already a descendant of child
        $childRoot = $this->getRootAppointment($childId);
        $parentRoot = $this->getRootAppointment($parentId);

        // If child is already in parent's chain
        if ($childRoot === $parentId || $childId === $parentRoot) {
            return true;
        }

        // Check if parent is in child's descendant tree
        $childDescendants = $this->getAllChildren($childId);
        foreach ($childDescendants as $descendant) {
            if ($descendant->merge_patient_id == $parentId) {
                return true;
            }
        }

        // Check if child is in parent's ancestor chain
        $currentId = $parentId;
        $visited = [];
        $maxDepth = 100;
        $depth = 0;

        while ($depth < $maxDepth) {
            $parentLog = AppointmentPortalMergeLogs::where('merge_patient_id', $currentId)
                ->where('del_flag', 'N')
                ->first();

            if (!$parentLog) {
                break;
            }

            if ($parentLog->main_patient_id == $childId) {
                return true; // Child is parent's ancestor
            }

            if (in_array($parentLog->main_patient_id, $visited)) {
                break;
            }

            $visited[] = $parentLog->main_patient_id;
            $currentId = $parentLog->main_patient_id;
            $depth++;
        }

        return false;
    }

    /**
     * Get the full merge chain for an appointment
     * Returns array of IDs from root to current
     *
     * @param int $appointmentId
     * @return array
     */
    public function getMergeChain($appointmentId)
    {
        $chain = [];
        $currentId = $appointmentId;
        $visited = [];
        $maxDepth = 100;
        $depth = 0;

        // Build chain from current to root
        while ($depth < $maxDepth) {
            $mergeLog = AppointmentPortalMergeLogs::where('merge_patient_id', $currentId)
                ->where('del_flag', 'N')
                ->first();

            if (!$mergeLog) {
                // Reached root
                array_unshift($chain, $currentId);
                break;
            }

            if (in_array($currentId, $visited)) {
                // Circular reference
                break;
            }

            $visited[] = $currentId;
            array_unshift($chain, $currentId);
            $currentId = $mergeLog->main_patient_id;
            $depth++;
        }

        // Add root if not already added
        if (!empty($chain) && $chain[0] !== $currentId) {
            array_unshift($chain, $currentId);
        }

        return $chain;
    }

    /**
     * Update merge chain metadata when a new merge occurs
     * Updates root_patient_id, parent_patient_id, merge_depth, and merge_path
     *
     * @param int $childId
     * @param int $parentId
     * @return void
     */
    public function updateMergeChainMetadata($childId, $parentId)
    {
        // Get the root of the parent
        $rootId = $this->getRootAppointment($parentId);

        // Get parent's merge log to determine depth
        $parentLog = AppointmentPortalMergeLogs::where('merge_patient_id', $parentId)
            ->where('del_flag', 'N')
            ->first();

        $parentDepth = $parentLog ? $parentLog->merge_depth : 0;
        $newDepth = $parentDepth + 1;

        // Build merge path
        $parentPath = $parentLog ? ($parentLog->merge_path ?: $rootId) : $rootId;
        $newPath = $parentPath . ',' . $parentId;

        // Update the newly created merge log
        AppointmentPortalMergeLogs::where('merge_patient_id', $childId)
            ->where('main_patient_id', $parentId)
            ->where('del_flag', 'N')
            ->update([
                'root_patient_id' => $rootId,
                'parent_patient_id' => $parentId,
                'merge_depth' => $newDepth,
                'merge_path' => $newPath
            ]);

        // Update all descendants of the child
        $this->updateDescendantsMetadata($childId, $rootId);
    }

    /**
     * Update metadata for all descendants when parent chain changes
     *
     * @param int $appointmentId
     * @param int $newRootId
     * @return void
     */
    private function updateDescendantsMetadata($appointmentId, $newRootId)
    {
        $children = $this->getAllChildren($appointmentId, true);

        foreach ($children as $child) {
            // Recalculate depth and path for this child
            $chain = $this->getMergeChain($child->merge_patient_id);
            $depth = count($chain) - 1;
            $path = implode(',', array_slice($chain, 0, -1));

            AppointmentPortalMergeLogs::where('id', $child->id)
                ->update([
                    'root_patient_id' => $newRootId,
                    'merge_depth' => $depth,
                    'merge_path' => $path
                ]);

            // Recursively update this child's descendants
            $this->updateDescendantsMetadata($child->merge_patient_id, $newRootId);
        }
    }

    /**
     * Save with enhanced nested merge support
     *
     * @param array $data
     * @return int
     */
    public function saveWithMetadata($data)
    {
        $auth = auth()->user();

        $data['created_date'] = date('Y-m-d H:i:s');
        $data['created_by']   = $auth['id'];
        $data['del_flag']     = 'N';

        // Set initial metadata if not provided
        if (!isset($data['root_patient_id'])) {
            $data['root_patient_id'] = $this->getRootAppointment($data['main_patient_id']);
        }

        if (!isset($data['parent_patient_id'])) {
            $data['parent_patient_id'] = $data['main_patient_id'];
        }

        if (!isset($data['merge_depth'])) {
            $parentLog = AppointmentPortalMergeLogs::where('merge_patient_id', $data['main_patient_id'])
                ->where('del_flag', 'N')
                ->first();
            $data['merge_depth'] = $parentLog ? ($parentLog->merge_depth + 1) : 1;
        }

        if (!isset($data['merge_path'])) {
            $chain = $this->getMergeChain($data['main_patient_id']);
            $data['merge_path'] = implode(',', $chain);
        }

        $insert = new AppointmentPortalMergeLogs($data);
        $insert->save();

        return $insert->id;
    }

    /**
     * Handle metadata updates when a merge is undone
     * Recalculates chain for any descendants of the unmerged appointment
     *
     * @param int $unmergedAppointmentId The appointment that was unmerged
     * @return void
     */
    public function updateMetadataAfterUnmerge($unmergedAppointmentId)
    {
        // Get all direct children of the unmerged appointment
        $children = $this->getAllChildren($unmergedAppointmentId, true);

        foreach ($children as $child) {
            $childId = $child->merge_patient_id;

            // Check if this child still has a parent (the unmerged appointment is now standalone)
            $parentLog = AppointmentPortalMergeLogs::where('merge_patient_id', $unmergedAppointmentId)
                ->where('del_flag', 'N')
                ->first();

            if ($parentLog) {
                // Unmerged appointment still has a parent, so children inherit that chain
                $newRootId = $parentLog->root_patient_id ?: $parentLog->main_patient_id;
            } else {
                // Unmerged appointment is now standalone, so children's root is the unmerged appointment
                $newRootId = $unmergedAppointmentId;
            }

            // Recalculate metadata for this child and all its descendants
            $chain = $this->getMergeChain($childId);
            $depth = count($chain) - 1;
            $path = implode(',', array_slice($chain, 0, -1));

            AppointmentPortalMergeLogs::where('id', $child->id)
                ->update([
                    'root_patient_id' => $newRootId,
                    'merge_depth' => $depth,
                    'merge_path' => $path
                ]);

            // Recursively update descendants
            $this->updateDescendantsMetadata($childId, $newRootId);
        }
    }

    /**
     * Get all merged appointment IDs that should be stored in merge_appointment_id field
     * This includes all direct children of an appointment
     *
     * @param int $appointmentId
     * @return array Array of child appointment IDs
     */
    public function getMergeAppointmentIds($appointmentId)
    {
        $children = AppointmentPortalMergeLogs::where('main_patient_id', $appointmentId)
            ->where('del_flag', 'N')
            ->pluck('merge_patient_id')
            ->toArray();

        return $children;
    }

    /**
     * Build the comma-separated merge_appointment_id string for an appointment
     * Includes all direct children
     *
     * @param int $appointmentId
     * @return string Comma-separated IDs or empty string
     */
    public function buildMergeAppointmentIdString($appointmentId)
    {
        $ids = $this->getMergeAppointmentIds($appointmentId);
        return !empty($ids) ? implode(',', $ids) : '';
    }

    /**
     * Get all appointments that this appointment has been merged into
     * Used for the merge_appointment_id field of a CHILD appointment
     *
     * @param int $appointmentId
     * @return array Array of parent appointment IDs in the chain
     */
    public function getParentChainIds($appointmentId)
    {
        $parents = [];
        $currentId = $appointmentId;
        $visited = [];
        $maxDepth = 100;
        $depth = 0;

        while ($depth < $maxDepth) {
            $mergeLog = AppointmentPortalMergeLogs::where('merge_patient_id', $currentId)
                ->where('del_flag', 'N')
                ->first();

            if (!$mergeLog) {
                break;
            }

            if (in_array($mergeLog->main_patient_id, $visited)) {
                break;
            }

            $parents[] = $mergeLog->main_patient_id;
            $visited[] = $mergeLog->main_patient_id;
            $currentId = $mergeLog->main_patient_id;
            $depth++;
        }

        return $parents;
    }

    /**
     * Build the merge_appointment_id string for a CHILD appointment
     * For a child, this contains the chain of parents it's been merged into
     *
     * @param int $appointmentId
     * @return string Comma-separated parent IDs
     */
    public function buildChildMergeAppointmentIdString($appointmentId)
    {
        $parentIds = $this->getParentChainIds($appointmentId);
        return !empty($parentIds) ? implode(',', $parentIds) : '';
    }

    /**
     * Sync the merge_appointment_id field in patient_master for an appointment
     * Updates both the appointment and all its descendants
     *
     * @param int $appointmentId
     * @return void
     */
    public function syncMergeAppointmentIdField($appointmentId)
    {
        // For the parent appointment, store all its direct children
        $mergeIds = $this->buildMergeAppointmentIdString($appointmentId);

        Patient::where('id', $appointmentId)
            ->update(['merge_appointment_id' => $mergeIds]);

        // Update all children to reflect their parent chain
        $children = $this->getAllChildren($appointmentId, true);
        foreach ($children as $child) {
            $childMergeIds = $this->buildChildMergeAppointmentIdString($child->merge_patient_id);

            Patient::where('id', $child->merge_patient_id)
                ->update(['merge_appointment_id' => $childMergeIds]);

            // Recursively update descendants
            $this->syncMergeAppointmentIdField($child->merge_patient_id);
        }
    }

    /**
     * Update merge_appointment_id for all appointments in a chain after a merge
     *
     * @param int $childId
     * @param int $parentId
     * @return void
     */
    public function updateMergeAppointmentIdAfterMerge($childId, $parentId)
    {
        // Update the parent appointment - add child to its merge list
        $existingMergeIds = Patient::where('id', $parentId)
            ->value('merge_appointment_id');

        $mergeIdsArray = $existingMergeIds ? explode(',', $existingMergeIds) : [];

        // Add child and all its descendants to parent's merge list
        $allChildIds = [$childId];
        $descendants = $this->getAllChildren($childId);
        foreach ($descendants as $descendant) {
            $allChildIds[] = $descendant->merge_patient_id;
        }

        $mergeIdsArray = array_unique(array_merge($mergeIdsArray, $allChildIds));
        $mergeIdsArray = array_filter($mergeIdsArray); // Remove empty values

        Patient::where('id', $parentId)
            ->update(['merge_appointment_id' => implode(',', $mergeIdsArray)]);

        // Update the child appointment - add parent chain to its merge list
        $parentChain = $this->getParentChainIds($childId);
        Patient::where('id', $childId)
            ->update(['merge_appointment_id' => implode(',', $parentChain)]);

        // Update all descendants of the child
        foreach ($descendants as $descendant) {
            $descendantParentChain = $this->getParentChainIds($descendant->merge_patient_id);
            Patient::where('id', $descendant->merge_patient_id)
                ->update(['merge_appointment_id' => implode(',', $descendantParentChain)]);
        }

        // Update parent's ancestors to include all new children
        $parentAncestors = $this->getParentChainIds($parentId);
        foreach ($parentAncestors as $ancestorId) {
            $ancestorMergeIds = $this->buildMergeAppointmentIdString($ancestorId);
            Patient::where('id', $ancestorId)
                ->update(['merge_appointment_id' => $ancestorMergeIds]);
        }
    }

    /**
     * Update merge_appointment_id for all appointments in a chain after an unmerge
     *
     * @param int $unmergedChildId The appointment that was unmerged
     * @param int $parentId The parent it was unmerged from
     * @return void
     */
    public function updateMergeAppointmentIdAfterUnmerge($unmergedChildId, $parentId)
    {
        // Remove child and its descendants from parent's merge list
        $existingMergeIds = Patient::where('id', $parentId)
            ->value('merge_appointment_id');

        if ($existingMergeIds) {
            $mergeIdsArray = explode(',', $existingMergeIds);

            // Remove the unmerged child
            $mergeIdsArray = array_filter($mergeIdsArray, function($id) use ($unmergedChildId) {
                return $id != $unmergedChildId;
            });

            // Also remove all descendants of the unmerged child
            $descendants = $this->getAllChildren($unmergedChildId);
            foreach ($descendants as $descendant) {
                $mergeIdsArray = array_filter($mergeIdsArray, function($id) use ($descendant) {
                    return $id != $descendant->merge_patient_id;
                });
            }

            Patient::where('id', $parentId)
                ->update(['merge_appointment_id' => implode(',', $mergeIdsArray)]);
        }

        // Update the unmerged child's merge_appointment_id
        // Check if it still has other parents (shouldn't happen but safety check)
        $remainingParents = $this->getParentChainIds($unmergedChildId);
        Patient::where('id', $unmergedChildId)
            ->update(['merge_appointment_id' => implode(',', $remainingParents)]);

        // Update all children of the unmerged appointment
        $this->syncMergeAppointmentIdField($unmergedChildId);

        // Update parent's ancestors
        $parentAncestors = $this->getParentChainIds($parentId);
        foreach ($parentAncestors as $ancestorId) {
            $this->syncMergeAppointmentIdField($ancestorId);
        }
    }

    public function getUniqueParentPatientIds()
    {
        return AppointmentPortalMergeLogs::where('del_flag', 'N')
            ->distinct()
            ->pluck('main_patient_id')
            ->toArray();
    }
}
