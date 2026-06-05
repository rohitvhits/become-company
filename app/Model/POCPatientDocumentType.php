<?php
 
namespace App\Model;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class PocMatchedTask extends Model
{
    use HasFactory;
    protected $table = "poc_matched_tasks";
    protected $guarded = ["id"];
}