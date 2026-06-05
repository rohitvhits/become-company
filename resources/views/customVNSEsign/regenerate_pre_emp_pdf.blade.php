<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Medical Report</title>
</head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">

<body style="font-family:helvetica;">

<table style="width:100%; border-collapse: collapse; background: #fff;font-family:helvetica">
    <!-- HEADER -->
  
    <tr>
        <td colspan="2">

            <!-- MAIN TITLE -->
            <table style="border-collapse: collapse; padding-bottom:30px; width:100%">
                <tr>
                
                    <td style="width:40%; padding-right:20px;">
                        <table style="padding:0px -5px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 15px;font-weight: bold;margin-bottom: 30px;text-align: left;">FINAL MEDICAL REPORT</div>
                                    </th>
                                </tr>
                            </thead>
                            
                        </table>
                    </td>
                    <td style="width:10%;">
                        <table style="padding:4px -13px -10px;font-family:helvetica">
                         
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 9px;font-weight: normal;text-align: left;">{{ $newform['report_date'] ?? '' }}</div>
                                    </th>
                                </tr>
                            </thead>
                            
                        </table>
                    </td>
                </tr>
            </table>

            <!-- EXAM INFORMATION & RESULT KEY -->
            <table style="width:100%; border-collapse: collapse; padding-bottom:20px">
                
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style="width:50%; padding-right:20px;">
                        <table style="width:100%; font-family:helvetica; line-height:11px; padding:0px 0px 10px -12px; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="border-bottom:1px solid #000; padding-bottom:2px; font-weight:bold; font-size:12px;">
                                    EXAM INFORMATION
                                    </th>
                                </tr>
                            </thead>
                            
                            
                        </table>
                        
                        <table style="width:100%; border-collapse:collapse;padding:5px -8px;">
                            <tr>
                                <td style="padding-right:2px">
                                    <span style="font-weight:bold;font-size:9px;padding-bottom:11px;font-family:helvetica;">Exam Date:</span><span style="font-size:9px;font-family:helvetica;">&nbsp;{{ $newform['exam_date'] }}</span>
                                
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <span style="font-weight:bold;font-size:9px;padding-bottom:11px;font-family:helvetica;">Result:</span>&nbsp;<span style="font-size:9px;font-family:helvetica;">{{ $newform['exam_result'] }}</span>
                                
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- RIGHT COLUMN -->
                    <td style="width:50%;">
                        <table style="width:100%; font-family:helvetica; line-height:11px; padding:0px -8px 10px -5px; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="border-bottom:1px solid #000; padding-bottom:2px; text-align:left; font-weight:bold; font-size:12px;">
                                        RESULT KEY
                                    </th>
                                </tr>
                            </thead>
                            
                            
                        </table>
                        <table style="width:100%; font-family:helvetica; line-height:11px; padding:3px -8px 1px -5px; border-collapse: collapse;">
                            <tbody style="font-family:helvetica;">
                               
                                <tr>
                                    <td>
                                        <span style="font-size:9px; font-weight:bold; color:green;">Complete:</span>
                                        <span style="font-size:9px;"> Medically qualified</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="font-size:9px; font-weight:bold; color:#ffa500;">Cleared:</span>
                                        <span style="font-size:9px;"> Qualified with comments</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="font-size:9px; font-weight:bold; color:red;">Not cleared:</span>
                                        <span style="font-size:9px;"> Medically disqualified</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            <table style="width:100%; border-collapse: collapse; padding-bottom:20px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style=" padding-right:20px;">
                        <table style="width:100%;padding:2px -8px 10px">
                            <tr>
                                <td style="border-bottom:1px solid #000;">
                                <span style="font-weight:bold;font-size:12px;padding-bottom:14px;font-family:helvetica">PATIENT INFORMATION</span>
                               
                                </td>
                            </tr>
                            
                        </table>
                        <table style="width:100%; border-collapse:collapse;padding:7px -8px;font-family:helvetica;" >
                            <tr>
                                <td style="padding-right:2px;">
                                    <span style="font-weight:bold;font-size:9px;">Name:</span><span style="font-size:9px;">&nbsp;{{ $newform['patient_name'] }}</span>
                                
                                </td>
                                <td style="padding-right:2px">
                                    <span style="font-weight:bold;font-size:9px;padding-bottom:11px;">Phone:</span><span style="font-size:9px;">&nbsp;{{ $newform['phone'] }}</span>
                                
                                </td>
                            </tr>
                            <tr>
                                <td style="line-height:5px; padding:0; margin:0;">
                                    <span style="font-weight:bold; font-size:9px;">Date of birth:</span>
                                    <span style="font-size:9px;">{{ $newform['dob'] }}</span>
                                
                                </td>
                                <td >
                                    <span style="font-weight:bold;font-size:9px;">Email:</span><span style="font-size:9px;">&nbsp;{{ $newform['email'] }}</span>
                                
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="line-height:5px; padding:0; margin:0;">
                                    <span style="font-weight:bold;font-size:9px;">Address:</span><span style="font-size:9px;">&nbsp;{{ $newform['address'] }}</span>
                                
                                </td>
                                
                            </tr>
                            <tr><td>
                                    <span style="font-weight:bold;font-size:9px;">Gender:</span><span style="font-size:9px;">&nbsp;{{ ucfirst($newform['gender']) }}</span>
                                
                                </td></tr>
                            
                        </table>
                    </td>
                </tr>
            </table>

            <table style="width:100%; border-collapse: collapse; padding-bottom:20px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style="padding-right:20px;">
                        <table style="width:100%;padding:3px -9px 10px;font-family:helvetica">
                            <tr>
                                <td style="border-bottom:1px solid #000;">
                                <span style="font-weight:bold;font-size:12px;">VISIT DETAILS</span>
                               
                                </td>
                            </tr>
                           
                        </table>
                      @php
                      $flag =1;
                      $newFlage =1;
                      $lastValue = array_slice($newform['visit_temp'], -1)[0];
                      
                        if(isset($newform['visit_comment'.$lastValue]) && trim($newform['visit_comment'.$lastValue]) !=""){
                            
                            $strlent = strlen($newform['visit_comment'.$lastValue]);
                            if($strlent <=991){
                                $flag=0;
                            }
                        }else{
                            $newFlage=0;
                        }
                      @endphp
                    
                        <table  cellpadding="4" cellspacing="0" width="100%" style="border-collapse: collapse; font-family: helvetica; font-size: 10pt;">
                            <tr style="background-color:#666; color:white;">
                                <th style="width:20%;padding:5px; text-align:left; font-size:10px;">DATE</th>
                                <th style="width:30%;padding:5px; text-align:left; font-size:10px;">PROCEDURE</th>
                                <th style="width:10%;padding:5px; text-align:left; font-size:10px;">RESULT</th>
                                <th style="width:40%;padding:5px; text-align:left; font-size:10px;">COMMENT</th>
                            </tr>

                            @forelse($newform['visit_temp'] as $key=> $ids)

                                @php
                                $hrFlag =0;
                                $visitDate ="";
                                $visit_procedure="";
                                $visit_result="";
                                $visit_comment="";
                                    if(isset($newform['visit_date'.$ids])){
                                        $visitDate =$newform['visit_date'.$ids];
                                    }
                                    if(isset($newform['visit_procedure'.$ids])){
                                        $visit_procedure =$newform['visit_procedure'.$ids];
                                    }
                                    if(isset($newform['visit_result'.$ids])){
                                        $visit_result =$newform['visit_result'.$ids];
                                    }
                                    if(isset($newform['visit_comment'.$ids])){
                                        $visit_comment =$newform['visit_comment'.$ids];
                                    }
                                @endphp
                                @if($visitDate !="" || $visit_procedure !="" || $visit_result !="" || $visit_comment !="")
                                @php
                                $hrFlag =1;
                                @endphp
                                <tr style="font-size:9px;font-family:helvetica">
                                    <td style="width:20%; ">{{$visitDate}}</td>
                                    <td style="width:30%;">@if(trim(strtolower(str_replace(' ','',$visit_procedure))) !='selectprocedurename') {{$visit_procedure}} @endif</td>
                                    <td style="width:11%;">@if(trim(strtolower(str_replace(' ','',$visit_result))) !='selectresult') {{$visit_result}} @endif</td>
                                    <td style="width:39%;">
                                        <span style="white-space: pre-line;">{!! nl2br(e($visit_comment)) !!}</span>
                                    </td>
                                </tr>
                                @endif
                            @if($lastValue !=$ids && $hrFlag ==1)
                            <hr>
                          @endif
                            @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:10px; font-size:12px; border-bottom:1px solid #ddd;">
                                    No visits found.
                                </td>
                            </tr>
                            @endforelse

                           
                        </table>
                    </td>
                </tr>
            </table>
            
           
        </td>
    </tr>
