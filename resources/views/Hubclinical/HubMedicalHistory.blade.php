<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Visit Report - NY Best Medical</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f5f5f5;
            padding: 20px;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .document {
            max-width: 850px;
            margin: 0 auto;
            background: white;
            padding: 40px 50px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .page {
            page-break-after: always;
        }
        
        .page:last-child {
            page-break-after: auto;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            background: #000;
            display: inline-block;
            position: relative;
            margin-bottom: 10px;
        }
        
        .logo-heart {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #4CAF50;
            font-size: 40px;
        }
        
        .logo-text {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-weight: bold;
            font-size: 10px;
            white-space: nowrap;
        }
        
        .practice-info {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .contact-info {
            font-size: 12px;
            line-height: 1.4;
        }
        
        .date {
            margin: 20px 0;
        }
        
        .patient-section {
            margin-bottom: 25px;
        }
        
        .patient-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .patient-details {
            margin-left: 20px;
            margin-bottom: 5px;
        }
        
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .subsection-title {
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 5px;
        }
        
        .content-block {
            margin-bottom: 15px;
            text-align: justify;
        }
        
        .indent {
            margin-left: 20px;
        }
        
        .vitals-grid {
            margin: 10px 0;
        }
        
        .diagnosis-code {
            font-family: 'Courier New', monospace;
            margin-bottom: 5px;
        }
        
        .medication-item {
            margin-bottom: 8px;
        }
        
        .signature-section {
            margin-top: 40px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 300px;
            margin: 30px 0 5px 0;
        }
        
        .footer {
            margin-top: 40px;
            font-size: 11px;
            color: #666;
        }
        
        .page-break {
            margin-top: 50px;
            border-top: 2px dashed #ccc;
            padding-top: 30px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .document {
                box-shadow: none;
                padding: 0;
            }
            
            .page-break {
                page-break-before: always;
                border: none;
                padding-top: 0;
            }
        }
    </style>
</head>
<body>
      @if (isset($data->dob) && $data->dob != '0001-01-01' && $data->dob !="0000-00-00" && $data->dob != '1000-01-01' && $data->dob != '')
                            @php $dob= date('m/d/Y', strtotime($data->dob));
                                $dob2 = new DateTime($data->dob);
                                $today = new DateTime();
                                  $age = $today->diff($dob2)->y;
                                   
                            @endphp
                                @else
                              @php  $dob=$age=""; @endphp
                            @endif
    <div class="document">
        <!-- Page 1 -->
        <div class="page">
            <div class="header">
                <div class="logo">
                    {{-- <div class="logo-heart">♥+</div>
                    <div class="logo-text">NY BEST MEDICAL</div> --}}
                    <img src="{{URL::to('/')}}/img/logo.png" style="height: 55px; width: 100%;margin-top: 15px;" alt="">
                </div>
                <div class="practice-info">NY Best Medical</div>
                <div class="contact-info">
                    <strong>2965 Ocean Parkway, 2B</strong><br>
                    <strong>Brooklyn, NY 11235</strong><br>
                    <strong>T: (484) 835-3633</strong><br>
                    <strong>F: (718) 972-4811</strong>
                </div>
            </div>
            
            <div class="date">Date: {{ date('m/d/Y') }}</div>
            
            <div class="patient-section">
                <div class="patient-name">
                    <input type="text" id="patient_name" name="patient_name" placeholder="Enter patient name"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="{{ $data->first_name??'' }} {{ $data->middle_name??'' }} {{ $data->last_name??'' }}">
                </div>
                <div class="patient-details" style="margin-top: 10px;">
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
                        <div>
                            <label>DOB:</label>
                            <input type="text" id="patient_dob" name="patient_dob" class="form-control hasDatepicker"
                                   style="border: 1px solid #ccc; padding: 5px; margin-left: 5px;"
                                    data-inputmask="'alias': 'datetime'"
                                                                                    data-inputmask-inputformat="mm/dd/yyyy"
                                                                                    im-insert="false"
                                                                                    value="<?php if ($data->dob != '') {
                                                                                        echo date('m/d/Y', strtotime($data->dob));
                                                                                    } ?>" >
                        </div>
                        <div>
                            <label>Sex:</label>
                            <input type="radio" id="male" name="patient_gender" value="male" style="margin-left: 10px;" @if(ucfirst($data->gender) =='Male') checked @endif>
                            <label for="male" style="margin-right: 10px;">Male</label>
                            <input type="radio" id="female" name="patient_gender" value="female" checked style="margin-left: 10px;">
                            <label for="female" @if(ucfirst($data->gender) =='Female') checked @endif>Female</label>
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        <input type="text" id="patient_address" name="patient_address" placeholder="Enter patient address"
                               style="border: 1px solid #ccc; padding: 5px; width: 400px; font-size: 14px;"
                               value="{{ $data->address1??'' }} {{ $data->address2??'' }} , {{ $data->city??'' }} , {{ $data->state??'' }} , {{ $data->zip??'' }} ">
                    </div>
                </div>
            </div>
            
            <div class="content-block">
                <strong>Chief Complaint:</strong><input type="text" id="chief_complaint" name="chief_complaint" placeholder="Enter Chief Complaint"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> <br>
                <strong>Reason for Visit:</strong><input type="text" id="reason_for_visit" name="reason_for_visit" placeholder="Enter Reason for Visit"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> 
            </div>
            
            <div class="section-title">History of Present Illness:</div>
            <div class="content-block">
                <textarea name="history_of_present_illness" id="" cols="100" rows="10"></textarea>
            </div>
            
            <div class="section-title">Patient History:</div>
            <div class="content-block">
                <strong>Medical History:</strong><input type="text" id="medical_history" name="medical_history" placeholder="Enter Medical History"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> <br>
                <strong>Current medications:</strong><input type="text" id="current_medications" name="current_medications" placeholder="Enter Current medications"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> <br>
                <strong>Past Surgical History:</strong><input type="text" id="past_surgical_history" name="past_surgical_history" placeholder="Enter Past Surgical History"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> <br>
                <strong>Social History:</strong> <input type="text" id="social_history" name="social_history" placeholder="Enter Past Surgical History"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">
                           <br>
                <strong>Allergies:</strong> <input type="text" id="allergies" name="allergies" placeholder="Enter Allergies"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">
            </div>
            
            <div class="section-title">Review of Systems:</div>
            <div class="content-block">
                Cardiovascular:<input type="text" id="cardiovascular" name="cardiovascular" placeholder="Enter Cardiovascular"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">, <br>
                Constitutional:<input type="text" id="en" name="constitutional" placeholder="Enter Constitutional"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">, <br>
                ENT:<input type="text" id="ent" name="ent" placeholder="Enter ENT"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">, <br>
                Endocrine:<input type="text" id="endocrine" name="endocrine" placeholder="Enter Endocrine"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">, <br>
                Gastrointestinal:<input type="text" id="gastrointestinal" name="gastrointestinal" placeholder="Enter Gastrointestinal"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> ,<br>
                Genitourinary:<input type="text" id="genitourinary" name="genitourinary" placeholder="Enter Genitourinary"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">,<br>
                Musculoskeletal:<input type="text" id="musculoskeletal" name="musculoskeletal" placeholder="Enter Musculoskeletal"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">,<br>
                Neurologic <input type="text" id="neurologic" name="neurologic" placeholder="Enter Neurologic"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">,<br>
                Ophthalmologic: <input type="text" id="ophthalmologic" name="ophthalmologic" placeholder="Enter Ophthalmologic"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">,<br>
                Psychiatric: <input type="text" id="psychiatric" name="psychiatric" placeholder="Enter Psychiatric"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">,<br>
                Respiratory:<input type="text" id="respiratory" name="respiratory" placeholder="Enter Respiratory"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">,<br>
                Skin: <input type="text" id="skin" name="skin" placeholder="Enter Skin"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value="">,
            </div>
            
            <div class="vitals-grid">
                <strong>Vitals:</strong> BP: <input type="text" id="bp" name="bp" placeholder="Enter bp"
                           style="border: 1px solid #ccc; padding: 5px; width: 100px; font-size: 14px;"
                           value="">; Pulse: <input type="text" id="pulse" name="pulse" placeholder="Enter Pulse"
                           style="border: 1px solid #ccc; padding: 5px; width: 100px; font-size: 14px;"
                           value="">; Resp: <input type="text" id="resp" name="resp" placeholder="Enter resp"
                           style="border: 1px solid #ccc; padding: 5px; width: 100px; font-size: 14px;"
                           value="">; Temp:<input type="text" id="temp" name="temp" placeholder="Enter Temp"
                           style="border: 1px solid #ccc; padding: 5px; width: 100px; font-size: 14px;"
                           value="">; Weight: <input type="text" id="weight" name="weight" placeholder="Enter Weight"
                           style="border: 1px solid #ccc; padding: 5px; width: 100px; font-size: 14px;"
                           value="">; Height: <input type="text" id="height" name="height" placeholder="Enter Height"
                           style="border: 1px solid #ccc; padding: 5px; width: 100px; font-size: 14px;"
                           value="">; BMI: <input type="text" id="bmi" name="bmi" placeholder="Enter BMI"
                           style="border: 1px solid #ccc; padding: 5px; width: 100px; font-size: 14px;"
                           value="">;
            </div>
            
            <div class="section-title">Physical Exam:</div>
            <div class="content-block">
                <strong>Appearance:</strong><input type="text" id="appearance" name="appearance" placeholder="Enter Appearance"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""><br>
                <strong>HEENT:</strong><input type="text" id="heent" name="heent" placeholder="Enter HEENT"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""><br>
                <strong>Neck:</strong> </strong><input type="text" id="neck" name="neck" placeholder="Enter Neck"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""><br>
                <strong>Cardiovascular:</strong><input type="text" id="cardiovascular" name="cardiovascular_exam" placeholder="Enter Cardiovascular"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> <br>
                <strong>Lungs:</strong><input type="text" id="lungs" name="lungs" placeholder="Enter Lungs"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""><br>
                <strong>Abdomen:</strong><input type="text" id="abdomen" name="abdomen" placeholder="Enter Abdomen"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> <br>
                <strong>Extremities:</strong><input type="text" id="extremities" name="extremities" placeholder="Enter Extremities"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> <br>
                <strong>Neuro:</strong><input type="text" id="neuro" name="neuro" placeholder="Enter Neuro"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> 
            </div>
            
            <div class="section-title">Diagnosis:</div>
            <div class="diagnosis-code">
              
                            <textarea name="diagnosis" id="diagnosis" cols="100" rows="3"></textarea>  
            </div>
            
            <div class="section-title">Assessment/Plan:</div>
            <div class="content-block">
                 <textarea name="assessment_plan" id="assessment_plan" cols="100" rows="3"></textarea>
            </div>
            
            <div class="section-title">Instructions:</div>
            <div class="content-block">
                <textarea name="instructions" id="instructions" cols="100" rows="8"></textarea>
                
            </div>
            
            {{-- <div class="footer">
                MELISSA ANNE CLAYTON, DOB: 1/9/1991, Date: 10/8/2025, Phys: PEDRO CORZO, MD - LICENSE NUMBER: 191262 Page: 1
            </div> --}}
        </div>
        
        <!-- Page 2 -->
        <div class="page page-break">
            <div class="footer" style="margin-top: 0; margin-bottom: 30px;">
                {{ $data->first_name??'' }} {{ $data->middle_name??'' }} {{ $data->last_name??'' }}, DOB: {{ $dob }}, Date: {{ date('m/d/Y') }}, Phys: PEDRO CORZO, MD - LICENSE NUMBER: 191262 Page: 2
            </div>
            
            <div class="section-title">Medications:</div>
            <div class="content-block">
                <div class="medication-item">
                    <textarea name="medications" id="medications" cols="100" rows="8"></textarea>
                    
                </div>
                {{-- <div class="medication-item">
                   
                </div> --}}
            </div>
            
            <div class="signature-section">
                <div>Thank you,</div>
                <div class="signature-line"></div>
                <div><strong></strong></div>
            </div>
        </div>
    </div>
</body>
<script src="{{ URL::to('/') }}/assets/js/jquery.min.js"></script>
	<script src="{{ URL::to('/') }}/assets/vendors/inputmask/jquery.inputmask.bundle.js"></script>
<script>
 
        $('#patient_dob, #visit_date, #excuse_from, #excuse_to').inputmask();
    </script>
</html>