<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agency_files';

    protected $fillable = [
        'agency_id',
        'folder_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(\App\Agency::class, 'agency_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(AgencyFolder::class, 'folder_id')->withTrashed();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_files.agency_id', $agencyId);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getIsPreviewableAttribute(): bool
    {
        return in_array($this->file_type, ['jpg', 'jpeg', 'png', 'gif', 'pdf']);
    }
}
