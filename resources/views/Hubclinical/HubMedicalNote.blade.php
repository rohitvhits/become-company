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
            margin-bottom: 20px;
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
            margin-top: 60px;
            font-size: 11px;
            color: #666;
            text-align: center;
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
                2965 Ocean Parkway, 2B<br>
                Brooklyn, NY 11235<br>
                T: (484) 835-3633<br>
                F: (718) 972-4811
            </div>
        </div>
        
        <h1>Medical excuse</h1>
        
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
                            <input type="text" id="patient_dob" class="form-control hasDatepicker" name="patient_dob"
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
        
        <div class="salutation">To Whom It May Concern:</div>
        
        <div class="excuse-line">Please excuse: <input type="text" id="excuse" name="excuse" placeholder="Enter excuse"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""></div>
        
        <div class="section-label">From:</div>
        <div class="checkbox-group">
            <div class="checkbox-item">
                 WORK <span ><input type="text" id="work" name="work" placeholder="Enter Work"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""></span>
            </div>
            <div class="checkbox-item">
                 SCHOOL<span class=""><input type="text" id="school" name="school" placeholder="Enter School"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""></span>
            </div>
            <div class="checkbox-item">
               OTHER:<span class=""><input type="text" id="other" name="other" placeholder="Enter Other"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""></span>
            </div>
        </div>
        
        <div class="section-label">Due To:</div>
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class=""></span> INJURY <span class=""><input type="text" id="injury" name="injury" placeholder="Enter Injury"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""></span>
            </div>
            <div class="">
                <span class=""></span> ILLNESS <span class=""><input type="text" id="illness" name="illness" placeholder="Enter Illness"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""></span>
            </div>
            <div class="checkbox-item">
                <span class=""></span> OTHER:<span class=""> <input type="text" id="due_to_other" name="due_to_other" placeholder="Enter Other"
                           style="border: 1px solid #ccc; padding: 5px; width: 300px; font-size: 14px;"
                           value=""> </span>
            </div>
        </div>
        
        <div class="section-label">Doctor's comment:</div>
        <div class="doctor-comment">
               <textarea name="doc_comment" id="doc_comment" cols="90" rows="5"></textarea>  
            
        </div>
        
        <div class="signature-line"></div>
        <div class="doctor-info"></div>
        
        <div class="footer">
            {{ $data->first_name??'' }} {{ $data->middle_name??'' }} {{ $data->last_name??'' }}, DOB: {{ $dob }}, Date: {{ date('m/d/Y') }}, Phys: PEDRO CORZO, MD - LICENSE NUMBER: 191262 Page: 1
        </div>
    </div>
</body>
<script src="{{ URL::to('/') }}/assets/js/jquery.min.js"></script>
	<script src="{{ URL::to('/') }}/assets/vendors/inputmask/jquery.inputmask.bundle.js"></script>
<script>
    $('#patient_dob').inputmask();  
    </script>
</html>