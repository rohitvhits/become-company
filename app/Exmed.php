<?php
namespace App;

class Exmed {
   const ROLES = array("Super Admin","EMC User","Agency Admin","Agency Rep");
   const RECORD_STATUS = array("Open"=>array("color-class"=>'info'),"Closed"=>array("color-class"=>'inverse'),"More Info Needed"=>array("color-class"=>'warning'),"Complete"=>array("color-class"=>'success'));
   const MEDICAID_ISSUE = array("Code Removal","Market Place","New Application","Pool Trust","Recertification","Rebudgets");
}