<?php

namespace App\Helpers;
use App\Model\HHACaregiverNotes;
class HHACaregiverNotesHelper
{
    public function __construct()
    {
    }

    public static  function insert($data)
    {
		$insert_data = $data; 
		$inser_id = new HHACaregiverNotes($insert_data);
		$inser_id->save();
		$Insert = $inser_id->id; 

		return $Insert;
	
		
    }

    public static function getCaregiverNotesList($caregiverId){
        return  HHACaregiverNotes::select('CaregiverNoteID')->where('del_flag','N')->get();

    }
}