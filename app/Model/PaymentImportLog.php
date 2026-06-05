<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentImportLog extends Model
{
    use SoftDeletes;

    protected $table = 'payment_import_logs';
    protected $guarded = ['id'];

    protected $casts = [
        'error_log' => 'array',
        'uploaded_at' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
