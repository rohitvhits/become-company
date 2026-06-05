<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class DocumentSignerMaster extends Model
{
    use Notifiable;

    protected $table = 'document_signer_master';
    protected $fillable = ['id', 'template_id', 'name', 'user_id', 'office_id','created_date','created_by'];
    public $timestamps = false;
 
}
