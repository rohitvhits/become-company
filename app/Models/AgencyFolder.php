<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyFolder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agency_folders';

    protected $fillable = [
        'agency_id',
        'parent_id',
        'name',
        'is_mdo',
        'is_telehealth',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(\App\Agency::class, 'agency_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AgencyFolder::class, 'parent_id')->withTrashed();
    }

    public function children(): HasMany
    {
        return $this->hasMany(AgencyFolder::class, 'parent_id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function files(): HasMany
    {
        return $this->hasMany(AgencyFile::class, 'folder_id');
    }

    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    public function scopeRootFolders($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getBreadcrumbAttribute(): array
    {
        $breadcrumb = [];
        $folder = $this;

        while ($folder) {
            array_unshift($breadcrumb, [
                'id' => $folder->id,
                'name' => $folder->name,
            ]);
            $folder = $folder->parent;
        }

        return $breadcrumb;
    }
}
