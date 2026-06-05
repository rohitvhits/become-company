<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class AppointmentImportFile extends Model
{
    use Notifiable;
    public $timestamps = false;
    protected $table = 'appointment_import_file';
    protected $fillable = ['id','agency_id','file','delete_flag','created_date','created_by','extension','upload_file','file_name'];

    /**
     * Get the import records for this import file
     */
    public function importRecords()
    {
        return $this->hasMany(ImportCsvFileRecord::class, 'import_file_id', 'id')
                    ->where('del_flag', 'N');
    }

    /**
     * Get the agency relationship
     */
    public function agency()
    {
        return $this->belongsTo(\App\Agency::class, 'agency_id', 'id');
    }

    /**
     * Get the creator user relationship
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }
}
