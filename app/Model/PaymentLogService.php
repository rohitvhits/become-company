<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentLogService extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_import_logs_service';

    protected $fillable = [
        'payment_log_id',
        'service_name',
        'total_amount',
        'created_by',
        'updated_by',
        'deleted_by',
        'delete_flag'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship with PaymentLog
     */
    public function paymentLog()
    {
        return $this->belongsTo(PaymentLogList::class, 'payment_log_id', 'id');
    }

    /**
     * Relationship with User (creator)
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }

    /**
     * Relationship with User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by', 'id');
    }

    /**
     * Scope to get only non-deleted records
     */
    public function scopeActive($query)
    {
        return $query->where('delete_flag', 'N')->whereNull('deleted_at');
    }
}
