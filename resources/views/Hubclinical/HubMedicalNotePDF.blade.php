<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Excuse - NY Best Medical</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f5f5f5;
            padding: 40px 20px;
        }
        
        .document {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 60px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            margin-bottom: 40px;
        }
        
        .logo-section {
            text-align: left;
            /* margin-bottom: 20px; */
        }
        
        .logo {
            width: 120px;
            height: 120px;
            background: #000;
            display: inline-block;
            position: relative;
            margin-bottom: 15px;
        }
        
        .logo-heart {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #4CAF50;
            font-size: 48px;
        }
        
        .logo-text {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-weight: bold;
            font-size: 11px;
            white-space: nowrap;
        }
        
        .practice-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .address {
            font-size: 14px;
            line-height: 1.6;
        }
        
        h1 {
            text-align: center;
            font-size: 24px;
            margin: 40px 0 30px 0;
            font-weight: bold;
        }
        
        .date {
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .patient-info {
            margin-bottom: 25px;
        }
        
        .patient-name {
            font-weight: normal;
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .patient-details {
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .salutation {
            margin: 25px 0 20px 0;
            font-size: 14px;
        }
        
        .excuse-line {
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .section-label {
            font-weight: bold;
            margin: 20px 0 10px 0;
            font-size: 14px;
        }
        
        .checkbox-group {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        
        .checkbox-item {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            margin-right: 8px;
            vertical-align: middle;
            position: relative;
        }
        
        .checkbox.checked::after {
            content: 'X';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
            font-size: 12px;
        }
        
        .underline {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 400px;
        }
        
        .doctor-comment {
            margin: 25px 0;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .signature-line {
            margin-top: 40px;
            border-bottom: 1px solid #000;
            width: 350px;
            padding-bottom: 5px;
        }
        
        .doctor-info {
            font-size: 14px;
            margin-top: 5px;
        }
        
        .footer {
           margin-bottom: 10px;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
      @if (isset($data['patient_dob']) && $data['patient_dob'] != '0001-01-01' && $data['patient_dob'] !="0000-00-00" && $data['patient_dob'] != '1000-01-01' && $data['patient_dob'] != '')
                            @php $dob= date('m/d/Y', strtotime($data['patient_dob']));
                                $dob2= new DateTime($data['patient_dob']);
                                $today = new DateTime();
                                  $age = $today->diff($dob2)->y;
                            @endphp
                                @else
                              @php  $dob=$age=""; @endphp
                            @endif
                            
    <div class="document">
     <div class="footer">
            {{ $data['patient_name']??"" }}, DOB:  {{ $dob  }} @if($age==!"" && $age==!0) Age: {{ $age }} Year(s) @endif Date: {{ date('m/d/Y', strtotime($data['created_at'])) }}, Phys: PEDRO CORZO,MD - LICENSE NUMBER: 191262 Page: 1
        </div>
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    {{-- <div class="logo-heart">♥+</div>
                    <div class="logo-text">NY BEST MEDICAL</div> --}}
                    <img src="{{URL::to('/')}}/img/logo.png" style="height: 55px; width: 100%;margin-top: 15px;" alt="">
                </div>
            </div>
            
            <div class="practice-name">NY Best Medical</div>
            <div class="address">
                <b>2965 Ocean Parkway, 2B<br>
                Brooklyn, NY 11235<br>
                T: (484) 835-3633<br>
                F: (718) 972-4811</b>
            </div>
        </div>
        
        <h1>Medical excuse</h1>
        
        <div class="date">Date: {{ date('m/d/Y', strtotime($data['created_at'])) }}</div>
        
       <div class="patient-section">
                <div class="patient-name">
                    {{ $data['patient_name']??"" }}
                </div>
                <div class="patient-details" style="margin-top: 10px;">
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
                        <div>
                            <label>DOB:</label>
                          
                           {{ $dob  }}
                           
                        </div>
                        <div>@if($age==!"" && $age==!0) <label for="">Age:</label> {{ $age }} Year(s) @endif
                        </div>
                        <div>
                            <label>Sex:</label>
                           {{ $data['patient_gender']??"" }}
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        {{ $data['patient_address']??"" }}
                    </div>
                </div>
            </div>
        
        <div class="salutation">To Whom It May Concern:</div>
        
        <div class="excuse-line">Please excuse: {{ $data['excuse']??"" }}</div>
        
        <div class="section-label">From:</div>
        <div class="checkbox-group">
            <div class="checkbox-item">
                 WORK <span >{{ $data['work']??"" }}</span>
            </div>
            <div class="checkbox-item">
                 SCHOOL<span class="">{{ $data['school']??"" }}</span>
            </div>
            <div class="checkbox-item">
               OTHER:<span class="">{{ $data['other']??"" }}</span>
            </div>
        </div>
        
        <div class="section-label">Due To:</div>
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class=""></span> INJURY <span class="">{{ $data['injury']??"" }}</span>
            </div>
            <div class="checkbox-item">
                <span class=""></span> ILLNESS <span class="">{{ $data['illness']??"" }}</span>
            </div>
            <div class="checkbox-item">
                <span class=""></span> OTHER:<span class=""> {{ $data['due_to_other']??"" }}</span>
            </div>
        </div>
        
        <div class="section-label">Doctor's comment:</div>
        <div class="doctor-comment">
               {{ $data['doc_comment']??"" }}
            
        </div>
        
        <div class="signature-line"></div>
        <div class="doctor-info">{{ $data['doctor_name']??"" }}</div>
        
        
    </div>
</body>
</html>