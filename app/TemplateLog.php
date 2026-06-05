<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TemplateLog extends Model
{
    use Notifiable; 

    protected $table = 'template_log';
    public $timestamps = false;
    protected $fillable = ['id','template_id','user_id','response','created_date', 'created_by', 'del_flag', 'docWidth','old_response'];
    
    public function users()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
