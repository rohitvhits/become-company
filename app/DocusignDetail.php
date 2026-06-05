<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class DocusignDetail extends Model
{
    use Notifiable;

    public $timestamps = false;
    protected $table = 'docusign_detail';
    protected $fillable = ['id', 'document_report_id', 'template_id', 'user_id','data','docWidth', 'created_date', 'del_flag', 'temp_img','updated_flag'];
   
	
}
