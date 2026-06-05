<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VNSProcedureResult extends Model
{
    use HasFactory;

    protected $table = 'vns_procedure_results';

    public $timestamps = false;

    protected $fillable = [
        'vns_procedure_id',
        'name',
        'del_flag',
        'created_date',
        'created_by',
        'updated_date',
        'updated_by',
        'deleted_date',
        'deleted_by'
    ];

    protected $casts = [
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
        'deleted_date' => 'datetime',
    ];

    /**
     * Get the VNS Procedure that owns the result
     */
    public function vnsProcedure()
    {
        return $this->belongsTo(VNSProcedure::class, 'vns_procedure_id', 'id');
    }
}
