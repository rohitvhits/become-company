<?php
 
namespace App\Model;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class HHAPocTask extends Model
{
    use HasFactory;
    protected $table = "hha_poc_task";
    protected $guarded = ["id"];
}
 