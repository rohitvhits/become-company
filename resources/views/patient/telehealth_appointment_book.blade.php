<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Required meta tags -->

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Telehealth Appointment Reschedule</title>

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
      max-width: 440px;
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
    .appointment-summary {
      font-size: 1.08rem;
      color: #334155;
      margin-bottom: 18px;
      line-height: 1.7;
    }
    .details-section {
      background: #f4f7fa;
      border-radius: 8px;
      padding: 18px 18px 10px 18px;
      margin-top: 18px;
      margin-bottom: 10px;
      box-shadow: 0 2px 8px rgba(60,72,100,0.06);
    }
    .details-header {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
      letter-spacing: 0.5px;
    }
    .details-list {
      padding: 0;
      margin: 0;
      list-style: none;
    }
    .details-list li {
      display: flex;
      align-items: center;
      margin-bottom: 8px;
      font-size: 0.95rem !important;
      color: #334155;
      gap: 10px;
    }
    .details-list .icon {
      color: #2563eb;
      min-width: 22px;
      text-align: center;
      font-size: 1.08rem;
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
      justify-content: flex-end;
      gap: 10px;
      margin-top: 10px;
    }
    .appointment-summary, .details-list, label, select, input[type="text"] {
      font-size: 0.85rem !important;
    }
    .title {
      font-size: 1.3rem !important;
    }
    .details-list li {
      font-size: 0.85rem !important;
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
    .footer {
      text-align: center;
      color: #64748b;
      font-size: 0.98rem;
      margin-top: auto;
      padding: 18px 0 8px 0;
    }
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
      max-width: 440px;
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
      position: absolute;
      width: 100%;
      top: 0; left: 0;
      backface-visibility: hidden;
      min-height: 420px;
      display: block !important;
      opacity: 1 !important;
    }
    .flip-front {
      z-index: 2;
    }
    .flip-back {
      transform: rotateY(180deg);
      z-index: 3;
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
  </style>

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
  <link rel="shortcut icon" href="<?php echo URL::to("/"); ?>/img/logo.png" />
</head>



<body class="sidebar-dark sidebar-fixed">



  <!--Header-part-->

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
                <div class="flip-front info-card">
                  <div class="col-md-12">
                    <div style="background: #000000;font-size: 28px;font-weight: bold;font-family: Arial, sans-serif;padding: 3px;margin-top: -28px;margin-left: -34px;margin-right: -34px;boder-radius: 6px;border-top-right-radius: 6px;border-top-left-radius: 6px;">
                      <img src="{{URL::to('/') . '/img/logo-ny.png'}}" class="logo" style="width:190px;vertical-align:middle;margin: 8px auto 8px auto;">
                    </div>
                  </div>
                  <div class="title" style="margin-top: 15px;">Telehealth Appointment</div>
                  <div class="appointment-summary">
                    Your booking is scheduled on <strong>{{ date('m/d/Y',strtotime($schedule_info->date)) ?? '' }}</strong>.
                  </div>
                  <div class="details-section">
                    <div class="details-header"><i class="fa fa-calendar-check-o"></i> Appointment Details</div>
                    <ul class="details-list">
                      <li><span class="icon"><i class="fa fa-user"></i></span><strong>Name:</strong> {{ $query->first_name }} {{$query->last_name}}</li>
                      <li><span class="icon"><i class="fa fa-calendar"></i></span><strong>Date:</strong> {{ date('m/d/Y',strtotime($schedule_info->date)) ?? '' }}</li>
                      <li><span class="icon"><i class="fa fa-clock-o"></i></span><strong>Time:</strong> {{ date('h:i A',strtotime($schedule_info->start_time)) }} - {{ date('h:i A',strtotime($schedule_info->end_time)) }}</li>
                      <li><span class="icon"><i class="fa fa-user-md"></i></span><strong>Nurse:</strong> C#{{ $schedule_info->nurse_id ?? '' }} ({{ $nurse[$schedule_info->nurse_id]['language']?? '' }})</li>
                      @if(isset($query->type) && $query->type == 'Caregiver')
                        <li><span class="icon"><i class="fa fa-language"></i></span><strong>Language:</strong> {{ $schedule_info->name ?? '' }}</li>
                      @endif
                    </ul>
                  </div>
                  <button id="show-reschedule-form" class="btn btn-primary mt-2 mb-2 btn-sm btn-main" aria-label="Click to reschedule your appointment" onclick="loadExistingAppointment();">Click to Reschedule</button>
                </div>
                @if(isset($query->type) && $query->type == 'Caregiver')
                <div class="flip-back form-card" id="reschedule-form">
                  <div style="background: #000000;font-size: 28px;font-weight: bold;font-family: Arial, sans-serif;padding: 3px;margin-top: -28px;margin-left: -24px;margin-right: -24px;boder-radius: 6px;border-top-right-radius: 6px;border-top-left-radius: 6px;">
                    <img src="{{URL::to('/') . '/img/logo-ny.png'}}" class="logo" style="width:190px;vertical-align:middle;margin: 8px auto 8px auto;">
                  </div>
                  <div class="title" style="font-size: 1.3rem; margin-bottom:18px;margin-top:18px;">Reschedule Your Appointment</div>
                  <form method="POST" name="telehealthform" id="telehealthform" action="<?php echo \URL::to('/tele-appointment-update'); ?>">
                    <div class="form-group">
                      <input type="hidden" id="key" value="{{$key}}">
                      <input type="hidden" id="tele_caregiver_service_id" name="tele_caregiver_service_id" value="{{$service}}">
                      <label for="language">Language <span style="color:red">*</span></label>
                      <select class="form-control" id="telehealth_language" name="language" required>
                        <option value="">Select Language</option>
                        @foreach($language_list as $lan)
                          <option value="{{$lan['id']}}">{{$lan['name']}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="appointment_date">Telehealth Appointment Date <span style="color:red">*</span></label>
                      <input type="text" name="date" class="form-control" autocomplete="off" id="telehealth_date_id" placeholder="mm/dd/yyyy" readonly>
                      <span id="telehealth_date_id_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <!-- Compact Slot Availability Information -->
                    <div class="slot-availability-container mb-3" style="display: none;">
                        <div class="availability-summary">
                            <div class="availability-item">
                                <span class="availability-label">Available Slots:</span>
                                <span class="availability-value available" id="available_slots">0</span>
                            </div>
                            <div class="availability-item">
                                <span class="availability-label">Booked Slots:</span>
                                <span class="availability-value booked" id="booked_slots">0</span>
                            </div>
                            <div class="availability-item">
                                <span class="availability-label">Total Slots:</span>
                                <span class="availability-value total" id="total_slots">0</span>
                            </div>
                        </div>
                    </div>
                     <!-- No Slots Available Message -->
                    <div class="no-slots-message alert alert-info" style="display: none;">
                        <i class="fa fa-info-circle mr-2"></i>
                        <span class="message-text">No slots available for the selected date and language. Please try a different date or check back later.</span>
                    </div>
                    <div class="form-group">
                      <label for="time_slot">Time Slot <span style="color:red">*</span></label>
                      <select name="telehealth_time_slot" id="telehealth_time_slot" class="form-control">
                        <option value="">Select Time Slot</option>
                      </select>
                    </div>
                    <div class="form-actions">
                      <button type="button" id="cancel-reschedule" class="btn btn-cancel btn-sm mb-2 btn btn-secondary btn-main">Cancel</button>
                      <button type="button" onclick="telehealthUpdate();" class="btn btn-primary btn-sm mb-2 btn-main" style="margin-bottom:0;">Save</button>
                    </div>
                  </form>
                </div>
                @else
                <div class="flip-back form-card" id="reschedule-form">
                  <div style="background: #000000;font-size: 28px;font-weight: bold;font-family: Arial, sans-serif;padding: 3px;margin-top: -28px;margin-left: -24px;margin-right: -24px;boder-radius: 6px;border-top-right-radius: 6px;border-top-left-radius: 6px;">
                    <img src="{{URL::to('/') . '/img/logo-ny.png'}}" class="logo" style="width:190px;vertical-align:middle;margin: 8px auto 8px auto;">
                  </div>
                  <div class="title" style="font-size: 1.3rem; margin-bottom:18px;margin-top:18px;">Reschedule Your Appointment</div>
                  <form method="POST" name="telehealthPatientform" id="telehealthPatientform" action="<?php echo \URL::to('/tele-appointment-update'); ?>">
                    <input type="hidden" id="key" value="{{$key}}">
                    <input type="hidden" id="tele_patient_service_id" name="tele_patient_service_id" value="{{$service}}">
                    <div class="form-group telehealth">
                        <label for="recipient-name" class="col-form-label">Telehealth Appointment Date <span style="color:red">*</span></label>
                        <input type="text" name="date" class="form-control" autocomplete="off" id="patient_telehealth_date_id" placeholder="mm/dd/yyyy" readonly>
                        <span id="patient_telehealth_date_id_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="nurse">Nurse <span style="color:red">*</span></label>
                        <select class="form-control select2" id="telehealth_nurse" name="telehealth_nurse">
                            <option value="">Select Nurse</option>
                            @foreach($nurse as $key => $user)
                            <option value="{{ $key }}">C#{{$key}} ({{ $user['language'] }})</option>
                            @endforeach
                        </select>
                        <span id="telehealth_nurse_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Select Slot <span style="color:red">*</span></label>
                        <select class="form-control select2" id="patient_telehealth_time_slot" name="patient_telehealth_time_slot">
                            <option value="">Select Slot</option>
                            @if(isset($slot))
                                @foreach($slots as $slot)
                                <option value="{{ $slots['id'] }}">{{ $slots['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span id="patient_telehealth_time_slot_error" class="error mt-2 error" for="document_type"></span>
                    </div>
                    <div class="form-actions">
                      <button type="button" id="cancel-reschedule" class="btn btn-cancel btn-sm mb-2 btn btn-secondary btn-main">Cancel</button>
                      <button type="button" onclick="patientTelehealthUpdate();" class="btn btn-primary btn-sm mb-2 btn-main" style="margin-bottom:0;">Save</button>
                    </div>
                  </form>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End Date Picker -->
      <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"> 2019 - {{date('Y')}} &copy; Nybest Medical.
        </div>
      </footer>
      <!-- partial -->
    </div>
    <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  

  <!-- End custom js for this page-->
</body>
</html>

<!-- base:js -->
<script src="<?= URL::to('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
<!-- endinject -->
<!-- Plugin js for this page-->
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.pie.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.resize.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.world.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/peity/jquery.peity.min.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery.flot.dashes.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
<!-- End plugin js for this page-->
<!-- inject:js -->
<script src="<?= URL::to('assets/js/off-canvas.js') ?>"></script>
<script src="<?= URL::to('assets/js/hoverable-collapse.js') ?>"></script>
<script src="<?= URL::to('assets/js/template.js') ?>"></script>
<script src="<?= URL::to('assets/js/settings.js') ?>"></script>
<script src="<?= URL::to('assets/js/todolist.js') ?>"></script>
<!-- endinject -->
<!-- endinject -->

<!-- plugin js for this page -->
<script src="<?= URL::to('assets/vendors/datatables.net/jquery.dataTables.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') ?>"></script>
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="<?= URL::to('assets/js/data-table.js') ?>"></script>
<!-- plugin js for this page -->
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="<?= URL::to('assets/js/dashboard.js') ?>"></script>
<!-- End custom js for this page-->

<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/js/jquery-confirm.min.js"></script>



<link href="<?php echo URL::to("/"); ?>/assets/libs/sweetalert/sweetalert.css" rel="stylesheet" />
<script src="<?php echo URL::to("/"); ?>/assets/libs/sweetalert/sweetalert.min.js"></script>
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/modulejs/telehealth_schedule.js')}}?time={{ env('timestamp')}}"></script>
<script>
  var unavailableDates = '{{$disable_date}}';
  var _RECORD_ID  = '{{$query->id}}';
  var _RECORD_TYPE = '{{$query->type}}';
  var _CSRF_TOKEN = '{{ csrf_token() }}';
  var CSRF_TOKEN = '{{ csrf_token() }}';
    let properJson = JSON.parse(unavailableDates.replace(/&quot;/g, '"'));

    function unavailable(date) {
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var day   = ("0" + date.getDate()).slice(-2);
        var year  = date.getFullYear();
        var formattedDate = day + "-" + month + "-" + year;
        if ($.inArray(formattedDate, properJson) !== -1) {
            return [false, "", "Unavailable"]; // Disable this date
        }
        return [true, ""];
    }
  $("#telehealth_date_id").datepicker({
      minDate: new Date(),
      buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
      beforeShowDay: unavailable
  });
  $("#patient_telehealth_date_id").datepicker({
      minDate: new Date(),
      buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
      beforeShowDay: unavailable
  });

  $(function() {
    $('#show-reschedule-form').on('click', function() {
      $('#flip-container').addClass('flipped');
    });
    $('#cancel-reschedule').on('click', function() {
      $('#flip-container').removeClass('flipped');
    });
  });

  function telehealthUpdate(){
    var telehealth_language = $('#telehealth_language').val();
    var telehealth_date_id = $('#telehealth_date_id').val();
    var telehealth_time_slot = $('#telehealth_time_slot').val();
    var cnt = 0;
    $('#telehealth_language_error').html("");
    $('#telehealth_date_id_error').html("");
    $('#telehealth_time_slot_error').html("");

    if (telehealth_language == '') {
        $('#telehealth_language_error').html("Please select Language");
        cnt = 1;
    }
    if (telehealth_date_id == '') {
        $('#telehealth_date_id_error').html("Please select Date");
        cnt = 1;
    }
    if (telehealth_time_slot == '') {
        $('#telehealth_time_slot_error').html("Please select Time Slot");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    }

    // Show confirmation dialog with jQuery Confirm
    $.confirm({
        title: 'Confirm Appointment',
        content: `
            <div class="confirmation-details">
                <p><strong>Language:</strong> ${$('#telehealth_language option:selected').text()}</p>
                <p><strong>Date:</strong> ${telehealth_date_id}</p>
                <p><strong>Time Slot:</strong> ${$('#telehealth_time_slot option:selected').text()}</p>
            </div>
            <p>Are you sure you want to proceed with this appointment?</p>
        `,
        type: 'blue',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Yes, confirm appointment',
                btnClass: 'btn-blue',
                action: function() {
                    // Prepare form data
                    var formData = {
                        telehealth_language: $('#telehealth_language').val(),
                        telehealth_date_id: $('#telehealth_date_id').val(),
                        telehealth_time_slot: $('#telehealth_time_slot').val(),
                        id: _RECORD_ID,
                        _token: _CSRF_TOKEN,
                        key: '{{$key}}',
                        tele_caregiver_service_id: $('#tele_caregiver_service_id').val()
                    };

                    // Make AJAX call
                    $.ajax({
                        url: $('#telehealthform').attr('action'),
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                $.confirm({
                                    title: 'Success!',
                                    content: 'Appointment has been successfully scheduled.',
                                    type: 'green',
                                    typeAnimated: true,
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            btnClass: 'btn-green',
                                            action: function() {
                                                // Close the modal and refresh if needed
                                               location.href = "{{ url('thank-you') }}";
                                            }
                                        }
                                    }
                                });
                            } else {
                                $.confirm({
                                    title: 'Error!',
                                    content: response.message || 'Failed to schedule appointment. Please try again.',
                                    type: 'red',
                                    typeAnimated: true,
                                    buttons: {
                                        tryAgain: {
                                            text: 'Try Again',
                                            btnClass: 'btn-red',
                                            action: function() {
                                                // Allow user to try again
                                            }
                                        }
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            $.confirm({
                                title: 'Error!',
                                content: 'An error occurred while processing your request. Please try again.',
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    tryAgain: {
                                        text: 'Try Again',
                                        btnClass: 'btn-red',
                                        action: function() {
                                            // Allow user to try again
                                        }
                                    }
                                }
                            });
                        }
                    });
                }
            },
            cancel: {
                text: 'No, cancel',
                btnClass: 'btn-red',
                action: function() {
                    // Do nothing, just close the dialog
                }
            }
        }
    });

    return false;
}

  function patientTelehealthUpdate(){
    var telehealth_nurse = $('#telehealth_nurse').val();
    var telehealth_date_id = $('#patient_telehealth_date_id').val();
    var telehealth_time_slot = $('#patient_telehealth_time_slot').val();
    var cnt = 0;
    $('#telehealth_nurse_error').html("");
    $('#telehealth_date_id_error').html("");
    $('#patient_telehealth_time_slot_error').html("");

    if (telehealth_nurse == '') {
        $('#telehealth_nurse_error').html("Please select Nurse");
        cnt = 1;
    }
    if (telehealth_date_id == '') {
        $('#patient_telehealth_date_id_error').html("Please select Date");
        cnt = 1;
    }
    if (telehealth_time_slot == '') {
        $('#patient_telehealth_time_slot_error').html("Please select Time Slot");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    }

    // Show confirmation dialog with jQuery Confirm
    $.confirm({
        title: 'Confirm Appointment',
        content: `
            <div class="confirmation-details">
                <p><strong>Nurse:</strong> ${$('#telehealth_nurse option:selected').text()}</p>
                <p><strong>Date:</strong> ${telehealth_date_id}</p>
                <p><strong>Time Slot:</strong> ${$('#patient_telehealth_time_slot option:selected').text()}</p>
            </div>
            <p>Are you sure you want to proceed with this appointment?</p>
        `,
        type: 'blue',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Yes, confirm appointment',
                btnClass: 'btn-blue',
                action: function() {
                    // Prepare form data
                    var formData = {
                        telehealth_nurse: telehealth_nurse,
                        patient_telehealth_date_id: telehealth_date_id,
                        patient_telehealth_time_slot: telehealth_time_slot,
                        id: _RECORD_ID,
                        _token: _CSRF_TOKEN,
                        type: _RECORD_TYPE,
                        key: '{{$key}}',
                        tele_patient_service_id: $('#tele_patient_service_id').val()
                    };

                    // Make AJAX call
                    $.ajax({
                        url: $('#telehealthPatientform').attr('action'),
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                $.confirm({
                                    title: 'Success!',
                                    content: 'Appointment has been successfully scheduled.',
                                    type: 'green',
                                    typeAnimated: true,
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            btnClass: 'btn-green',
                                            action: function() {
                                                // Close the modal and refresh if needed
                                               location.href = "{{ url('thank-you') }}";
                                            }
                                        }
                                    }
                                });
                            } else {
                                $.confirm({
                                    title: 'Error!',
                                    content: response.message || 'Failed to schedule appointment. Please try again.',
                                    type: 'red',
                                    typeAnimated: true,
                                    buttons: {
                                        tryAgain: {
                                            text: 'Try Again',
                                            btnClass: 'btn-red',
                                            action: function() {
                                                // Allow user to try again
                                            }
                                        }
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            $.confirm({
                                title: 'Error!',
                                content: 'An error occurred while processing your request. Please try again.',
                                type: 'red',
                                typeAnimated: true,
                                buttons: {
                                    tryAgain: {
                                        text: 'Try Again',
                                        btnClass: 'btn-red',
                                        action: function() {
                                            // Allow user to try again
                                        }
                                    }
                                }
                            });
                        }
                    });
                }
            },
            cancel: {
                text: 'No, cancel',
                btnClass: 'btn-red',
                action: function() {
                    // Do nothing, just close the dialog
                }
            }
        }
    });

    return false;
}
</script>

