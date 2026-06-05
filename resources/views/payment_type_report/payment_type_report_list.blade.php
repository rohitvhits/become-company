@include('include/header')
@include('include/sidebar')


<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css') }}">

<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
  rel="stylesheet" />


<style>
  :root {
    --primary-color: #4F46E5;
    --secondary-color: #10B981;
    --danger-color: #EF4444;
    --warning-color: #F59E0B;
    --info-color: #3B82F6;
    --dark-color: #1F2937;
    --light-gray: #F3F4F6;
    --border-color: #E5E7EB;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    margin-top: 1.5rem;
    justify-items: center;
  }



  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--primary-color), var(--info-color));
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
  }

  .stat-card:nth-child(4n+1)::before {
    background: linear-gradient(180deg, #6366F1, #8B5CF6);
  }

  .stat-card:nth-child(4n+2)::before {
    background: linear-gradient(180deg, #10B981, #059669);
  }

  .stat-card:nth-child(4n+3)::before {
    background: linear-gradient(180deg, #F59E0B, #D97706);
  }

  .stat-card:nth-child(4n+4)::before {
    background: linear-gradient(180deg, #3B82F6, #2563EB);
  }

  .stat-label {
    font-size: 0.875rem;
    color: #6B7280;
    font-weight: 500;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark-color);
    line-height: 1;
    margin-bottom: 0.25rem;
  }

  .stat-sublabel {
    font-size: 0.75rem;
    color: #9CA3AF;
  }

  .stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  /* Gradient border on left side */
  .stat-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 6px;
    height: 100%;
    border-radius: 16px 0 0 16px;
    background: linear-gradient(180deg, #6366F1, #8B5CF6);
  }

  /* Different gradient per card */
  .stat-card:nth-child(2)::before {
    background: linear-gradient(180deg, #10B981, #059669);
  }

  .stat-card:nth-child(3)::before {
    background: linear-gradient(180deg, #F59E0B, #D97706);
  }

  .stat-card:nth-child(4)::before {
    background: linear-gradient(180deg, #3B82F6, #2563EB);
  }

  /* Hover effect */
  .stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
  }

  /* Text styling */
  .stat-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    letter-spacing: 0.5px;
  }

  .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.25rem;
  }

  .stat-sublabel {
    font-size: 0.8rem;
    color: #9CA3AF;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
    margin-bottom: 2rem;
    justify-items: center;
    /* centers the cards */
  }

  .stat-card {
    width: 100%;
    max-width: 280px;
    /* prevents stretching too wide */
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
</style>
<div class="main-panel main-page-box">

  <div class="content-wrapper content-wrapper-box">

    <div class="page-title-main">
      <h5 class="mb-0 font-weight-bold">Payment Type Report</h5>
      <div class="page-rightbtns cust-page-rightbtns">
        <div>
          <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn"
            style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter
            <span></span></a>
        </div>
      </div>
    </div>
    <hr />
    <div class="row ">
      <div class="col-sm-12">
        <div id="search-filter-btn" style="display: none;">
          <div class="card search-card1 cust-card-box" id="search-div">
            <div class="card-body p-0 border-0 form-patient-list-box">
              <form id="search-form">
                <div class="row form-row-gap">
                  <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                  <div class="col-md-3">
                    <div class="form-group row">
                      <label for="agency_fk" class="col-sm-12 ">Agency</label>
                      <div class="col-sm-12">
                        <select name="agency_fk" id="agency_fk" class="form-control">
                          <option value="">All Agencies</option>
                          @foreach ($agencyList as $rwAgency)
                          <option value="{{ $rwAgency->id }}" {{ ($agency_fk == $rwAgency->id) ? 'selected' : '' }}>
                            {{ $rwAgency->agency_name }}
                          </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                  <?php } ?>
                  <div class="col-md-3">
                    <div class="form-group row">
                      <label for="payment_type" class="col-sm-12 ">Payment Types</label>
                      <div class="col-sm-12">
                        <select name="payment_type" id="payment_type" class="form-control">
                          <option value="">All Payment Types</option>

                          @foreach($paymentTypesList as $pt)
                          <option value="{{ $pt->id }}">
                            {{ $pt->name }}
                          </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group row">
                      <label for="appointment_date" class="col-sm-12 ">Appointment Date</label>
                      <div class="col-sm-12">
                        <input type="text" name="appointment_date" value="" class="datepickernn form-control" id="appointment_date" readonly>
                      </div>
                    </div>
                  </div>
                  @php
                      $searchStatus = ['processing','completed','Telehealth Completed','Form Completed']
                  @endphp
                  <div class="col-md-3">
                    <div class="form-group row">
                      <label for="payment_type" class="col-sm-12 ">Status</label>
                      <div class="col-sm-12">
                        <select name="payment_type_status" id="payment_type_status" class="form-control">
                          <option value="">Select Status</option>
                        @foreach($searchStatus as $sts)
                        <option value="{{ $sts}}">
                            {{ ucfirst($sts) }}
                          </option>
                        @endforeach
                          
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <div class="row form-row-gap mt-3">
                <div class="col-md-9">
                  <div class="appointment-btn-box" style="justify-content:left !important">
                    <input type="button" name="search" class="btn btn-primary btn-rounded btn-fw btn-sm" id="search-data" value="Search" onclick="loadPatientList(1)">

                    <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()">Clear</a>
                    <a href="javascript:void(0)" class="btn btn-info btn-rounded btn-fw btn-sm" onclick="exportCsv()">Export</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div id="payment-type-counts-container">
          @if(count($paymentTypeCounts) > 0)
          <div class="stats-grid" style="display: flex; flex-wrap: wrap; gap: 15px;">
            @foreach($paymentTypeCounts as $ptCount)
            <div class="stat-card" style="flex: 1 1 calc(25% - 15px); box-sizing: border-box;">
              <div class="stat-label">
                {{ $ptCount->payment_type_name ?: 'Not Specified' }}
              </div>
              <div class="stat-value">
                {{ number_format($ptCount->total) }}
              </div>
              <div class="stat-sublabel">Total Portals</div>
            </div>
            @endforeach
          </div>
          @endif
        </div>
      </div>
    </div>
    <div class="row ">
      <div class="col-sm-12">

      </div>
    </div>

    <div class="row">
      <div class="col-12 ">
        <div class="location-wise-data-loader shimmer_id hideClass">
          <div class="col-md-12 pl-0">
            <table id="" class="table table-bordered">
              <thead>
                <tr>
                  <th>NO</th>
                  <th>ID</th>
                  <th>Patient Code</th>
                  <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                    <th>Agency Name</th>
                  <?php } ?>
                  <th>Type</th>
                  <th>Full Name</th>
                  <th>Services</th>
                  <th>Mobile</th>
                  <th>Phone</th>
                  <th>Payment Type</th>
                  <th>DOB</th>
                  <th>Gender</th>
                  <th>Appointment Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody class="shimmer-loader">
                <tr>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                </tr>
              </tbody>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
        <span id="patient-list-container"></span>
      </div>
    </div>


  </div>
  <div class="row" id="blank_div" style='margin-top: 100px;'>
  </div>
</div>

@include('include/footer')

<script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script>
  // Load payment type counts via Ajax
  function loadPaymentTypeCounts() {
    var agency_fk = $('#agency_fk').val() || '';
    var payment_type = $('#payment_type').val() || '';
    var appointment_date = $('#appointment_date').val() || '';
    var payment_type_status = $('#payment_type_status').val() || '';
    $.ajax({
      url: '<?php echo URL::to("/") ?>/payment-type-report/ajax-counts',
      type: 'GET',
      data: {
        agency_fk: agency_fk,
        payment_type: payment_type,
        appointment_date: appointment_date,
        payment_type_status:payment_type_status
      },
      success: function(response) {
        if (response.success && response.counts) {
          updateCountsDisplay(response.counts);
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading counts:', error);
      }
    });
  }

  // Update counts display
  function updateCountsDisplay(counts) {
    var html = '';
    if (counts.length > 0) {
      html = '<div class="stats-grid" style="display: flex; flex-wrap: wrap; gap: 15px;">';
      counts.forEach(function(ptCount) {
        var paymentTypeName = ptCount.payment_type_name || 'Not Specified';
        var total = parseInt(ptCount.total).toLocaleString();
        html += '<div class="stat-card" style="flex: 1 1 calc(25% - 15px); box-sizing: border-box;">';
        html += '<div class="stat-label">' + paymentTypeName + '</div>';
        html += '<div class="stat-value">' + total + '</div>';
        html += '<div class="stat-sublabel">Total Portals</div>';
        html += '</div>';
      });
      html += '</div>';
    }
    $('#payment-type-counts-container').html(html);
  }

  // Load patient list via Ajax
  function loadPatientList(page = 1) {
    var agency_fk = $('#agency_fk').val() || '';
    var payment_type = $('#payment_type').val() || '';
    var appointment_date = $('#appointment_date').val() || '';
    var payment_type_status = $('#payment_type_status').val() || '';
    // Show loading indicator
    $('.shimmer_id').removeClass('d-none');

    // Load both counts and patient list
    loadPaymentTypeCounts();

    $.ajax({
      url: '<?php echo URL::to("/") ?>/payment-type-report/ajax-list',
      type: 'GET',
      data: {
        agency_fk: agency_fk,
        payment_type: payment_type,
        appointment_date: appointment_date,
        page: page,
        payment_type_status:payment_type_status
      },
      success: function(response) {
        $('.shimmer_id').addClass('d-none');
        $('#patient-list-container').html(response);
      },
      error: function(xhr, status, error) {
        $('#patient-list-container').html('<div class="alert alert-danger">Error loading data. Please try again.</div>');
        console.error(error);
      }
    });
  }

  // Handle pagination clicks
  $(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadPatientList(page);
  });

  // Export function
  function export_data() {
    var agency_fk = $('#agency_fk').val() || '';
    var payment_type = $('#payment_type').val() || '';
    var search_term = $('#search_term').val() || '';
    var payment_type_status =$('#payment_type_status').val() || '';
    var url = '<?php echo URL::to("/") ?>/payment-type-report/export?agency_fk=' + agency_fk +
      '&payment_type=' + payment_type +
      '&search_term=' + search_term +"&payment_type_status="+payment_type_status;

    $('#export_btn').attr("href", url);
  }

  // Auto-load on page load if filters are set
  $(document).ready(function() {
    loadPatientList(1);
    <?php if ($agency_fk || $payment_type || $search_term): ?>

    <?php endif; ?>
  });

  // Allow Enter key to trigger search
  $('#search_term').on('keypress', function(e) {
    if (e.which === 13) {
      e.preventDefault();
      loadPatientList();
    }
  });

  $("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
  });
  $(function() {
    let start = moment().subtract(0, 'days');
    let end = moment();
    $('#appointment_date').daterangepicker({
      startDate: start,
      endDate: end,
      autoUpdateInput: false,
      startOfWeek: 'sunday',
      ranges: {
        'Select Date': [start, end],
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
          'month').endOf('month')],
        'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
          .endOf('month')
        ],
        'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
          .endOf('isoWeek')
        ],
        'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
          'weeks').endOf('isoWeek')],
      }
    }, function(chosen_date, end_date) {

      $('#appointment_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
        'MM/DD/YYYY'));
    })

    $('#appointment_date').on('apply.daterangepicker', function(ev, picker) {
      // Detect "Select Date"
      if (picker.chosenLabel === 'Select Date') {
        $(this).val('');
      } else {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
      }
    });

  });

  function refresh() {
    $('#agency_fk').val('').trigger("change")
    $('#payment_type').val('').trigger("change")
    $('#appointment_date').val('');
    $('#payment_type_status').val("");
    loadPatientList(1);
  }

  function exportCsv() {
    $('.hideClass').removeClass('d-none');
    let agency_fk = $('#agency_fk').val();
    let payment_type = $('#payment_type').val();
    let appointment_date = $('#appointment_date').val();
    var payment_type_status =$('#payment_type_status').val() || '';
    $.ajax({
      url: "{{url('payment-type-report/export')}}",
      type: "get",
      data: {
        'agency_fk': agency_fk,
        'payment_type': payment_type,
        'appointment_date': appointment_date,
        'payment_type_status':payment_type_status
      },
      success: function(response) {
        $('.hideClass').addClass('d-none');
        let blob = new Blob([response]);
        if (response == "") {
          toastr.error('Please check there is no data to export.');
        } else {
          let link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);
          let form_name = "payment_report_" + "{{date('m-d-Y')}}";
          link.download = form_name + ".csv";
          link.click();
        }
      }
    });
  }
</script>