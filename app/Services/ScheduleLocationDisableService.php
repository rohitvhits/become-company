<?php

namespace App\Services;

use App\Model\ScheduleLocationDisable;

class ScheduleLocationDisableService
{
    public function save($data)
    {
        $auth = auth()->user();
        $locationId = $data['location_id'];
        $status = $data['status'];
        // Normalize request dates
        $requestDates = collect(explode(',', $data['dates']))
            ->map(fn($date) => trim($date))
            ->filter()
            ->map(fn($date) => date('Y-m-d', strtotime($date)))
            ->unique()
            ->values();
        // Get existing dates only (faster than full records)
        $existingDates = ScheduleLocationDisable::where('location_id', $locationId)
            ->pluck('disable_date');
        $now = now();
        // Dates to Insert
        $datesToInsert = $requestDates->diff($existingDates);
        if ($datesToInsert->isNotEmpty()) {
            $insertData = $datesToInsert->map(function ($date) use ($locationId, $status, $auth, $now) {
                return [
                    'location_id'  => $locationId,
                    'disable_date' => $date,
                    'status'       => $status,
                    'deleted_flag' => 'N',
                    'created_by'   => $auth->id,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            })->toArray();
            ScheduleLocationDisable::insert($insertData); // bulk insert
        }

        // Dates to Update (existing ones in request)
        $datesToUpdate = $requestDates->intersect($existingDates);

        if ($datesToUpdate->isNotEmpty()) {
            ScheduleLocationDisable::where('location_id', $locationId)
                ->whereIn('disable_date', $datesToUpdate)
                ->update([
                    'status'       => $status,
                    'deleted_flag' => 'N',
                    'updated_by'   => $auth->id,
                    'updated_at'   => $now,
                ]);
        }

        // Dates to Soft Delete (existing but NOT in request)
        $datesToDelete = $existingDates->diff($requestDates);
        if ($datesToDelete->isNotEmpty()) {
            ScheduleLocationDisable::where('location_id', $locationId)
                ->whereIn('disable_date', $datesToDelete)
                ->update([
                    'status'       => $status,
                    'deleted_flag' => 'Y',
                    'deleted_by'   => $auth->id,
                    'deleted_at'   => $now,
                ]);
        }
        return true;
    }

    public function getByLocationId($locationId)
    {
        return ScheduleLocationDisable::where('location_id', $locationId)
            ->where('deleted_flag', 'N')
            ->get();
    }

    public static function getLocationDisableForSchedule()
    {
        $records = ScheduleLocationDisable::query()
            ->select('location_id', 'disable_date','status')
            ->where('deleted_flag','N')
            ->get();
        $result = [];
        foreach ($records as $row) {
            // Convert ANY valid date format into d-m-Y
            $formattedDate = date('d-m-Y', strtotime($row->disable_date));
            $result[$row->location_id.'_'.$row->status][] = $formattedDate;
        }
        return $result;
    }
}
