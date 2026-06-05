<?php
namespace App;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
class MasterType extends Model

{
    use Notifiable;

  protected $table = 'master_type';
  protected $fillable = ['id', 'name'];

}

