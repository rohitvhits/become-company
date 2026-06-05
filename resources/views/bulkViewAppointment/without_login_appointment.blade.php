<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Required meta tags -->

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Make an Appointment</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="<?php echo URL::to("/"); ?>/assets/vendors/mdi/css/materialdesignicons.min.css">

  <link rel="stylesheet" href="<?php echo URL::to("/"); ?>/assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="<?php echo URL::to("/"); ?>/assets/vendors/jqvmap/jqvmap.min.css">
  

  <link rel="stylesheet" href="<?php echo URL::to("/"); ?>/assets/vendors/flag-icon-css/css/flag-icon.min.css">

  <!-- End plugin css for this page -->

  <!-- inject:css -->

  <link rel="stylesheet" href="<?php echo URL::to("/"); ?>/assets/css/vertical-layout-light/style.css">

  <link rel="stylesheet" href="<?= URL::to('assets/css/horizontal-default-light/style.css') ?>">
  <link rel="stylesheet" href="<?= URL::to('assets/css/sweetalert2.min.css') ?>">
  <link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
  <!-- endinject -->
  <script src="<?= URL::to('assets/js/jquery.min.js') ?>"></script>
  <script src="<?= URL::to('assets/js/sweetalert2.min.js') ?>"></script>
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/modulejs/css/header.css">
  <link rel="shortcut icon" href="<?= URL::to('img/favicon.png') ?>" />
  <style>
    body {
      background: #f8fafc;
      min-height: 100vh;
      font-family: 'Roboto', Arial, sans-serif;
      margin: 0;
      display: flex;
      flex-direction: column;
    }
    .main-container {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 32px 8px;
    }
    .info-card, .form-card {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 4px 16px rgba(60,72,100,0.10);
      width: 100%;
      margin-bottom: 24px;
      padding: 28px 24px 0px 24px;
    }
    .info-card {
      margin-top: 32px;
    }
    .logo {
      display: block;
      margin: 0 auto 18px auto;
      width: 120px;
    }
    .title {
      font-weight: 700;
      color: #2d3748;
      text-align: center;
      margin-bottom: 10px;
    }
    
  
    .btn-main, .btn-cancel, #show-reschedule-form {
      font-size: 0.85rem !important;
      padding: 7px 18px !important;
      min-width: 90px;
      border-radius: 5px;
      line-height: 1.2;
    }
    #show-reschedule-form{
      margin: auto;
    }
    .btn-main {
      background: #2563eb;
      color: #fff;
      border: none;
      font-weight: 600;
      margin-top: 18px;
      margin-bottom: 0;
      transition: background 0.2s;
      cursor: pointer;
      display: block;

    }
    .btn-main:hover {
      background: #1d4ed8;
    }
    .btn-cancel {
      background: #e5e7eb;
      color: #374151;
      border: none;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn-cancel:hover {
      background: #d1d5db;
    }
    .form-actions {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 10px;
    }
   
    .title {
      font-size: 1.3rem !important;
    }
   
    .form-card {
      /* Remove display:none to ensure it's always present for flipping */
    }
    .form-group {
      margin-bottom: 18px;
    }
    label {
      font-weight: 500;
      color: #374151;
      margin-bottom: 6px;
      display: block;
    }
    select, input[type="text"] {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      font-size: 1rem;
      background: #f9fafb;
      transition: border 0.2s;
    }
    select:focus, input[type="text"]:focus {
      border-color: #2563eb;
      outline: none;
    }
  
    body.sidebar-dark.sidebar-fixed { padding-bottom: 56px; }
    @media (max-width: 600px) {
      .info-card, .form-card {
        padding: 28px 24px 0px 24px;
        max-width: 98vw;
      }
      .logo {
        width: 90px;
      }
    }
    ul li, ol li, dl li{
      line-height: 1.2 !important;
    }
    .flip-container {
      perspective: 1200px;
      width: 100%;
      max-width: 700px;
      margin: 32px auto 0 auto;
      min-height: 420px;
      position: relative;
    }
    .flipper {
      transition: 0.7s cubic-bezier(.4,2,.6,1);
      transform-style: preserve-3d;
      position: relative;
      width: 100%;
      min-height: 420px;
    }
    .flip-container.flipped .flipper {
      transform: rotateY(180deg);
    }
    .flip-front, .flip-back {
      /* position: absolute; */
      width: 100%;
      top: 0; left: 0;
      
      min-height: 420px;
      display: block !important;
      opacity: 1 !important;
    }
    .flip-front {
      z-index: 2;
    }
    
      .slot-availability-container {
    background: #f8f9fa;
    border-radius: 4px;
    padding: 10px;
}

.availability-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.availability-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 10px;
}

.availability-label {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
}

.availability-value {
    font-size: 16px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
}

.availability-value.available {
    background: #d4edda;
    color: #155724;
}

.availability-value.booked {
    background: #f8d7da;
    color: #721c24;
}

.availability-value.total {
    background: #cce5ff;
    color: #004085;
}

