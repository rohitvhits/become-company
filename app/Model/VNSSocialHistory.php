<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Template;

class VNSSocialHistory extends Model
{
    use HasFactory;

    protected $table = 'vns_social_history';

    public $timestamps = false;

    protected $fillable = [
        'template_id',
        'name',
        'default_value',
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
     * Get the template that owns the social history
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id', 'id');
    }
}