</table>

<table style="width:100%; border-collapse: collapse; background: #fff;page-break-before: always;">
    
    <tr>
        <td colspan="2">

            <!-- MAIN TITLE -->
            <table style="border-collapse: collapse; padding-bottom:30px; width:100%;font-family:helvetica">
                <tr>
                
                    <td style="width:40%; padding-right:20px;">
                        <table style="padding:0px -5px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 15px;font-weight: bold;margin-bottom: 30px;text-align: left;">FINAL MEDICAL REPORT</div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </td>
                    <td style="width:10%;">
                        <table style="padding:4px -13px -10px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 9px;font-weight: normal;text-align: left;">{{ $newform['report_date2'] ?? '' }}</div>
                                    </th>
                                </tr>
                            </thead>
                            
                        </table>
                    </td>
                </tr>
            </table>

            <table style="width:100%; border-collapse: collapse; padding-bottom:20px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style="padding-right:20px;">
                        <table style="width:100%;padding:2px -7px;">
                            <tr>
                                <td style="border-bottom:1px solid #000;">
                                <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">VISIT DETAILS</span>
                               
                                </td>
                            </tr>
                           
                        </table>
                        
                        <table  cellpadding="4" cellspacing="0" width="100%" style="border-collapse: collapse; font-family: helvetica; font-size: 10pt;">
                            <tr style="background-color:#666; color:white;text-align:left;font-size:10px;padding:5px;">
                                <th style="width:20%;">DATE</th>
                                <th style="width:30%;">PROCEDURE</th>
                                <th style="width:10%;">RESULT</th>
                                <th style="width:40%;">COMMENT</th>
                            </tr>
                            @php
                            $lastValue2 = array_slice($newform['temp2'], -1)[0];
                            @endphp
                            @foreach($newform['temp2'] as $key=> $ids2)
                            @php
                            $hr2Flag =0;
                                $visitDate2 ="";
                                $visit_procedure2="";
                                $visit_result2="";
                                $visit_comment2="";
                                if(isset($newform['visit_date_second'.$ids2])){
                                    $visitDate2 =$newform['visit_date_second'.$ids2];
                                }
                                if(isset($newform['visit_procedure_second'.$ids2])){
                                    $visit_procedure2 =$newform['visit_procedure_second'.$ids2];
                                }
                                if(isset($newform['visit_result_second'.$ids2])){
                                    $visit_result2 =$newform['visit_result_second'.$ids2];
                                }
                                if(isset($newform['visit_comment'.$ids])){
                                    $visit_comment2 =$newform['visit_comment_second'.$ids2];
                                }
                            @endphp
                            @if($visitDate2 !="" || $visit_procedure2 !="" || $visit_result2 !="" || $visit_comment2 !="")
                            @php 
                                $hr2Flag=1;
                            @endphp
                            <tr style="font-family:helvetica; font-size:9px;">
                                <td style="width:20%;">{{ $visitDate2 }}</td>
                                <td style="width:30%;">@if(trim(strtolower(str_replace(' ','',$visit_procedure2))) !='selectprocedurename') {{ $visit_procedure2 }} @endif</td>
                                <td style="width:11%;">
                                @if(trim(strtolower(str_replace(' ','',$visit_result2))) !='selectresult') {{$visit_result2}} @endif
                                </td>
                                <td style="width:39%;">

                                {!! nl2br(e($visit_comment2)) !!}
                                </td>
                            </tr>
                            @endif
                            @if($lastValue2 != $ids2 && $hr2Flag ==1)
                            <hr>
                            @endif
                            @endforeach
                           
                            
                        </table>
                    </td>
                </tr>
            </table>
            <table style="width:100%; border-collapse: collapse; padding-bottom:20px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style=" padding-right:20px;">
                        <table style="width:100%;padding:3px -8px;font-family:helvetica">
                            <tr>
                                <td style="border-bottom:1px solid #000;">
                                <span style="font-weight:bold;font-size:10px;">DISPOSITION</span>
                                
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span style="font-weight:bold;">Status:</span><span style="font-size:9px;font-weight:normal;">  {{ $newform['disposition_status']}}</span>
                               </td>
                            </tr>
                            <tr >
                                <td>
                                    <span style="font-weight:bold;">Comments:</span><span style="font-size:9px;font-weight:normal;">
                                    {!! nl2br(e($newform['disposition_comment'])) !!}
                                    </span>
                                 
                                </td>
                            </tr>
                           
                        </table>
                        
                    </td>
                </tr>
            </table>
            
           
        </td>
    </tr>