/* Slot Availability Styles */
.slot-availability-container {
    background: #f8f9fa;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 15px;
}

.availability-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.availability-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 10px;
}

.availability-label {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
}

.availability-value {
    font-size: 16px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
}

.availability-value.available {
    background: #d4edda;
    color: #155724;
}

.availability-value.booked {
    background: #f8d7da;
    color: #721c24;
}

.availability-value.total {
    background: #cce5ff;
    color: #004085;
}

/* No Slots Message Styles */
.no-slots-message {
    margin: 10px 0;
    padding: 10px;
    border-radius: 4px;
    display: flex;
    align-items: center;
}

.no-slots-message i {
    font-size: 16px;
    margin-right: 8px;
}

.no-slots-message .message-text {
    font-size: 14px;
    line-height: 1.4;
}

.no-slots-message.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.no-slots-message.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeeba;
    color: #856404;
}

.no-slots-message.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

/* Your Appointment */
option.your-appointment {
	background-color: #e3f2fd !important;
	color: #1976d2 !important;
	font-weight: bold !important;
}
select option.your-appointment:checked {
	background-color: #1976d2 !important;
	color: white !important;
}

/* Booked Slots */
option.booked-slot {
	background-color: #f5f5f5 !important;
	color: #9e9e9e !important;
	font-style: italic !important;
	text-decoration: line-through !important;
}

/* General Select Styling */
select {
	padding: 8px;
	border-radius: 4px;
	border: 1px solid #ddd;
	width: 100%;
	max-width: 100%;
}
select:focus {
	outline: none;
	border-color: #1976d2;
	box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.2);
}

