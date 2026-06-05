<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Medical Report</title>
    <link href="{{ asset('assets/esign/bower_components/bootstrap/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/esign/libs/sweetalert/sweetalert.css')}}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .body_pdf {
            padding: 40px;
        }

        .page-layout {
            width: 8.5in;
            min-height: 11in;
            margin: 0 auto 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .main-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: left;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 20px;
        }

        .info-group {
            margin-bottom: 15px;
            display: flex;
            align-items: baseline;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 140px;
            font-size: 13px;
        }

        .input-line {
            border: none;
            border-bottom: 1px solid #000;
            flex: 1;
            padding: 2px 5px;
            font-size: 13px;
            outline: none;
        }

        .result-key {
            margin-top: 10px;
        }

        .key-item {
            margin-bottom: 5px;
            font-size: 13px;
        }

        .key-color {
            font-weight: bold;
        }

        .key-green {
            color: green;
        }

        .key-yellow {
            color: #ffa500;
        }

        .key-red {
            color: red;
        }

        .key-white {
            color: #666;
        }

        .key-blue {
            color: blue;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background: #666;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }

        table input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #000;
            padding: 2px 5px;
            font-size: 12px;
        }

        .text-area {
            width: 100%;
            min-height: 80px;
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
            font-family: Arial, sans-serif;
            outline: none;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 250px;
            margin: 0 10px;
        }

        .checkbox-option {
            margin: 15px 0;
            font-size: 13px;
        }

        .checkbox-option input {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }

        .add-row-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            margin-top: 10px;
            cursor: pointer;
            font-size: 12px;
            border-radius: 3px;
        }

        .add-row-btn:hover {
            background: #45a049;
        }

        .delete-row-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 12px;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .delete-row-btn:hover {
            background: #d32f2f;
        }

        .submit-btn {
            background: #2196F3;
            color: white;
            border: none;
            padding: 12px 30px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            display: block;
            margin: 30px auto;
            position: relative;
  
        }

        .submit-btn:hover {
            background: #0b7dda;
        }

        .systems-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        .system-item {
            display: flex;
            align-items: baseline;
            font-size: 12px;
        }

        .system-label {
            min-width: 120px;
            font-weight: normal;
        }

        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            text-align: center;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2196F3;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .page {
                box-shadow: none;
                page-break-after: always;
                margin: 0;
            }

            .add-row-btn,
            .submit-btn {
                display: none;
            }
        }

        footer {
            height: 100px;
            background: url(footer_final.png) no-repeat center bottom;
            width: 100%;
            background-size: 100% 100%;
            margin-top: 100%;
        }
        #rename_canvas {
        border: 1px solid navy;
    }

    .loader {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    width: 14px;
    height: 14px;
    animation: spin 0.8s linear infinite;
    display: inline-block;
    margin-left: 8px;
    vertical-align: middle;
    }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
    </style>

    <style>
        .header {
  position: relative;
  width: 100%;
  height: 120px;
  background: linear-gradient(45deg, #fff 45%, #000 44%);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 40px;
}

.id-box {
    position: absolute;
    top: 10px;
    left: 289px;
    font-weight: bold;
    border-radius: 2px;
}

.name-box {
    position: absolute;
    margin-top: 33px;
    left: 335px;
    font-weight: 500;
    font-size: 13px;
    border-radius: 2px;
}

.birth_date-box {
    position: absolute;
    margin-top: 50px;
    left: 335px;
    font-weight: 500;
    font-size: 13px;
    border-radius: 2px;
}

.header .left-logo {
      position: absolute;
    top: 56px;
    left: 116px;
}

.header .right-logo {
  position: absolute;
  top: 35px;
  right: 60px;
  text-align: right;
}

.header .right-logo h2 {
  margin: 0;
  font-size: 18px;
  font-weight: bold;
  letter-spacing: 1px;
}

.content {
  padding: 40px;
}

.hide{
    display:none
}
    </style>
</head>

<body>
    <div class="loading" id="loading">
        <div class="spinner"></div>
        <div>Generating PDF...</div>
    </div>

    <!-- Page 1 -->
     <form id="submitRegenerateFormID">
        <div class="page-layout">
            <div class="header_pdf">
                <header style="background: url('{{ asset('header.png')}}') no-repeat center top;background-size: cover;height: 120px;">
                    <div class="id-box">ID: {{ $patient_data->id}}</div>
                    <input type="hidden" name="patient_id" value="{{ $patient_data->id}}">
                   
                </header>
            </div>
            <div class="body_pdf">
                <div class="page" id="page1">
                    <div class="main-title">FINAL MEDICAL REPORT
                        <input type="text" class="input-line" style="display: inline-block; width: 150px; margin-left: 20px;" id="reportDate" name="report_date" placeholder="MM/DD/YYYY" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                    </div>
                    <div class="two-column">
                        <div>
                            <div class="section-title">EXAM INFORMATION</div>
                            <div class="info-group">
                                <span class="info-label">Exam Date:</span>
                                <input type="text" class="input-line" id="examDate" name="exam_date" @if($patient_data->appointment_date !='' && $patient_data->appointment_date !='0000-00-00 00:00:00') readonly @endif data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="@if($patient_data->appointment_date !='' && $patient_data->appointment_date !='0000-00-00 00:00:00'){{ date('m/d/Y',strtotime($patient_data->appointment_date))}}@endif">
                            </div>
                            <div class="info-group">
                                <span class="info-label">Result:</span>
                                <select id="examResult"  name="exam_result">
                                    <option value="">Select Result</option>
                                    <option value="Complete">Complete</option>
                                    <option value="Cleared">Cleared</option>
                                    <option value="Not cleared">Not cleared</option>
                                </select>
                              
                            </div>
                        </div>
                        <div>
                            <div class="section-title">RESULT KEY</div>
                            <div class="result-key">
                                <div class="key-item"><span class="key-color key-green">Complete:</span> Medically qualified
                                </div>
                                <div class="key-item"><span class="key-color key-yellow">Cleared:</span> Qualified with
                                    comments</div>
                                <div class="key-item"><span class="key-color key-red">Not cleared:</span> Medically disqualified
                                </div>
                               
                            </div>
                        </div>
                    </div>

                    <div class="section-title">PATIENT INFORMATION</div>
                    <div class="two-column">
                        <div>
                            <div class="info-group">
                                <span class="info-label">Name:</span>
                                <input type="text" class="input-line" id="patientName" name="patient_name" value="{{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}">
                            </div>
                            <div class="info-group">
                                <span class="info-label">Date of birth:</span>
                                <input type="text" class="input-line" id="dob" name="dob"  value="{{ date('m/d/Y',strtotime($patient_data->dob))}}" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                            </div>
                           
                        </div>
                        <div>
                            <div class="info-group">
                                <span class="info-label">Phone:</span>
                                <input type="text" class="input-line" id="phone" name="phone"  value="{{ $patient_data->mobile}}">
                            </div>
                            <div class="info-group">
                                <span class="info-label">Email:</span>
                                <input type="text" class="input-line" id="email" name="email"  value="{{ $patient_data->email}}">
                            </div>
                            
                        </div>
                        <div style="grid-column: 1 / span 2; margin-top: -30px;">
                            <div class="info-group">
                                <span class="info-label">Address:</span>
                                <input type="text" class="input-line" id="address" name="address"
                                    value="{{ $patient_data->address1.','.$patient_data->address2.' '.$patient_data->city.','.$patient_data->state.','.$patient_data->zip_code }}">
                            </div>
                        </div>
                        <div style="margin-top: -30px;">
                            <div class="info-group">
                                <span class="info-label">Gender:</span>
                                <input type="text" class="input-line" id="gender" name="gender" value="{{ $patient_data->gender }}">
                            </div>
                        </div>
                    </div>

                    <div class="section-title">VISIT DETAILS</div>
                    <table id="visitTable">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>PROCEDURE</th>
                                <th>RESULT</th>
                                <th>COMMENT</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $uniqId =uniqid();
                            @endphp
                            <tr>
                            <input type="hidden" name="visit_temp[]" value="{{ $uniqId}}">
                                <td><input type="text" placeholder="Enter Date" name="visit_date{{$uniqId}}" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false"></td>
                                <td>
                                <select name="visit_procedure_id{{$uniqId}}" id="visit_procedure_id{{$uniqId}}" onchange="fetchProcedureResult('{{$uniqId}}')">
                                    <option value="">Select  Procedure name</option>
                                    @foreach($templateProcedure as $prd)
                                    <option value="{{ $prd->id}}">{{ $prd->procedure_name}}</option>
                                    @endforeach
                                </select>
                                </td>
                                <td>
                                    <select name="visit_result_id{{$uniqId}}" id="visit_result{{$uniqId}}">
                                        <option value="">Select Result</option>
                                       
                                    </select>
                                </td>

                                <td><textarea type="text" class="form-control" rows="5" cols="30" placeholder="Enter Comment" name="visit_comment{{$uniqId}}"></textarea></td>
                                <td>
                                    <button type="button" id="removeDelete" class="delete-row-btn hide" onclick="deleteRow(this)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="add-row-btn" type="button" onclick="addVisitRow()">+ Add More Visit</button>
                </div>
            </div>
            <div class="footer_pdf">
                <footer>
                    <div style="text-align:center; font-size:10px; margin-top:60px;">
                        <span class="page-number"></span>
                        <div class="name-box">Name: {{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}</div>
                        <input type="hidden" name="footer_name" value="{{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}">
                        <div class="birth_date-box">Date of birth:{{ date('m/d/Y',strtotime($patient_data->dob))}}</div>
                        <input type="hidden" name="footer_dob" value="{{ date('m/d/Y',strtotime($patient_data->dob))}}">
                    </div>
                </footer>
            </div>
        </div>

        <!-- Page 2 -->
        <div class="page-layout">
            <div class="header_pdf">
                <header style="background: url('{{ asset('header.png')}}') no-repeat center top;background-size: cover;height: 120px;">
                </header>
            </div>
            <div class="body_pdf">

                <div class="page" id="page2">
                    <div class="main-title">FINAL MEDICAL REPORT
                        <input type="text" class="input-line" style="display: inline-block; width: 150px; margin-left: 20px;" id="reportDate2" name="report_date2" placeholder="MM/DD/YYYY" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                    </div>

                    <div class="section-title">VISIT DETAILS</div>
                    <table id="addVisitRow2">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>PROCEDURE</th>
                                <th>RESULT</th>
                                <th>COMMENT</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @php
                                    $uniqId1 =uniqid();
                                @endphp
                                <input type="hidden" name="temp2[]" value="{{ $uniqId1}}">
                                <td><input type="text" id="riskDate" placeholder="Enter Date" name="visit_date_second{{$uniqId1}}"  data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false"></td>
                                <td>
                                <select name="visit_procedure_second_id{{$uniqId1}}" id="visit_procedure_second_id{{$uniqId1}}" onchange="fetchProcedureSecondResult('{{$uniqId1}}')">
                                    <option value="">Select  Procedure name</option>
                                    @foreach($templateProcedure as $prd)
                                    <option value="{{ $prd->id}}">{{ $prd->procedure_name}}</option>
                                    @endforeach
                                </select>
                               </td>
                                <td>
                                    <select name="visit_result_second_id{{$uniqId1}}" id="visit_result_second{{$uniqId1}}">
                                        <option value="">Select Result</option>
                                        
                                    </select>
                                </td>
                                <td><textarea type="text" class="form-control"  rows="5" cols="30"  placeholder="Enter Comment" name="visit_comment_second{{$uniqId1}}"></textarea></td>
                                <td><button type="button" id="removeDelete2" class="delete-row-btn hide" onclick="deleteRow1(this)">Delete</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="add-row-btn" type="button" onclick="addVisitRow2()">+ Add More Visit</button>

                    <div class="section-title">DISPOSITION</div>
                    <div class="info-group">
                        <span class="info-label">Status:</span>
                        <input type="text" class="input-line" id="dispositionStatus" name="disposition_status">
                    </div>
                    <div style="margin-top: 15px;">
                        <div style="font-weight: bold; margin-bottom: 5px; font-size: 13px;">Comments:</div>
                        <textarea class="text-area" id="dispositionComments" name="disposition_comment"></textarea>
                    </div>
                </div>
            </div>
            <div class="footer_pdf">
                <footer>
                    <div style="text-align:center; font-size:10px; margin-top:60px;">
                        <span class="page-number"></span>
                        <div class="name-box">Name: {{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}</div>
                        <input type="hidden" name="footer_name" value="{{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}">
                        <div class="birth_date-box">Date of birth:{{ date('m/d/Y',strtotime($patient_data->dob))}}</div>
                        <input type="hidden" name="footer_dob" value="{{ date('m/d/Y',strtotime($patient_data->dob))}}">
                    </div>
                </footer>
            </div>
        </div>

        <!-- Page 3 -->
        <div class="page-layout">
            <div class="header_pdf">
                <header style="background: url(header.png) no-repeat center top;background-size: cover;height: 120px;">
                </header>
            </div>
            <div class="body_pdf">
                <div class="page" id="page3">
                    <div class="main-title">FINAL MEDICAL REPORT
                        <input type="text" class="input-line" style="display: inline-block; width: 150px; margin-left: 20px;" id="reportDate3" name="report_date3" placeholder="MM/DD/YYYY" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                    </div>

                    <div class="section-title">ADDITIONAL INFORMATION</div>
                    <p style="font-size: 12px; line-height: 1.6; margin-bottom: 15px;">
                        If you wish to discuss this medical report in more detail, or require additional information
                        regarding these
                        clinical services, please contact (718) 972-3693
                    </p>

                    <p style="font-size: 12px; line-height: 1.6; margin-bottom: 15px;">
                        The procedure(s) and/or examination(s) [Services] provided pursuant to the above order were reviewed
                        by NY
                        Best Medical's clinical team, under the supervision of Svetlana Zeltser, NP.
                    </p>

                    <p style="font-size: 12px; line-height: 1.6; margin-bottom: 30px;">
                        These services are coordinated and/or provided by or through NY Best Medical. The services are
                        performed
                        under the supervision of Svetlana Zeltser, NP, and are performed by individuals who maintain
                        appropriate
                        medical licensure, certification, and/or training.
                    </p>

                    <div style="margin-top: 60px;">
                        <div style="font-size: 13px; margin-bottom: 10px;">Svetlana Zeltser, NP</div>
                        <div style="font-size: 13px;">License # <input type="text" class="input-line"
                                style="min-width: 200px;" id="licenseNumber" name="licenseNumber" value="347234"></div>
                    </div>
                </div>
            </div>
            <div class="footer_pdf">
                <footer>
                    <div style="text-align:center; font-size:10px; margin-top:60px;">
                        <span class="page-number"></span>
                        <div class="name-box">Name: {{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}</div>
                        <input type="hidden" name="footer_name" value="{{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}">
                        <div class="birth_date-box">Date of birth:{{ date('m/d/Y',strtotime($patient_data->dob))}}</div>
                        <input type="hidden" name="footer_dob" value="{{ date('m/d/Y',strtotime($patient_data->dob))}}">
                    </div>
                </footer>
            </div>
        </div>

        <!-- Page 4 -->
        <div class="page-layout">
            <div class="header_pdf">
                <header style="background: url(header.png) no-repeat center top;background-size: cover;height: 120px;">
                </header>
            </div>
            <div class="body_pdf">
                <!-- Page 4 -->
                <div class="page" id="page4">
                    <div class="main-title">FINAL MEDICAL REPORT
                        <input type="text" class="input-line" style="display: inline-block; width: 150px; margin-left: 20px;" id="reportDate4" name="report_date4" placeholder="MM/DD/YYYY" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                    </div>

                    <div class="section-title">RESULT DETAILS</div>

                    <div style="font-weight: bold; font-size: 14px; margin: 20px 0 10px;">RISK ASSESSMENT</div>

                    <div style="font-weight: bold; margin-bottom: 10px; font-size: 13px;">Questions</div>
                    @php
                        $questionCount =1;
                    @endphp
                    @foreach($vnsQuestionList as $vnq)
                    @php
                        $uniqIdQuestion =uniqid();
                    @endphp
                        <input type="hidden" name="question_temp[]" value="{{ $uniqIdQuestion}}">
                        <input type="hidden" name="question_id{{ $uniqIdQuestion }}" value="{{ $vnq->id}}">
                        <p style="font-size: 11px; line-height: 1.6; margin-bottom: 8px;">
                           <b>{{$questionCount}}.</b> {{$vnq->question_name}}
                            <input type="hidden" name="question_name{{ $uniqIdQuestion }}" value="{{ $vnq->question_name}}">
                            
                        </p>
                        <div class="info-group"><input type="text" class="input-line" style=" margin-left: 10px;" id="tbTreated" name="question_value_{{ $uniqIdQuestion}}"></div>
                        @php
                        $questionCount++;
                    @endphp
                    @endforeach
                    
                    <div style="font-weight: bold; margin: 20px 0 10px; font-size: 13px;">Findings</div>
                    <div class="info-group">
                        <span style="font-size: 12px; min-width: 250px;">Individual reports TB like symptoms:</span>
                        <input type="text" class="input-line" id="tbSymptoms" name="tbSymptoms">
                    </div>
                    <div class="info-group">
                        <span style="font-size: 12px; min-width: 250px;">Individual identified TB Risk Factors:</span>
                        <input type="text" class="input-line" id="tbRiskFactors" name="tbRiskFactors">
                    </div>
                    <div class="info-group">
                        <span style="font-size: 12px; min-width: 250px;">Status:</span>
                        <input type="text" class="input-line" id="tbStatus" name="tbStatus">
                    </div>

                    <div style="font-weight: bold; font-size: 14px; margin: 30px 0 10px;">HISTORY & PHYSICAL</div>

                    <div style="font-weight: bold; margin-bottom: 10px; font-size: 13px;">Vital signs</div>
                    <div class="two-column" style="gap: 15px;">
                        <div>
                            <div class="info-group">
                                <span style="font-size: 12px; min-width: 120px;">Height:</span>
                                <input type="text" class="input-line" id="height" name="height">
                            </div>
                            <div class="info-group">
                                <span style="font-size: 12px; min-width: 120px;">Weight:</span>
                                <input type="text" class="input-line" id="weight" name="weight">
                            </div>
                            <div class="info-group">
                                <span style="font-size: 12px; min-width: 120px;">Blood Pressure:</span>
                                <input type="text" class="input-line" id="bp" name="bp">
                            </div>
                        </div>
                        <div>
                            <div class="info-group">
                                <span style="font-size: 12px; min-width: 120px;">Pulse:</span>
                                <input type="text" class="input-line" id="pulse" name="pulse">
                            </div>
                            <div class="info-group">
                                <span style="font-size: 12px; min-width: 120px;">Respiration:</span>
                                <input type="text" class="input-line" id="respiration" name="respiration">
                            </div>
                            <div class="info-group">
                                <span style="font-size: 12px; min-width: 120px;">Temperature:</span>
                                <input type="text" class="input-line" id="temperature" name="temperature">
                            </div>
                        </div>
                    </div>

                    <div style="font-weight: bold; margin: 20px 0 10px; font-size: 13px;">Past Medical Illnesses</div>
                    <textarea class="text-area" style="min-height: 60px;" id="pastIllnesses" name="past_medical_illnesses"></textarea>

                    <div style="font-weight: bold; margin: 15px 0 10px; font-size: 13px;">Past Medical History</div>
                    <textarea class="text-area" style="min-height: 60px;" id="pastHistory" name="past_medical_history"></textarea>
                </div>
            </div>
            <div class="footer_pdf">
                <footer>
                    <div style="text-align:center; font-size:10px; margin-top:60px;">
                        <span class="page-number"></span>
                        <div class="name-box">Name: {{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}</div>
                        <input type="hidden" name="footer_name" value="{{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}">
                        <div class="birth_date-box">Date of birth:{{ date('m/d/Y',strtotime($patient_data->dob))}}</div>
                        <input type="hidden" name="footer_dob" value="{{ date('m/d/Y',strtotime($patient_data->dob))}}">
                    </div>
                </footer>
            </div>
        </div>

        <!-- Page 5 -->
        <div class="page-layout">
            <div class="header_pdf">
                <header style="background: url('{{ asset('header.png')}}') no-repeat center top;background-size: cover;height: 120px;">
                </header>
            </div>
            <div class="body_pdf">
                <!-- Page 5 -->
                <div class="page" id="page5">
                    <div class="main-title">FINAL MEDICAL REPORT
                        <input type="text" class="input-line" style="display: inline-block; width: 150px; margin-left: 20px;" id="reportDate5" name="report_date5" placeholder="MM/DD/YYYY" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                    </div>

                    <div class="section-title">RESULT DETAILS</div>

                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 15px;">HISTORY & PHYSICAL</div>

                    <div style="font-weight: bold; margin: 15px 0 10px; font-size: 13px;">Past Surgical History</div>
                    <textarea class="text-area" id="pastSurgery" name="past_surgery_history"></textarea>

                    <div style="font-weight: bold; margin: 20px 0 10px; font-size: 13px;">Medications</div>
                    <textarea class="text-area" id="medications" name="past_medications"></textarea>

                    <div style="font-weight: bold; margin: 20px 0 10px; font-size: 13px;">Allergies</div>
                    <textarea class="text-area" id="allergies" name="allergies"></textarea>

                    <div style="font-weight: bold; margin: 20px 0 10px; font-size: 13px;">Social History</div>
                    
                    @foreach($vnsSocialHistoryList as $vnqh)
                    @php
                        $uniqIdVNSHistory =uniqid();
                    @endphp
                    <input type="hidden" name="vns_history_temp[]" value="{{ $uniqIdVNSHistory}}">
                    <input type="hidden" name="vns_history_id{{ $uniqIdVNSHistory }}" value="{{ $vnqh->id}}">
                    <input type="hidden" name="vns_history_name{{ $uniqIdVNSHistory }}" value="{{ $vnqh->name}}">
                    <div class="info-group">
                        <span style="font-size: 12px; min-width: 180px;">{{ $vnqh->name}}:</span>
                        <input type="text" class="input-line" name="vns_history_value{{ $uniqIdVNSHistory}}" value="{{ $vnqh->default_value }}">
                    </div>
                    @endforeach
    
                </div>
            </div>
            <div class="footer_pdf">
                <footer>
                    <div style="text-align:center; font-size:10px; margin-top:60px;">
                        <span class="page-number"></span>
                        <div class="name-box">Name: {{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}</div>
                        <input type="hidden" name="footer_name" value="{{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}">
                        <div class="birth_date-box">Date of birth:{{ date('m/d/Y',strtotime($patient_data->dob))}}</div>
                        <input type="hidden" name="footer_dob" value="{{ date('m/d/Y',strtotime($patient_data->dob))}}">
                    </div>
                </footer>
            </div>
        </div>

        <!-- Page 6 -->
        <div class="page-layout">
            <div class="header_pdf">
                <header style="background: url('{{ asset('header.png')}}') no-repeat center top;background-size: cover;height: 120px;">
                </header>
            </div>
            <div class="body_pdf">
                <!-- Page 6 -->
                <div class="page" id="page6">
                    <div class="main-title">FINAL MEDICAL REPORT
                        <input type="text" class="input-line" style="display: inline-block; width: 150px; margin-left: 20px;" id="reportDate6" name="report_date6" placeholder="MM/DD/YYYY" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false">
                    </div>

                    <div class="section-title">RESULT DETAILS</div>

                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 15px;">HISTORY & PHYSICAL</div>

                    <div style="font-weight: bold; margin: 15px 0 10px; font-size: 13px;">Review Of Systems</div>
                    <div class="systems-grid">
                        <div class="system-item">
                            <span class="system-label">Skin:</span>
                            <input type="text" class="input-line" id="skin" name="skin">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Head:</span>
                            <input type="text" class="input-line" id="head" name="head">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Eyes:</span>
                            <input type="text" class="input-line" id="eyes" name="eyes">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Nose/Sinus:</span>
                            <input type="text" class="input-line" id="nose" name="nose">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Mouth & Throat:</span>
                            <input type="text" class="input-line" id="mouth" name="mouth">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Neck:</span>
                            <input type="text" class="input-line" id="neck" name="neck">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Respiratory:</span>
                            <input type="text" class="input-line" id="respiratory" name="respiratory">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Cardiac:</span>
                            <input type="text" class="input-line" id="cardiac" name="cardiac">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Gastrointestinal:</span>
                            <input type="text" class="input-line" id="gastro" name="gastroin_testinal">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Urinary:</span>
                            <input type="text" class="input-line" id="urinary" name="urinary">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Endocrine:</span>
                            <input type="text" class="input-line" id="endocrine" name="endocrine">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Musculoskeletal:</span>
                            <input type="text" class="input-line" id="musculo" name="musculoskeletal">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Neurologic:</span>
                            <input type="text" class="input-line" id="neuro" name="neurologic">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Hematologic:</span>
                            <input type="text" class="input-line" id="hemato" name="hematologic">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Psychiatric:</span>
                            <input type="text" class="input-line" id="psych" name="psychiatric">
                        </div>
                        <div class="system-item">
                            <span class="system-label">Vascular:</span>
                            <input type="text" class="input-line" id="vascular" name="Vascular">
                        </div>
                    </div>

                    <div style="font-weight: bold; margin: 30px 0 10px; font-size: 13px;">Exam</div>
                    <div class="info-group">
                        <span style="font-size: 12px; min-width: 100px;">Examination:</span>
                        <input type="text" class="input-line" id="examination" name="examination">
                    </div>
                    <textarea class="text-area" style="margin-top: 10px;" id="examNotes" name="examNotes"></textarea>
                </div>

            </div>
            <div class="footer_pdf">
                <footer>
                    <div style="text-align:center; font-size:10px; margin-top:60px;">
                        <span class="page-number"></span>
                        <div class="name-box">Name: {{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}</div>
                        <input type="hidden" name="footer_name" value="{{ $patient_data->first_name.' '.$patient_data->middle_name.' '.$patient_data->last_name}}">
                        <div class="birth_date-box">Date of birth:{{ date('m/d/Y',strtotime($patient_data->dob))}}</div>
                        <input type="hidden" name="footer_dob" value="{{ date('m/d/Y',strtotime($patient_data->dob))}}">
                    </div>
                </footer>
            </div>
        </div>
    </form>
    <div class="modal fade" id="modal-default" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Signature Pad</h4>
                </div>
                <div class="modal-body">
                    <div id="signature-pad" class="signature-pad">
                        <div class="signature-pad--body">
                            
                            <input type="hidden" id="imagesId">
                            <div id="signaturePageBody">
                                <canvas width="550" height="500" id="rename_canvas"
                                    style="touch-action: none;"></canvas>
                            </div>

                            
                        </div>


                        <div class="signature-pad--footer">
                            <div class="description">Sign above</div>

                            <div class="signature-pad--actions">
                                <div>
                                    <button type="button" class="button clear" id="clear"
                                        data-action="clear">Clear</button>


                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left custom-margin-left"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary custom-margin-right" id="testingsSave">Save
                        changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <button class="submit-btn" id="submit_save_esign_form" type="button" onclick="generatePDF()">
    <span id="btn-text">Save</span>
    <span id="btn-loader" class="loader" style="display:none;"></span>
    </button>
    <script src="{{ asset('assets/js/jquery.min.js')}}"></script>
    
    <script src="{{ asset('assets/esign/libs/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('assets/esign/js/signature_pad.umd.js')}}"></script>
    <script src="{{ asset('assets/esign/js/appsignaturepad.js')}}?<?php echo strtotime(now()); ?>"></script>
<script src="{{ asset('assets/esign/js/signature_pad.min.js')}}"></script>
<script src="{{ asset('assets/esign/libs/sweetalert/sweetalert.min.js')}}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>

    <script>
        $(":input").inputmask();
        var responseProcedures = '<?php echo json_encode($templateProcedure);?>';
        
        var docusignId =1;
        var times = "<?php echo time(); ?>";
    var mainURL = "<?php echo URL::to('/'); ?>/";
        function addVisitRow() {
            let random10Digit = Math.floor(1000000000 + Math.random() * 9000000000) + new Date().getUTCMilliseconds();
            const tbody = document.querySelector('#visitTable tbody');
            const newRow = document.createElement('tr');

            var dropdownProd ='<option value="">Select Procedure</option>';

            $.each(JSON.parse(responseProcedures), function(i, v) {
                dropdownProd += "<option value='" + v.id + "'>" + v.procedure_name + "</option>";
            });
            newRow.innerHTML = `<input type="hidden" name="visit_temp[]" value="${random10Digit}">
                <td><input type="text" placeholder="Enter Date" name="visit_date${random10Digit}" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false"></td>
                <td>
                <select name="visit_procedure_id${random10Digit}" id="visit_procedure_id${random10Digit}" onchange="fetchProcedureResult(${random10Digit})">${dropdownProd}</select>
                </td>
                <td><select name="visit_result_id${random10Digit}" id="visit_result${random10Digit}"><option value="">Select Result</option></select>

                </td>
                <td><textarea type="text" class="form-control"  rows="5" cols="30" placeholder="Enter Comment" name="visit_comment${random10Digit}"></textarea></td>
                <td><button type="button" class="delete-row-btn" id="removeDelete" onclick="deleteRow(this)">Delete</button></td>
            `;
            tbody.appendChild(newRow);

            if($('input[name="visit_temp[]"]').length >1){
                $('#removeDelete').removeClass('hide');
            }
            $(":input").inputmask();
        }

        function addVisitRow2() {
            let random10Digit2 = Math.floor(1000000000 + Math.random() * 9000000000) + new Date().getUTCMilliseconds();
            const tbody = document.querySelector('#addVisitRow2 tbody');
            const newRow = document.createElement('tr');
            var dropdownProd ='<option value="">Select Procedure</option>';

            $.each(JSON.parse(responseProcedures), function(i, v) {
                dropdownProd += "<option value='" + v.id + "'>" + v.procedure_name + "</option>";
            });
                    
            newRow.innerHTML = `<input type="hidden" name="temp2[]" value="${random10Digit2}">
                <td><input type="text" placeholder="Enter Date" name="visit_date_second${random10Digit2}" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false"></td>
                <td><select name="visit_procedure_second_id${random10Digit2}" id="visit_procedure_second_id${random10Digit2}" onchange="fetchProcedureSecondResult(${random10Digit2})">${dropdownProd}</select></td>
                <td><select name="visit_result_second_id${random10Digit2}" id="visit_result_second${random10Digit2}"><option value="">Select Result</option></select></td>
                <td><textarea type="text" class="form-control"  rows="5" cols="30" placeholder="Enter Comment" name="visit_comment_second${random10Digit2}"></textarea></td>
                <td><button type="button" class="delete-row-btn" id="" onclick="deleteRow1(this)">Delete</button></td>
            `;
            tbody.appendChild(newRow);
            if($('input[name="temp2[]"]').length >1){
            $('#removeDelete2').removeClass('hide');
           }
           $(":input").inputmask();
        }

        function deleteRow(button) {
            const row = button.closest('tr');
            row.remove();
           if($('input[name="visit_temp[]"]').length ==1){
            $('#removeDelete').addClass('hide');
           }
        }

        async function generatePDF() {
           $('#submit_save_esign_form').prop('disabled',true);
           $('#btn-text').html('Saving...')
           $('#btn-loader').attr('style','display:inline-block')
            try {
                var formData = new FormData($('#submitRegenerateFormID')[0]);
                formData.append('_token','{{ csrf_token()}}')
                formData.append('patient_id','{{ $patient_data->id}}')
                formData.append('template_id','{{ $template_id}}')
                $('input[name="visit_temp[]"]').each(function(i,v){
                    var selectedText = $('select[name="visit_procedure_id'+$(this).val()+'"] option:selected').text();
                    if($('select[name="visit_procedure_id'+$(this).val()+'"] option:selected').val() !=""){
                        formData.append('visit_procedure'+$(this).val(),selectedText)
                    }

                    var selectedTextResult = $('select[name="visit_result_id'+$(this).val()+'"] option:selected').text();
                    if($('select[name="visit_result_id'+$(this).val()+'"] option:selected').val() !=""){
                        formData.append('visit_result'+$(this).val(),selectedTextResult)
                    }
                })
                $('input[name="temp2[]"]').each(function(i,v){
                    var selectedText = $('select[name="visit_procedure_second_id'+$(this).val()+'"] option:selected').text();
                    if($('select[name="visit_procedure_second_id'+$(this).val()+'"] option:selected').val() !=""){
                        formData.append('visit_procedure_second'+$(this).val(),selectedText)
                    }
                    
                    var selectedTextResult = $('select[name="visit_result_second_id'+$(this).val()+'"] option:selected').text();
                    if($('select[name="visit_result_second_id'+$(this).val()+'"] option:selected').val() !=""){
                        formData.append('visit_result_second'+$(this).val(),selectedTextResult)
                    }
                })
                
                $.ajax({
                    type:"post",
                    url:"{{ url('save_response_data_vns')}}",
                    data:formData,
                    processData: false,
                    contentType: false,
                    success:function(res){
                        window.location.href = "{{ url('esign/thankyou-esign')}}";
                    },
                    error:function(jqr){
                        $('#submit_save_esign_form').prop('disabled',false);
                        $('#btn-text').html('Save')
                        $('#btn-loader').attr('style','display:none')
                        alert('Sorry, something went wrong. Please try again.');
                    }

                })

            } catch (error) {
                $('#submit_save_esign_form').prop('disabled',false);
                $('#btn-text').html('Save')
                $('#btn-loader').attr('style','display:none')
                alert('Error generating PDF. Please try again.');
            }
        }
        function mySign() {


        getWebviewCanvas();
   

        }
        function getWebviewCanvas(documentMentId, rand, imgid) {
            $('#modal-default').modal('show');
            $('#file_upload').val('');
            $('#modal-default #signature-pad .signature-pad--body canvas').attr('width', 550);
            $('#modal-default #signature-pad .signature-pad--body canvas').attr('height', 200);
           
            
            // Initialize signature pad for this specific imgid
            var canvas = document.querySelector('#rename_canvas');
            signaturePads[imgid] = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });
            
          
        }

        function getSubmit(blob) {
        var formData = new FormData();
       

        formData.append("image", blob);
        formData.append("_token", '{{ csrf_token() }}');

        $.ajax({
          
            url: mainURL + 'esign/docusign/esign-signature', // Upload Script
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data != '') {
                    $('#employeeSignatureId').attr('src',data)
                    $('#employeeSignature').val(data)
                   $('#modal-default').modal('hide');
                } else {
                    swal({
                        title: "Error",
                        text: "Failed to save signature. Please try again.",
                        icon: "error",
                        button: "Ok",
                    });
                }
            },
            error: function(xhr, status, error) {
                swal({
                    title: "Error",
                    text: "An error occurred while saving the signature. Please try again.",
                    icon: "error",
                    button: "Ok",
                });
            }
        });
    }
    function deleteRow1(button) {
            const row = button.closest('tr');
            row.remove();
           if($('input[name="temp2[]"]').length ==1){
            $('#removeDelete2').addClass('hide');
           }
        }

        async function fetchProcedureResult(uniqId) {
            var selectedId = $('#visit_procedure_id'+uniqId).val();
            const response = await fetchCommonProceduereResult(selectedId);
            $('#visit_result'+uniqId).html("");
            $('#visit_result'+uniqId).html(response);
           
        }

        async function fetchCommonProceduereResult(selectedId){
            
            try {
                const response = await $.ajax({
                    type: "GET",
                    url: "{{ url('vns-procedure-result/by-procedure') }}",
                    data:{
                        'id':selectedId
                    }
                });

                var htmlResultResponse = '<option value="">Select Result</option>'
                if(response.data.length !=0){
                    $.each(response.data,function(i,v){
                        htmlResultResponse +='<option value="'+v.id+'">'+v.name+'</option>';
                    })
                }
                return htmlResultResponse;
            } catch (error) {
                console.error("Error fetching procedure result:", error);
                return [];
            }
        }

        async function fetchProcedureSecondResult(uniqId) {
            var selectedId = $('#visit_procedure_second_id'+uniqId).val();
            const response = await fetchCommonProceduereResult(selectedId);
            $('#visit_result_second'+uniqId).html("");
            $('#visit_result_second'+uniqId).html(response);
           
        }
    </script>
    
</body>

</html>