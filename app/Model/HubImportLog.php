<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Model\HubCompany;

class HubImportLog extends Model
{
    protected $table = 'hub_import_logs';
    public $timestamps = false;

    protected $fillable = [
        'agency_id',
        'file_name',
        'file_path',
        'unique_fields',
        'total_records',
        'inserted_count',
        'updated_count',
        'failed_count',
        'inactive_count',
        'status',
        'error_details',
        'created_by',
        'created_date'
    ];

    protected $casts = [
        'unique_fields' => 'array',
        'error_details' => 'array'
    ];

    public function agency()
    {
        return $this->belongsTo(HubCompany::class, 'agency_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}