</table>


<table style="width:100%; border-collapse: collapse; background: #fff;page-break-before: always;">
    <!-- HEADER -->
  
    <tr>
        <td colspan="2">

            <table style="border-collapse: collapse; padding-bottom:30px; width:100%;font-family:helvetica">
                <tr>
                
                    <td style="width:40%; padding-right:20px;">
                        <table style="padding:0px -5px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 15px;font-weight: bold;margin-bottom: 30px;text-align: left;">FINAL MEDICAL REPORT</div>
                                    </th>
                                </tr>
                            </thead>
                            
                        </table>
                    </td>
                    <td style="width:10%;">
                        <table style="padding:4px -13px -10px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                    <div style="font-size: 9px;font-weight: normal;text-align: left;">{{ $newform['report_date3'] ?? '' }}</div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </td>
                </tr>
            </table>

            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style="padding-right:20px;">
                        <table style="width:100%;padding:2px -8px 5px;font-family:helvetica">
                            <tr>
                                <td  style="border-bottom:1px solid #000;">
                                    <span style="font-weight:bold;font-size:10px;">ADDITIONAL INFORMATION</span>
                                    
                                
                                </td>
                            </tr>
                            <tr>
                                <td><span style="font-size:9px;">
                                If you wish to discuss this medical report in more detail, or require additional information
                                    regarding these clinical services, please contact (718) 972-3693
                               
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td ><span style="font-size:9px;"> The procedure(s) and/or examination(s) [Services] provided pursuant to the above order were reviewed
                                            by NY
                                            Best Medical's clinical team, under the supervision of Svetlana Zeltser, NP.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><span style="font-size:9px;">
                                        These services are coordinated and/or provided by or through NY Best Medical. The services are
                                            performed
                                            under the supervision of Svetlana Zeltser, NP, and are performed by individuals who maintain
                                            appropriate
                                            medical licensure, certification, and/or training.
                                        </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="height:14px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="height:14px;">&nbsp;</td>
                            </tr>
                        </table>
                        
                    </td>
                </tr>
            </table>
            <table style="width:100%;padding:6px -8px 5px;font-family:helvetica">
                <tr>
                    <td>
                        <span style="font-size:10px; font-family:helvetica;">&nbsp;&nbsp;&nbsp;Svetlana Zeltser, NP</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-size:10px;padding-bottom:14px;font-family:helvetica">&nbsp;&nbsp;&nbsp;License #</span><span style="font-family:Arial, sans-serif">  {{ $newform['licenseNumber']}}</span>
                    
                    </td>
                </tr>
            </table>
           
        </td>
    </tr>
