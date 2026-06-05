<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Visit Report - NY Best Medical</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .main-table {
            max-width: 850px;
            margin: 0 auto;
        }
        
        .logo-cell {
            width: 120px;
            height: 100px;
            background-color: #000000;
            text-align: center;
            vertical-align: middle;
            position: relative;
        }
        
        .logo-img {
            max-width: 100px;
            max-height: 80px;
        }
        
        .header-info {
            font-weight: bold;
            font-size: 10px;
            line-height: 1.4;
        }
        
        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        
        .content-row td {
            padding: 5px 0;
            vertical-align: top;
        }
        
        .label-bold {
            font-weight: bold;
        }
        
        .diagnosis-code {
            font-family: 'Courier New', monospace;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 300px;
            margin-top: 20px;
        }
        
        /* .page-break {
            page-break-before: always;
        } */
        
        .footer-text {
            font-size: 11px;
            color: #666666;
            margin-top: 20px;
        }
        
        .indent-left {
            padding-left: 20px;
        }
        
        .text-justify {
            text-align: justify;
        }
    </style>
</head>
<body>
    <!-- PAGE 1 -->
    <table class="main-table" cellpadding="0" cellspacing="0">
        <!-- Header Section -->
        <tr>
            <td colspan="2">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 120px; vertical-align: top;">
                            <!-- Logo placeholder - Replace with actual image -->
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="logo-cell">
                                        <span></span>
                                        <span></span>
                                        <br>
                                        <img src="{{ public_path('img/logo.png') }}" style="margin-top: 15px;" alt="NY Best Medical Logo">
                                    </td>
                                </tr>
                            </table>
                        </td>
                         </tr>
                         <tr>
                        <td style="vertical-align: top; padding-left: 10px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="header-info">
                                        <strong>NY Best Medical</strong><br>
                                        <strong>2965 Ocean Parkway, 2B</strong><br>
                                        <strong>Brooklyn, NY 11235</strong><br>
                                        <strong>T: (484) 835-3633</strong><br>
                                        <strong>F: (718) 972-4811</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Date -->
         <tr>
            <td></td>
         </tr>
        <tr>
            <td colspan="2" style="padding-top: 20px; padding-bottom: 10px;">
                Date: {{ date('m/d/Y', strtotime(date('Y-m-d'))) }}
            </td>
        </tr>
        <tr>
            <td></td>
         </tr>
        <!-- Patient Information -->
        <tr>
            <td colspan="2" style="padding-bottom: 15px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="font-weight: bold; padding-bottom: 5px;">
                            {{ $data['patient_name']??"" }}
                        </td>
                    </tr>
                    <tr>
                        <td class="indent-left">
                            <table cellpadding="2" cellspacing="0">
                                <tr>
                                    <td colspan="4">    <strong>DOB:</strong>  @if (isset($data['patient_dob']) && $data['patient_dob'] != '0001-01-01' && $data['patient_dob'] !="0000-00-00" && $data['patient_dob'] != '1000-01-01' && $data['patient_dob'] != '')
                            @php $dob= date('m/d/Y', strtotime($data['patient_dob']));
                                $dob2 = new DateTime($data['patient_dob']);
                                $today = new DateTime();
                                  $age = $today->diff($dob2)->y;
                            @endphp
                                @else
                              @php  $dob=$age=""; @endphp
                            @endif
                                        {{ $dob }} @if($age==!"" && $age==!0) <strong>Age:</strong> {{ $age }} Year(s) @endif  <strong>Sex:</strong> {{ $data['patient_gender']??"" }}
                                    </td>
                                   
                                    {{-- <td style="padding-left: 20px;"></td>
                                    <td></td> --}}
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding-top: 5px;">
                                        {{ $data['patient_address']??"" }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Chief Complaint & Reason for Visit -->
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr class="content-row">
            <td colspan="2">
                <strong>Chief Complaint:</strong> {{ $data['chief_complaint']??"" }}<br>
                <strong>Reason for Visit:</strong> {{ $data['reason_for_visit']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- History of Present Illness -->
        <tr>
            <td colspan="2" class="section-title">History of Present Illness:</td>
        </tr>
        <tr class="content-row">
            <td colspan="2" class="text-justify">
                {{ $data['history_of_present_illness']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Patient History -->
        <tr>
            <td colspan="2" class="section-title">Patient History:</td>
        </tr>
        <tr class="content-row">
            <td colspan="2">
                <strong>Medical History:</strong> {{ $data['medical_history']??"" }}<br>
                <strong>Current medications:</strong> {{ $data['current_medications']??"" }}<br>
                <strong>Past Surgical History:</strong> {{ $data['past_surgical_history']??"" }}<br>
                <strong>Social History:</strong> {{ $data['social_history']??"" }} <br>
                <strong>Allergies:</strong> {{ $data['allergies']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Review of Systems -->
        <tr>
            <td colspan="2" class="section-title">Review of Systems:</td>
        </tr>
        <tr class="content-row">
            <td colspan="2">
                Cardiovascular: {{ $data['cardiovascular']??"" }},<br>
                Constitutional: {{ $data['constitutional']??"" }},<br>
                ENT: {{ $data['ent']??"" }},<br>
                Endocrine: {{ $data['endocrine']??"" }},<br>
                Gastrointestinal: {{ $data['gastrointestinal']??"" }},<br>
                Genitourinary: {{ $data['genitourinary']??"" }},<br>
                Musculoskeletal: {{ $data['musculoskeletal']??"" }},<br>
                Neurologic: {{ $data['neurologic']??"" }},<br>
                Ophthalmologic: {{ $data['ophthalmologic']??"" }},<br>
                Psychiatric: {{ $data['psychiatric']??"" }},<br>
                Respiratory: {{ $data['respiratory']??"" }},<br>
                Skin: {{ $data['skin']??"" }},
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Vitals -->
        <tr class="content-row">
            <td colspan="2" style="padding-top: 5px;">
                <strong>Vitals:</strong> BP: {{ $data['bp']??"" }}; Pulse: {{ $data['pulse']??'' }}; Resp: {{ $data['resp']??"" }}; Temp: {{ $data['temp']??"" }}; Weight: {{ $data['weight']??"" }}; Height: {{ $data['height']??"" }}; BMI: {{ $data['bmi']??"" }};
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Physical Exam -->
        <tr>
            <td colspan="2" class="section-title">Physical Exam:</td>
        </tr>
        <tr class="content-row">
            <td colspan="2">
                <strong>Appearance:</strong> {{ $data['appearance']??"" }}<br>
                <strong>HEENT:</strong> {{ $data['heent']??"" }}<br>
                <strong>Neck:</strong> {{ $data['neck']??"" }}<br>
                <strong>Cardiovascular:</strong> {{ $data['cardiovascular_exam']??"" }}<br>
                <strong>Lungs:</strong> {{ $data['lungs']??"" }}<br>
                <strong>Abdomen:</strong> {{ $data['abdomen']??"" }}<br>
                <strong>Extremities:</strong> {{ $data['extremities']??"" }}<br>
                <strong>Neuro:</strong> {{ $data['neuro']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Diagnosis -->
        <tr>
            <td colspan="2" class="section-title">Diagnosis:</td>
        </tr>
        <tr class="content-row">
            <td colspan="2" class="diagnosis-code">
                {{ $data['diagnosis']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Assessment/Plan -->
        <tr>
            <td colspan="2" class="section-title">Assessment/Plan:</td>
        </tr>
        <tr class="content-row">
            <td colspan="2" class="text-justify">
                {{ $data['assessment_plan']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Instructions -->
        <tr>
            <td colspan="2" class="section-title">Instructions:</td>
        </tr>
        <tr class="content-row">
            <td colspan="2" class="text-justify">
                {{ $data['instructions']??"" }}
            </td>
        </tr>
    </table>
    
    <!-- PAGE 2 -->
    <table class="main-table page-break" cellpadding="0" cellspacing="0">
        <!-- Page 2 Header -->
        <tr>
            <td colspan="2" class="footer-text" style="padding-bottom: 20px;">
                {{-- {{ $data['patient_name']??"" }}, DOB: {{ $dob }}, Date: {{ date('m/d/Y', strtotime(date('Y-m-d'))) }}, Page: 2 --}}
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Medications -->
        <tr>
            <td colspan="2" class="section-title">Medications:</td>
        </tr>
        <tr class="content-row">
            <td colspan="2">
                {{ $data['medications']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <!-- Signature Section -->
        <tr>
            <td colspan="2" style="padding-top: 30px;">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td>Thank you,</td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="signature-line">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 5px;">
                            <strong>{{ $data['doctor_name']??"" }}</strong><br>
                            <strong>Date: {{ isset($data['created_at']) && $data['created_at'] ? date('m/d/Y', strtotime($data['created_at'])) : date('m/d/Y') }}</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>