<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Excuse - NY Best Medical</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            line-height: 1.6;
            margin: 0;
            padding: 40px 20px;
        }
        
        table {
            border-collapse: collapse;
        }
        
        .main-table {
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }
        
        .logo-cell {
            width: 110px;
            height: 100px;
            background-color: #000000;
            text-align: center;
            vertical-align: middle;
        }
        
        .logo-img {
            max-width: 100px;
            max-height: 100px;
        }
        
        .header-text {
            font-weight: bold;
            font-size: 10px;
            line-height: 1.6;
        }
        
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            padding: 30px 0;
        }
        
        .section-label {
            font-weight: bold;
            padding-top: 15px;
            padding-bottom: 8px;
        }
        
        .checkbox-row {
            padding-left: 20px;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        
        .underline-cell {
            border-bottom: 1px solid #000;
            width: 400px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 250px;
            padding-top: 30px;
            padding-bottom: 5px;
        }
        
        .footer-text {
            font-size: 10px;
            color: #666666;
            text-align: center;
            padding-top: 40px;
        }
        
        .indent-left {
            padding-left: 20px;
        }
        
        .text-justify {
            text-align: justify;
        }
        
        .spacing-top {
            padding-top: 10px;
        }
        
        .spacing-bottom {
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    @if (isset($data['patient_dob']) && $data['patient_dob'] != '0001-01-01' && $data['patient_dob'] !="0000-00-00" && $data['patient_dob'] != '1000-01-01' && $data['patient_dob'] != '')
                                          @php $dob= date('m/d/Y', strtotime($data['patient_dob']));
                                $dob2 = new DateTime($data['patient_dob']);
                                $today = new DateTime();
                                $age = $today->diff($dob2)->y;
                            @endphp
                                @else
                              @php  $dob=$age=""; @endphp
                            @endif
    <table class="main-table" cellpadding="0" cellspacing="0">
        <!-- Header Section -->
         <tr>
            <td colspan="2" class="footer-text">
                {{ $data['patient_name']??"" }} DOB: {{ $dob }} @if($age==!"" && $age==!0) Age: {{ $age }} Year(s) @endif Date: {{ date('m/d/Y', strtotime(date('Y-m-d'))) }}, Phys: PEDRO CORZO,MD - LICENSE NUMBER: 191262 Page: 1
            </td>
        </tr>
        <tr>
                <td colspan="2">
                   
                </td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 120px; vertical-align: top;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="logo-cell">
                                        <span></span>
                                        <span></span>
                                        <br>
                                        <img src="{{ public_path('img/logo.png') }}"  alt="NY Best Medical Logo">
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="vertical-align: top; padding-left: 0px;">
                            <!-- Empty space for alignment -->
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 15px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="header-text">
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
        
        <!-- Title -->
        <tr>
            <td colspan="2" class="title">
                Medical excuse
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <!-- Date -->
        <tr>
            <td colspan="2" style="padding-bottom: 20px;">
                Date: {{ date('m/d/Y', strtotime(date('Y-m-d'))) }}
            </td>
        </tr>
         <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <!-- Patient Information -->
        <tr>
            <td colspan="2" style="padding-bottom: 20px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding-bottom: 5px;">
                            <strong>{{ $data['patient_name']??"" }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="indent-left">
                            <table cellpadding="2" cellspacing="0">
                                <tr>
                                    <td>  DOB: {{ $dob }} @if($age==!"" && $age==!0) Age: {{ $age }} Year(s) @endif Sex: {{ $data['patient_gender']??"" }}
                                   </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="indent-left">
                            <table cellpadding="2" cellspacing="0">
                                <tr>
                                    <td>  {{ $data['patient_address']??"" }}
                                   </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <!-- Salutation -->
        <tr>
            <td colspan="2" style="padding-top: 15px; padding-bottom: 15px;">
                To Whom It May Concern:
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <!-- Please excuse -->
        <tr>
            <td colspan="2" style="padding-bottom: 15px;">
                Please excuse: {{ $data['excuse']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <!-- From Section -->
        <tr>
            <td colspan="2" class="section-label">
                From:
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="checkbox-row">
                           <strong>WORK:</strong> <span style="padding-left: 10px;">{{ $data['work']??"" }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="checkbox-row">
                            <strong>SCHOOL:</strong> <span class="underline-cell" style="padding-left: 10px;">{{ $data['school']??"" }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="checkbox-row">
                           <strong>OTHER:</strong> <span class="underline-cell" style="padding-left: 10px;">{{ $data['other']??"" }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <!-- Due To Section -->
        <tr>
            <td colspan="2" class="section-label">
                Due To:
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="checkbox-row">
                           <strong>INJURY:</strong> <span class="underline-cell" style="padding-left: 10px;">{{ $data['injury']??"" }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="checkbox-row">
                            <strong>ILLNESS:</strong> <span style="padding-left: 10px;">{{ $data['illness']??"" }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="checkbox-row">
                            <strong>OTHER:</strong> <span class="underline-cell" style="padding-left: 10px;">{{ $data['due_to_other']??"" }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <!-- Doctor's Comment -->
        <tr>
            <td colspan="2" class="section-label">
                Doctor's comment:
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 10px; padding-bottom: 20px;">
                {{ $data['doc_comment']??"" }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
               
            </td>
        </tr>
        <!-- Signature Section -->
        <tr>
            <td colspan="2">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="signature-line">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 5px;">
                            <strong>{{ $data['doctor_name']??"" }}</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer -->
       
    </table>
</body>
</html>