</table>
<table style="width:100%; border-collapse: collapse; background: #fff;page-break-before: always;">
    <!-- HEADER -->
  
    <tr>
        <td colspan="2">

            <table style="border-collapse: collapse; padding-bottom:30px; width:100%;font-family:helvetica">
                <tr>
                
                    <td style="width:40%; padding-right:20px;">
                        <table style="padding:0px -5px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 15px;font-weight: bold;margin-bottom: 30px;text-align: left;">FINAL MEDICAL REPORT</div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </td>
                    <td style="width:10%;">
                        <table style="padding:4px -13px -10px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 9px;font-weight: normal;text-align: left;">{{ $newform['report_date4'] ?? '' }}</div>
                                    </th>
                                </tr>
                            </thead>
                            
                        </table>
                    </td>
                </tr>
            </table>
            <!-- EXAM INFORMATION & RESULT KEY -->
            <table style="width:100%; border-collapse: collapse; padding-bottom:10px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style="padding-right:20px;">
                        <table style="width:100%;padding:3px -8px;">
                            <tr>
                                <td style="border-bottom:1px solid #000;padding-bottom:10px">
                                <span style="font-weight:bold;font-size:12px;padding-bottom:14px;font-family:helvetica">RESULT DETAILS</span>
                               
                                </td>
                            </tr>
                            
                        </table>
                        <table style="width:100%; border-collapse:collapse;padding:3px -8px" >
                            <tr>
                                <td style="padding-right:2px">
                                    <span style="font-weight:bold;font-size:10px;padding-bottom:11px;font-family:helvetica;">RISK ASSESSMENT</span>
                                
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding-top:10px; font-size:13px;">
                                <span style="font-size:9px;padding-bottom:11px;font-family:helvetica">Questions</span>
                                
                                </td>
                            </tr>
                            @php
                                $cnt =1;
                            @endphp

                            @if(!empty($newform['question_temp'][0]))
                                @foreach($newform['question_temp'] as $qtTemp)
                                    <tr>
                                        <td style="padding-top:10px; font-size:13px;">
                                            <span style="font-size:9px;padding-bottom:11px;font-family:helvetica">{{ $newform['question_name'.$qtTemp]}}</span><br>
                                            <span style="font-size:9px;padding-bottom:11px;font-family:helvetica;"><b>Ans :</b> @if(trim($newform['question_value_'.$qtTemp]) !=""){{$newform['question_value_'.$qtTemp] }}@else - @endif</span>
                                        
                                        </td>
                                    </tr>
                                    @php
                                    $cnt++;
                                    @endphp
                                @endforeach
                            @endif
                            
                            
                        </table>
                        
                    </td>
                </tr>
            </table>

            <table style="width:100%; border-collapse: collapse; padding-bottom:10px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style="padding-right:20px;">
                        <table style="width:100%;padding:3px -8px;">
                            <tr>
                                <td >
                                <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">Findings</span>
                               
                                </td>
                            </tr>
                            
                        </table>
                        <table style="width:100%; border-collapse:collapse;padding:3px -8px" >
                            <tr>
                                <td style="padding-right:2px">
                                    <span style="font-size:9px;font-family:helvetica;">Individual reports TB like symptoms:</span><span style="font-family:helvetica;font-size:9px;">  {{ $newform['tbSymptoms']}}</span>
                                
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding-right:2px">
                                    <span style="font-size:9px;font-family:helvetica;">Individual identified TB Risk Factors:</span><span style="font-family:helvetica;font-size:9px;">  {{ $newform['tbRiskFactors']}}</span>
                                
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-right:2px">
                                    <span style="font-size:9px;font-family:helvetica;">Status:</span><span style="font-family:helvetica;font-size:9px;">  {{ $newform['tbStatus']}}</span>
                                
                                </td>
                            </tr>
                        </table>
                        
                    </td>
                </tr>
            </table>

            <table style="width:100%; border-collapse: collapse; padding-bottom:10px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style=" padding-right:20px;">
                        <table style="width:100%;padding:3px -8px;">
                            <tr>
                                <td >
                                <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">HISTORY & PHYSICAL</span>
                               
                                </td>
                            </tr>
                            <tr>
                                <td >
                                <span style="font-size:9px;padding-bottom:14px;font-family:helvetica">Vital signs</span>
                                </td>
                            </tr>
                        </table>
                        
                        <table style="width:100%; border-collapse:collapse;padding:2px -8px" >
                            <tr>
                                <td style="padding-right:2px">
                                    <span style="font-size:9px;font-family:helvetica;">Height:</span><span style="font-size:9px;font-family:helvetica;">&nbsp;{{ $newform['height'] }}</span>
                                
                                </td>
                                <td style="padding-right:2px">
                                    <span style="font-size:9px;font-family:helvetica;">Pulse:</span><span style="font-size:9px;font-family:helvetica;">&nbsp;{{ $newform['pulse'] }}</span>
                                
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <span style="font-size:9px;font-family:helvetica;">Weight:</span>&nbsp;<span style="font-size:9px;font-family:helvetica;"> {{ $newform['weight'] }}</span>
                                
                                </td>
                                <td>
                                <span style="font-size:9px;font-family:helvetica">Respiration:</span>&nbsp;<span style="font-size:9px;font-family:helvetica;">{{ $newform['respiration'] }}</span>
                                
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span style="font-size:9px;font-family:helvetica;">Blood Pressure:</span><span style="font-size:9px;font-family:helvetica">&nbsp;{{ $newform['bp'] }}</span>
                                
                                </td>
                                <td>
                                    <span style="font-size:9px;font-family:helvetica">Temperature:</span><span style="font-size:9px;font-family:helvetica">&nbsp;{{ $newform['temperature'] }}</span>
                                
                                </td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
            </table>

            <table style="width:100%; border-collapse: collapse; padding-bottom:20px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td style="padding-right:20px;">
                        <table style="width:100%;padding:3px -8px;">
                            <tr>
                                <td >
                                <span style="font-weight:bold;font-size:9px;padding-bottom:14px;font-family:helvetica">Past Medical Illnesses </span>
                                
                               
                                </td>
                            </tr>
                            <tr>
                                <td><span style="font-family:helvetica;font-size:9px;white-space: pre-line;">
                                @if($newform['past_medical_illnesses'] !="") {{ $newform['past_medical_illnesses']}} @else None @endif </span></td>
                            </tr>
                            <tr>
                                <td >
                                <span style="font-weight:bold;font-size:9px;font-family:helvetica">Past Medical History</span>
                               
                               
                                </td>
                            </tr>
                            <tr>
                                <td><span style="font-family:helvetica;font-size:9px;white-space: pre-line;">
                                    @if($newform['past_medical_history'] !=""){{ $newform['past_medical_history']}} @else None @endif</span></td>
                            </tr>
                            <tr>
                                <td style="height:25px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="height:25px;">&nbsp;</td>
                            </tr>
   
                        </table>
        
        
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table style="width:100%; border-collapse: collapse; background: #fff;page-break-before: always;">
    <!-- HEADER -->
  
    <tr>
        <td colspan="2">

            <!-- MAIN TITLE -->
            <table style="border-collapse: collapse; padding-bottom:30px; width:100%">
                <tr>
                
                    <td style="width:40%; padding-right:20px;">
                        <table style="padding:0px -5px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 15px;font-weight: bold;margin-bottom: 30px;text-align: left;">FINAL MEDICAL REPORT</div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </td>
                    <td style="width:10%;">
                        <table style="padding:4px -13px -10px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 9px;font-weight: normal;text-align: left;">{{ $newform['report_date5'] ?? '' }}</div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </td>
                </tr>
            </table>
           
            <!-- EXAM INFORMATION & RESULT KEY -->
            <table style="width:100%; border-collapse: collapse; padding-bottom:20px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td>
                        <table style="width:100%;padding:5px -8px;">
                            <tr>
                                <td style="border-bottom:1px solid #000;padding-bottom:500px">
                                <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">RESULT DETAILS</span>
                               
                                </td>
                            </tr>
                            
                        </table>
                        
                        <table style="width:100%;padding:10px -8px;font-family:helvetica">
                            <tr>
                                <td >
                                    <span style="font-weight:bold;font-size:10px;">HISTORY & PHYSICAL</span>
                               
                                </td>
                            </tr>
                           
                            <tr>
                                <td >
                                    <span style="font-weight:bold;font-size:9px;">Past Surgical History</span><br>
                                    <span style="font-size:9px;">@if($newform['past_surgery_history'] !=""){{$newform['past_surgery_history']}} @else None @endif</span>
                                    
                               
                                </td>
                            </tr>
                            
                            <tr>
                                <td >
                                    <span style="font-weight:bold;font-size:9px;">Medications</span><br>
                                    <span style="font-size:9px;">@if($newform['past_medications'] !="") {{ $newform['past_medications']}} @else None @endif</span>
                               
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <span style="font-weight:bold;font-size:9px;">Allergies</span><br>
                                    <span style="font-size:9px;">@if($newform['allergies'] !="") {{ $newform['allergies']}} @else No known allergies @endif</span>
                               
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%;padding:5px -8px">
                            <tr>
                                <td >
                                    <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">Social History</span>
                               
                                </td>
                            </tr>
                           @if(!empty($newform['vns_history_temp']))
                                @foreach($newform['vns_history_temp'] as $vnsh)
                                    <tr>
                                        <td >
                                            <span style="font-size:9px;font-family:helvetica">{{ $newform['vns_history_name'.$vnsh] }} : </span>
                                            <span style="font-size:9px;font-family:helvetica">{{ $newform['vns_history_value'.$vnsh] }}</span>
                                    
                                        </td>
                                    </tr>
                            @endforeach
                            @endif

                           
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>


