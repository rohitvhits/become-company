 @include('include/header')
 @include('include/sidebar')
 <style type="text/css">
     #order-listing_length,
     #order-listing_paginate,
     #order-listing_info {
         display: none;
     }

     #order-listing_filter {
         text-align: right;
     }

     .select2-design+.select2.select2-container.select2-container--default {
         width: 100% !important;
     }

     td {
         table-layout: fixed;
         width: 20px;
         overflow: hidden;
         word-wrap: break-word;
     }

     .table-width1 {
         background-color: #fff;
     }

     .search-inner {
         display: flex;
         justify-content: space-between;
         padding-top: 10px;
         padding-right: 20px;
         padding-left: 20px;
     }

     .search-main1 {
         border-top: 1px solid #eeeeee;
         margin-left: -20px;
         margin-right: -20px;
     }

     .search-btn1,
     .search-btn1:hover,
     .search-btn1:active,
     .search-btn1:focus {
         background: #007bff !important;
         border: #007bff !important;
         border-radius: 20px;
         height: 36px;
     }

     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
     }

     .search-card1 {
         margin-bottom: 20px;
     }

     .search-card1 .form-group {
         margin-bottom: 0.5rem;
     }

     .search-card1 label {
         margin-bottom: 0;
     }

     .search-card1 .card-body {
         padding-bottom: 10px;
     }

     .search-card1 input[type=text] {
         border-radius: 4px;
         border-color: #aaa;
     }

     .srch-icon {
         padding: 0 !important;
         width: 40px;
         height: 40px;
     }

     .custom-switch-sm {
         padding-left: 2rem;
     }
     .custom-switch-sm .custom-control-input ~ .custom-control-label::before {
         width: 1.5rem;
         height: 0.85rem;
         border-radius: 0.5rem;
         left: -2rem;
         top: 0.2rem;
     }
     .custom-switch-sm .custom-control-input ~ .custom-control-label::after {
         width: calc(0.85rem - 4px);
         height: calc(0.85rem - 4px);
         border-radius: 50%;
         left: calc(-2rem + 2px);
         top: calc(0.2rem + 2px);
     }
     .custom-switch-sm .custom-control-input:checked ~ .custom-control-label::after {
         transform: translateX(0.65rem);
     }
 </style>
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Doctor List</h5>
                <div class="page-rightbtns">
                    <div>
                        @can('doctor-add')
                        <a href="<?php echo URL::to('/doctor/add'); ?>" class="btn btn-primary btn-rounded btn-fw btn-sm"><i
                                class="mdi mdi-plus"> </i> Add Doctor </a>
                        @endcan

                        <a href="<?php echo URL::to('/'); ?>/doctor" class="btn btn-light btn-rounded btn-fw btn-sm ml-1"><i
                             class="mdi mdi-reload"></i> Reset</a>

                        @can('doctor-export')
                        <a href="" class="btn btn-success btn-rounded btn-sm btn-fw ml-1" id="test_agency"
                            onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
                        @endcan
                        <button class="btn btn-dark btn-rounded btn-fw btn-sm ml-1 srch-icon" id="searchbtns"><i
                             class="fa fa-search"></i></button>
                    </div>
             </div>
        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="card search-card1" id="search-div" style="display: none;">
                    <div class="card-body">
                        <form method="get" id="formsubmit">
                            <input type="hidden" name="_token" value="T2fdzK1ShOFrIaDGtfR43XwT91A6Ahjq88isXJeQ">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Doctor Name</label>
                                        <div class="col-sm-12 ">
                                            <input autocomplete="off" type="text" class="form-control"
                                                 name="full_name" id="full_name" value="{{ $full_name }}">
                                        </div>
                                        <span class="error ml-2" id="error_all"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Email</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" class="form-control"
                                                name="email" id="email" value="{{ $email }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Phone</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" class="form-control"
                                                name="phone" id="phone" value="{{ $phone }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">License</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" class="form-control"
                                                name="license" id="license" value="{{ $license }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">State</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" class="form-control"
                                                name="state" id="state" value="{{ $state }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">City</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" class="form-control"
                                                name="city" id="city" value="{{ $city }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Zipcode</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" class="form-control"
                                                name="zipcode" id="zipcode" value="{{ $zipcode }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Place Of Examination</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" class="form-control"
                                                name="place_of_examination" id="place_of_examination"
                                                value="{{ $place_of_examination }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12 ">Date Of Examination</label>
                                        <div class="col-sm-12">
                                            <input type="text" autocomplete="off" class="form-control"
                                                name="date_of_examination" id="date_of_examination"
                                                value="{{ $date_of_examination }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Doctor Status</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" name="is_active" id="is_active">
                                                <option value="">-- All --</option>
                                                <option value="1" {{ $is_active === '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $is_active === '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-12">Signature & Stamp Status</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" name="is_signature_stamp_active" id="is_signature_stamp_active">
                                                 <option value="">-- All --</option>
                                                <option value="1" {{ $is_signature_stamp_active === '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $is_signature_stamp_active === '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="search-main1">
                                <div class="search-inner">
                                    <div>
                                        <input type="button" name="search"
                                            class="btn btn-primary search-btn1 searchAppoinment" id="search-data"
                                            value="Search">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table id="order-listing1" class="table table-bordered table-width1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>

                            <th>License</th>
                            <th>Full Address</th>
                            <th>Place Of Examination</th>
                            <th>Date Of Examination</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($query->total() != 0) {
                            $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                            foreach ($query as $row) {  ?>
                                <tr>
                                    <td><?= '#' . ' ' . $row->id ?></td>
                                    <td><?php echo $row->full_name; ?></td>
                                    <td><?php echo $row->email; ?></td>
                                    <td><?php echo $row->phone; ?></td>
                                    <td><?php echo $row->gender; ?></td>

                                    <td><?php echo $row->license; ?></td>
                                    <td><?php echo $row->address . ',' . $row->state . ',' . $row->city . ',' . $row->zipcode; ?></td>
                                    <td><?php echo $row->place_of_examination; ?></td>
                                    <td><?php echo $row->date_of_examination; ?></td>
                                    <td style="min-width:110px;">
                                        <div class="d-flex flex-column" style="gap:6px;">
                                            <div class="d-flex align-items-center" style="gap:10px;">
                                                <div class="custom-control custom-switch custom-switch-sm">
                                                    <input type="checkbox" class="custom-control-input doctor-status-toggle"
                                                        id="doc_status_{{ $row->id }}"
                                                        data-id="{{ $row->id }}"
                                                        data-name="{{ $row->full_name }}"
                                                        {{ $row->is_active == 1 ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="doc_status_{{ $row->id }}"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="Doctor Status: {{ $row->is_active == 1 ? 'Active' : 'Inactive' }}"></label>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-sm">
                                                    <input type="checkbox" class="custom-control-input sig-stamp-toggle"
                                                        id="sig_stamp_{{ $row->id }}"
                                                        data-id="{{ $row->id }}"
                                                        data-name="{{ $row->full_name }}"
                                                        {{ $row->is_signature_stamp_active == 1 ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="sig_stamp_{{ $row->id }}"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="Signature &amp; Stamp: {{ $row->is_signature_stamp_active == 1 ? 'Active' : 'Inactive' }}"></label>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center" style="gap:8px;">
                                                @can('doctor-edit')
                                                <a href="<?php echo URL::to('/'); ?>/doctor/edit/<?php echo $row->id; ?>" title="Edit"><i class="fa fa-edit"></i></a>
                                                @endcan
                                                @can('doctor-delete')
                                                <a href="javascript:void(0);" onclick="deleteRecordDoctor('{{ $row->id }}')" title="Delete"><i class="fa fa-trash"></i></a>
                                                @endcan
                                                <a href="<?php echo URL::to('/'); ?>/doctor/log/<?php echo $row->id; ?>" data-lid="{{ $row->id }}" id="logln<?php echo $row->id; ?>" title="Log List"><i class="fa fa-list"></i></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="10">
                                    <center><b>Data not found</b></center>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="pull-right pegination-margin">
                    {{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
     
</div>
 <!-- Rate Start -->

 <!-- Rate End -->
 <script>
     function deleteRecordDoctor(id) {
         var url = "{{ url('doctor/delete') }}";
         $.confirm({
             title: 'Delete',
             columnClass: "col-md-6",
             content: 'Are you sure delete record?',
             buttons: {
                 formSubmit: {
                     text: 'Delete',
                     btnClass: 'btn-danger',
                     action: function() {
                         window.location.href = url + '/' + id;
                     }
                 },
                 cancel: function() {
                     //close
                 },
             },
         });
     }
     /* ..Start.. For page refresh when search data then show search area */
     $(document).ready(function() {
         var params = new URLSearchParams(window.location.search);
         var searchKeys = ['full_name','email','phone','license','state','city','zipcode','place_of_examination','date_of_examination','is_active','is_signature_stamp_active'];
         var hasFilter = searchKeys.some(function(k){ return params.has(k) && params.get(k) !== ''; });
         if (hasFilter) {
             $("#search-div").show();
         }
     });
     /* ..End.. For page refresh when search data then show search area */
     $("#searchbtns").click(function() {
         $("#search-div").toggle();
     });

     $(document).on("click", ".searchAppoinment", function() {

         var full_name = $('#full_name').val();
         var email = $('#email').val();
         var phone = $('#phone').val();
         var license = $('#license').val();
         var state = $('#state').val();
         var city = $('#city').val();
         var zipcode = $('#zipcode').val();
         var place_of_examination = $('#place_of_examination').val();
         var date_of_examination = $('#date_of_examination').val();
         var is_active = $('#is_active').val();
         var is_signature_stamp_active = $('#is_signature_stamp_active').val();
         $("#error_all").html('');

         if (full_name == '' && email == '' && phone == '' && license == '' && state == '' && city == '' &&
             zipcode == '' && place_of_examination == '' && date_of_examination == '' &&
             is_active == '' && is_signature_stamp_active == '') {
             $("#error_all").html('Please enter any one search text');
             return false;
         } else {
             var links = "{{ url('/doctor') }}?full_name=" + encodeURIComponent(full_name) +
                 "&email=" + encodeURIComponent(email) +
                 "&phone=" + encodeURIComponent(phone) +
                 "&license=" + encodeURIComponent(license) +
                 "&state=" + encodeURIComponent(state) +
                 "&city=" + encodeURIComponent(city) +
                 "&zipcode=" + encodeURIComponent(zipcode) +
                 "&place_of_examination=" + encodeURIComponent(place_of_examination) +
                 "&date_of_examination=" + encodeURIComponent(date_of_examination) +
                 "&is_active=" + encodeURIComponent(is_active) +
                 "&is_signature_stamp_active=" + encodeURIComponent(is_signature_stamp_active);
             window.location.href = links;
         }
     });
 </script>
 <script>
     function export_data() {

         var agency_name = $('#full_name').val();
         var email = $('#email').val();
         var phone = $('#phone').val();
         var license = $('#license').val();
         var state = $('#state').val();
         var city = $('#city').val();
         var zipcode = $('#zipcode').val();
         var place_of_examination = $('#place_of_examination').val();
         var date_of_examination = $('#date_of_examination').val();
         var is_active = $('#is_active').val();
         var is_signature_stamp_active = $('#is_signature_stamp_active').val();

         var temp1 = '{{ url('/doctor/doctor-export') }}?full_name=' + encodeURIComponent(agency_name) +
             '&email=' + encodeURIComponent(email) +
             '&phone=' + encodeURIComponent(phone) +
             '&license=' + encodeURIComponent(license) +
             '&state=' + encodeURIComponent(state) +
             '&city=' + encodeURIComponent(city) +
             '&zipcode=' + encodeURIComponent(zipcode) +
             '&place_of_examination=' + encodeURIComponent(place_of_examination) +
             '&date_of_examination=' + encodeURIComponent(date_of_examination) +
             '&is_active=' + encodeURIComponent(is_active) +
             '&is_signature_stamp_active=' + encodeURIComponent(is_signature_stamp_active);
         $('#test_agency').attr("style", '');
         $('#test_agency').attr("href", temp1);
     }
 </script>
 <!-- Date Picker -->
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
 <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
 
 <script>
     $("#date_of_examination").datepicker();
 </script>

 <script>
    
    $('[data-toggle="tooltip"]').tooltip();

    var _DOCTOR_TOGGLE_URL = "{{ url('doctor/toggle-status') }}";
    var _SIG_STAMP_TOGGLE_URL = "{{ url('doctor/toggle-signature-stamp-status') }}";
    var _CSRF = "{{ csrf_token() }}";

    function doctorToggleAjax(url, id, checkbox, label) {
        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: _CSRF, id: id },
            beforeSend: function() { checkbox.prop('disabled', true); },
            success: function(response) {
                if (response.success) {
                    var s = response.new_status;
                    checkbox.prop('checked', s == 1);
                    toastr.success(response.error_message);
                    try {
                        var statusText = s == 1 ? 'Active' : 'Inactive';
                        var lbl = checkbox.next('label[data-toggle="tooltip"]');
                        var oldTitle = lbl.attr('title') || '';
                        var prefix = oldTitle.split(':')[0];
                        lbl.attr('title', prefix + ': ' + statusText).tooltip('dispose').tooltip();
                    } catch(e) {}
                } else {
                    checkbox.prop('checked', !checkbox.is(':checked'));
                    toastr.error(response.error_message || 'Failed to update status');
                }
            },
            error: function(xhr) {
                checkbox.prop('checked', !checkbox.is(':checked'));
                showErrorAndLoginRedirection(xhr);
            },
            complete: function() {
                checkbox.prop('disabled', false);
            }
        });
    }

    $(document).on('change', '.doctor-status-toggle', function() {
        var checkbox = $(this);
        var id = checkbox.data('id');
        var name = checkbox.data('name');
        var currentChecked = checkbox.is(':checked');
        var label = checkbox.next('label');
        var actionText = currentChecked ? 'activate' : 'deactivate';

        $.confirm({
            title: 'Confirm Status Change',
            content: 'Are you sure you want to ' + actionText + ' Doctor <strong>"' + name + '"</strong>?',
            type: 'orange',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function() {
                        doctorToggleAjax(_DOCTOR_TOGGLE_URL, id, checkbox, label);
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-secondary',
                    action: function() {
                        checkbox.prop('checked', !currentChecked);
                    }
                }
            }
        });
    });

    $(document).on('change', '.sig-stamp-toggle', function() {
        var checkbox = $(this);
        var id = checkbox.data('id');
        var name = checkbox.data('name');
        var currentChecked = checkbox.is(':checked');
        var label = checkbox.next('label');
        var actionText = currentChecked ? 'activate' : 'deactivate';

        $.confirm({
            title: 'Confirm Status Change',
            content: 'Are you sure you want to ' + actionText + ' Signature & Stamp for <strong>"' + name + '"</strong>?',
            type: 'orange',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function() {
                        doctorToggleAjax(_SIG_STAMP_TOGGLE_URL, id, checkbox, label);
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-secondary',
                    action: function() {
                        checkbox.prop('checked', !currentChecked);
                    }
                }
            }
        });
    });
 </script>
 @include('include/footer')