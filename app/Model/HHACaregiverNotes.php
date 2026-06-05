<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class HHACaregiverNotes extends Model
{
	use Notifiable; 
	public $timestamps =false;
	protected $table = 'hha_caregivers_notes';
	protected $fillable = ['id','CaregiverNoteID','CaregiverID','NoteDate','Note','del_flag', 'created_date','created_by'];

	
}