<table style="width:100%; border-collapse: collapse; background: #fff;page-break-before: always;">
    <!-- HEADER -->
  
    <tr>
        <td colspan="2">

            <!-- MAIN TITLE -->
            <table style="border-collapse: collapse; padding-bottom:30px; width:100%">
                <tr>
                
                    <td style="width:40%; padding-right:20px;">
                        <table style="padding:0px -5px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 15px;font-weight: bold;margin-bottom: 30px;text-align: left;">FINAL MEDICAL REPORT</div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </td>
                    <td style="width:10%;">
                        <table style="padding:4px -13px -10px;font-family:helvetica">
                            <thead>
                                <tr>
                                    <th>
                                        <div style="font-size: 9px;font-weight: normal;text-align: left;">{{ $newform['report_date6'] ?? '' }}</div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </td>
                </tr>
            </table>
         
            
            <!-- EXAM INFORMATION & RESULT KEY -->
            <table style="width:100%; border-collapse: collapse; padding-bottom:20px">
                <tr>
                    <!-- LEFT COLUMN -->
                    <td >
                        <table style="width:100%;padding:5px -8px;">
                            <tr>
                                <td style="border-bottom:1px solid #000;padding-bottom:500px">
                                <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">RESULT DETAILS</span>
                               
                                </td>
                            </tr>
                            
                        </table>
                        
                        <table style="width:100%;padding:10px -8px">
                            <tr>
                                <td >
                                    <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">HISTORY & PHYSICAL</span>
                               
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%;padding:5px -8px">
                            <tr>
                                <td >
                                    <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">Review Of Systems</span>
                               
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%; border-collapse:collapse;padding:2px -8px;font-family:helvetica;font-size:9px;" >
                            <tr>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Skin : </span>
                                    <span style="">@if($newform['skin'] !="") {{ $newform['skin']}} @else Non-contributory @endif</span>
                               
                                </td>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Head : </span>
                                    <span style="">@if($newform['head'] !="") {{ $newform['head']}} @else Non-contributory @endif</span>
                               
                                </td>
                            </tr>

                            <tr>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Eyes : </span>
                                    <span style="">@if($newform['eyes'] !="") {{ $newform['eyes']}} @else Non-contributory @endif</span>
                                   
                                </td>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Nose/Sinus : </span>
                                    <span style="">@if($newform['eyes'] !="") {{ $newform['nose']}} @else Non-contributory @endif</span>
                                 
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Mouth & Throat : </span>
                                    <span style="">@if($newform['mouth'] !="") {{ $newform['mouth']}} @else Non-contributory @endif</span>
                                </td>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Neck : </span>
                                    <span style="">@if($newform['neck'] !="") {{ $newform['neck']}} @else Non-contributory @endif</span>
                                   
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Respiratory : </span>
                                    <span style="">@if($newform['respiratory'] !="") {{ $newform['respiratory']}} @else Non-contributory @endif</span>
                                    
                                </td>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Cardiac : </span>
                                    <span style="">@if($newform['cardiac'] !="") {{ $newform['cardiac']}} @else Non-contributory @endif</span>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Gastrointestinal : </span>
                                    <span style="">@if($newform['gastroin_testinal'] !="") {{ $newform['gastroin_testinal']}} @else Non-contributory @endif</span>
                                   
                                </td>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Urinary : </span>
                                    <span style="">@if($newform['urinary'] !="") {{ $newform['urinary']}} @else Non-contributory @endif</span>
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Endocrine : </span>
                                    <span style="">@if($newform['endocrine'] !="") {{ $newform['endocrine']}} @else Non-contributory @endif</span>
                                   
                                </td>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Musculoskeletal : </span>
                                    <span style="">@if($newform['musculoskeletal'] !="") {{ $newform['musculoskeletal']}} @else Non-contributory @endif</span>
                                  
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Neurologic : </span>
                                    <span style="">@if($newform['neurologic'] !="") {{ $newform['neurologic']}} @else Non-contributory @endif</span>
                                </td>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Hematologic : </span>
                                    <span style="">@if($newform['hematologic'] !="") {{ $newform['hematologic']}} @else Non-contributory @endif</span>
                               
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Psychiatric : </span>
                                    <span style="">@if($newform['psychiatric'] !="") {{ $newform['psychiatric']}} @else Non-contributory @endif</span>
                               
                                </td>
                                <td >
                                    <span style="font-size:bold;font-size:9px;font-family:helvetica">Vascular : </span>
                                    <span style="">@if($newform['Vascular'] !="") {{ $newform['Vascular']}} @else Non-contributory @endif</span>
                               
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%;padding:5px -8px">
                            <tr>
                                <td >
                                    <span style="font-weight:bold;font-size:10px;padding-bottom:14px;font-family:helvetica">Exam</span>
                               
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <span style="font-size:10px;padding-bottom:14px;font-family:helvetica">Examination</span>
                                    <span style="font-size:9px;font-family:helvetica;" >{!! nl2br($newform['examination']) !!}</span>
                                </td>
                            </tr>
                            <tr>
                                <td >
                                <span style="width: 100%; min-height: 80px; border: 1px solid #000; padding: 8px; font-size: 9px; font-family: helvetica; outline: none; margin-top: 10px;white-space: pre-line;">{{ $newform['examNotes']}}</span>
                                    
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>



</body>

</html>