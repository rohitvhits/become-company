<?php
 
namespace App\Model;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class VisitTaskHealth extends Model
{
    use HasFactory;
    protected $table = "visit_task_health";
    protected $guarded = ["id"];
}