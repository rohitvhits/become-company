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
            /* line-height: 1.4; */
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
            
            <div class="date">Date: {{ date('m/d/Y', strtotime($data['created_at'])) }}</div>
            
            <div class="patient-section">
                <div class="patient-name">
                    {{ $data['patient_name']??"" }}
                    
                </div>
                <div class="patient-details" style="margin-top: 10px;">
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
                        <div>
                            <label>DOB:</label>
                       @if (isset($data['patient_dob']) && $data['patient_dob'] != '0001-01-01' && $data['patient_dob'] !="0000-00-00" && $data['patient_dob'] != '1000-01-01' && $data['patient_dob'] != '')
                            @php $dob= date('m/d/Y', strtotime($data['patient_dob']));
                                $dob2 = new DateTime($data['patient_dob']);
                                $today = new DateTime();
                                  $age = $today->diff($dob2)->y;
                                   
                            @endphp
                                @else
                              @php  $dob=$age=""; @endphp
                            @endif
                           {{ $dob  }} @if($age==!"" && $age==!0) Age: {{ $age }} Year(s) @endif <label>Sex:</label>
                            {{ $data['patient_gender']??"" }}
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        {{ $data['patient_address']??"" }}
                    </div>
                </div>
            </div>
            
            <div class="content-block">
                <strong>Chief Complaint:</strong>{{ $data['chief_complaint']??"" }} <br>
                <strong>Reason for Visit:</strong>{{ $data['reason_for_visit']??"" }} <br>
            </div>
            
            <div class="section-title">History of Present Illness:</div>
            <div class="content-block">
               {{ $data['history_of_present_illness']??"" }}
            </div>
            
            <div class="section-title">Patient History:</div>
            <div class="content-block">
                <strong>Medical History:</strong>{{ $data['medical_history']??"" }} <br>
                <strong>Current medications:</strong>{{ $data['current_medications']??"" }} <br>
                <strong>Past Surgical History:</strong>{{ $data['past_surgical_history']??"" }} <br>
                <strong>Social History:</strong> {{ $data['social_history']??"" }} <br>
                <strong>Allergies:</strong> {{ $data['allergies']??"" }}
            </div>
            
            <div class="section-title">Review of Systems:</div>
            <div class="content-block">
                Cardiovascular: {{ $data['cardiovascular']??"" }}, <br>
                Constitutional: {{ $data['constitutional']??"" }}, <br>
                ENT: {{ $data['ent']??"" }}, <br>
                Endocrine: {{ $data['endocrine']??"" }}, <br>
                Gastrointestinal: {{ $data['gastrointestinal']??"" }} ,<br>
                Genitourinary: {{ $data['genitourinary']??"" }},<br>
                Musculoskeletal: {{ $data['musculoskeletal']??"" }},<br>
                Neurologic: {{ $data['neurologic']??"" }},<br>
                Ophthalmologic: {{ $data['ophthalmologic']??"" }},<br>
                Psychiatric: {{ $data['psychiatric']??"" }},<br>
                Respiratory: {{ $data['respiratory']??"" }},<br>
                Skin: {{ $data['skin']??"" }},
            </div>
            
            <div class="vitals-grid">
                <strong>Vitals:</strong> BP: {{ $data['bp']??"" }}; Pulse: {{ $data['pulse']??'' }}; Resp: {{ $data['resp']??"" }}; Temp: {{ $data['temp']??"" }}; Weight: {{ $data['weight']??"" }}; Height: {{ $data['height']??"" }}; BMI: {{ $data['bmi']??"" }};
            </div>
            
            <div class="section-title">Physical Exam:</div>
            <div class="content-block">
                <strong>Appearance:</strong>{{ $data['appearance']??"" }}<br>
                <strong>HEENT:</strong>{{ $data['heent']??"" }}<br>
                <strong>Neck:</strong> </strong>{{ $data['neck']??"" }}<br>
                <strong>Cardiovascular:</strong>{{ $data['cardiovascular_exam']??"" }} <br>
                <strong>Lungs:</strong>{{ $data['lungs']??"" }}<br>
                <strong>Abdomen:</strong>{{ $data['abdomen']??"" }} <br>
                <strong>Extremities:</strong>{{ $data['extremities']??"" }} <br>
                <strong>Neuro:</strong>{{ $data['neuro']??"" }} <br>
            </div>
            
            <div class="section-title">Diagnosis:</div>
            <div class="diagnosis-code">
              
                           {{ $data['diagnosis']??"" }}
            </div>
            
            <div class="section-title">Assessment/Plan:</div>
            <div class="content-block">
                {{ $data['assessment_plan']??"" }}
            </div>
            
            <div class="section-title">Instructions:</div>
            <div class="content-block">
                {{ $data['instructions']??"" }}
                
            </div>
            
            {{-- <div class="footer">
                MELISSA ANNE CLAYTON, DOB: 1/9/1991, Date: 10/8/2025, Phys: PEDRO CORZO, MD - LICENSE NUMBER: 191262 Page: 1
            </div> --}}
        </div>
        
        <!-- Page 2 -->
        <div class="page page-break">
            <div class="footer" style="margin-top: 0; margin-bottom: 30px;">
                {{ $data['patient_name']??"" }}, DOB: {{ $dob  }}, Date: {{ date('m/d/Y', strtotime($data['created_at'])) }}, Page: 2
            </div>
            
            <div class="section-title">Medications:</div>
            <div class="content-block">
                <div class="medication-item">
                    {{ $data['medications']??"" }}
                    
                </div>
                {{-- <div class="medication-item">
                   
                </div> --}}
            </div>
            
            <div class="signature-section">
                <div>Thank you,</div>
                <div class="signature-line"></div>
                <div><strong>{{ $data['doctor_name']??"" }}</strong></div>
                <div><strong>Date: {{ date('m/d/Y', strtotime($data['created_at'])) }}</strong></div>
            </div>
        </div>
    </div>
</body>
</html>