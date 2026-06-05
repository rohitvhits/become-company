<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\User;
class ImportCsvFileRecord extends Model
{
    use Notifiable;

    protected $table = 'import_csv_file_record';
    public $timestamps = false;
    protected $guarded = ["id"];

    public function userDetail(){
        return $this->belongsTo(User::class,'created_by','id');
    }
}