.footer {
    width: 100%;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
    text-align: center;
    color: #64748b;
    font-size: 0.9rem;
    padding: 12px 0;
    position: relative; /* Remove fixed if you want natural flow */
    bottom: 0;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.container-scroller {
    flex: 1;
}

.footer {
    margin-top: auto;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
    text-align: center;
    padding: 12px 0;
    font-size: 0.9rem;
    color: #64748b;
}

  </style>

  <link rel="stylesheet" href="{{ asset('/assets/esign/libs/jquery-ui/jquery-ui.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
  <link href="{{ asset('assets/bootstrap-datetimepicker.min.css')}}" type="text/css" media="all"
    rel="stylesheet" />
  <link rel="shortcut icon" href="<?php echo URL::to("/"); ?>/img/logo.png" />
</head>



<body class="sidebar-dark sidebar-fixed">


  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <div class="container">
      <div class="row">
        
      </div>
      <div class="row" style="margin: 16px auto 16px 9px">
        <div class="col-md-12">
          <!-- partial -->
          <div class="main-container">
           
            <div class="flip-container" id="flip-container">
              <div class="flipper">
                <div class="flip-front info-card p-0">
                  <div class="col-md-12 p-0">
                    <div style="background: #000000;font-size: 28px;font-weight: bold;font-family: Arial, sans-serif;padding: 3px;boder-radius: 6px;border-top-right-radius: 6px;border-top-left-radius: 6px;max-width:700px">
                      <img alt src="{{URL::to('/') . '/img/logo-ny.png'}}" class="logo" style="width:190px;vertical-align:middle;margin: 8px auto 8px auto;">
                    </div>
                  </div>
                  <div id="maulik" style="padding: 28px 24px 0px 24px">
                      <div class="title" style="margin-top: 15px;">Schedule Your Appointment</div>
                      <div class="appointment-summary">
                        <p class="text-muted text-center">Book your consultation with our experienced medical professionals</p>
                      </div>
                      <form id="submit_form_id">
                        <div class="row mt-3">
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date">Full Name<span style="color:red">*</span></label>
                                <input type="text" name="full_name" class="form-control" autocomplete="off" id="full_name" placeholder="Enter Full Name">
                                <span id="full_name_error" class="error mt-2 error" for="document_type"></span>
                              </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date">Phone<span style="color:red">*</span></label>
                                <input type="text" name="phone" class="form-control" autocomplete="off" id="phone_id"  data-inputmask-alias="(999) 999-9999"  placeholder="Enter Phone">
                                <span id="phone_error" class="error mt-2 error" for="document_type"></span>
                              </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date">Email</label>
                                <input type="text" name="email" class="form-control" autocomplete="off" id="email" placeholder="Enter Email">
                                <span id="email_error" class="error mt-2 error" for="document_type"></span>
                              </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date">Agency<span style="color:red">*</span></label>
                                <input type="text" name="agency_name" class="form-control" autocomplete="off" id="agency_name" placeholder="Enter Agency">
                                <span id="agency_name_error" class="error mt-2 error" for="document_type"></span>
                              </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date">Services<span style="color:red">*</span></label>
                                <input type="text" name="service_name" class="form-control" autocomplete="off" id="service_name" placeholder="Enter Services">
                                <span id="service_name_error" class="error mt-2 error" for="document_type"></span>
                              </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date">County<span style="color:red">*</span></label>
                                <input type="text" name="county" class="form-control" autocomplete="off" id="county" placeholder="Enter County">
                                <span id="county_error" class="error mt-2 error" for="document_type"></span>
                              </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointment_date">Appointment Date<span style="color:red">*</span></label>
                                <input type="text" name="book_date" class="form-control" autocomplete="off" id="book_date" placeholder="Enter Appointment Date">
                                <span id="book_date_error" class="error mt-2 error" for="document_type"></span>
                              </div>
                          </div>
                          
                          
                        </div>
                        <div class="form-actions mb-3">
                          <button type="button" onclick="saaveDetails();" class="btn btn-primary btn-sm mb-2 btn-main" style="margin-bottom:0;">Book Appointment</button>
                            
                          
                        </div>
                              
                      </form>
                  </div>
                  
                  
                  <footer class="footer">
                    <div class="text-center">
                      <span class="text-muted">© 2019 - <?= date('Y') ?> Nybest Medical. All rights reserved.</span>
                    </div>
                    <div class="text-center mt-3">
                        <div class="row">
                            <div class="col-4">
                                <i class="fa fa-phone" style="font-size:15px;margin-right:10px;"></i>(718) 972 3693
                          </div>
                          <div class="col-4">
                                <i class="fa fa-clock" style="font-size:15px;margin-right:10px;"></i>24/7 Emergency Care
                          </div>
                          <div class="col-4">
                              <i class="fa fa-map-marker" style="font-size:15px;margin-right:10px;"></i>New York, NY
                          </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                    <i class="fa fa-envelope-o" style="font-size:15px;margin-right:10px;"></i>contact@nybestmedical.com
                      </div>
                  </footer>
                </div>
                
                
              </div>
              
            </div>
            
            
          </div>
          
        </div>
        
      </div>
      
    </div>
    
  </div>


</body>
</html>

<!-- base:js -->
<script src="<?= URL::to('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
<!-- endinject -->
<!-- Plugin js for this page-->

<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>

<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/js/jquery-confirm.min.js"></script>



<link href="<?php echo URL::to("/"); ?>/assets/libs/sweetalert/sweetalert.css" rel="stylesheet" />
<script src="<?php echo URL::to("/"); ?>/assets/libs/sweetalert/sweetalert.min.js"></script>
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script src="{{ asset('assets/bootstrap-datetimepicker.min.js')}}"></script>
<script>
  $(":input").inputmask();
  $('#book_date').datepicker({
      minDate:new Date(),
    "format": "MM/DD/YYYY",

});

function saaveDetails(){
  var full_name = $('#full_name').val();
  var phone = $('#phone_id').val();
  var email = $('#email').val();
  var agency_name =$('#agency_name').val();
  var service_name = $('#service_name').val();
  var county = $('#county').val();
  var book_date = $('#book_date').val();
  var emailRegex = /^[a-zA-Z0-9]+([._-][a-zA-Z0-9]+)*@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/gm
  var phonePattern = /^\((\d{3})\)\s(\d{3})-(\d{4})$/;
  var cnt =0;
  $('#book_date_error').html("");
  $('#full_name_error').html("");
  $('#phone_error').html("");
  $('#email_error').html("");
  $('#agency_name_error').html("");
  $('#service_name_error').html("");
  $('#county_error').html("");

  if(full_name.trim() ==''){
      $('#full_name_error').html("Please enter Full Name");
      cnt =1;
  }

  if(phone.trim() ==''){
    $('#phone_error').html("Please enter Phone no");
      cnt =1;
  }

  if(phone.trim() !=''){
    if (!phonePattern.test(phone)) {
      $('#phone_error').html("Please enter valid Phone no");
      cnt =1;
    }
  }


  if(email.trim() !=''){
    if (!emailRegex.test(email)) {
      $('#email_error').html("Please enter a valid email address");
      cnt =1;
    }
  }

  if(agency_name.trim() ==''){
    $('#agency_name_error').html("Please enter Agency");
      cnt =1;
  }
  if(service_name.trim() ==''){
    $('#service_name_error').html("Please enter Service");
      cnt =1;
  }

  if(county.trim() ==''){
    $('#county_error').html("Please enter County");
      cnt =1;
  }

  if(book_date.trim() ==''){
    $('#book_date_error').html("Please select Appointment Date");
    cnt =1;
  }

  if(cnt ==1){
    return false;
  }else{
      var formData = new FormData($('#submit_form_id')[0]);
      formData.append('_token','{{ csrf_token()}}');
    
      $.ajax({
          async:false,
          global:false,
          type:"POST",
          url:"{{ url('make-an-appointment/save-make-appointment')}}",
          data:formData,
          processData: false,
          contentType: false,
          success:function(res){
              window.location.href="{{ url('/appointment-thank-you')}}"
          },
          error:function(jqr){
              
              toastr.error(jqr.responseJson.error_msg);
          }
      })
  }
}
</script>