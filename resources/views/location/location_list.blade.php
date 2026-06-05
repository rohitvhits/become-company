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

     .table-width1 tr th:first-child {
         width: 3%;
     }
     .table-width1 tr th:nth-child(5) {
         width: 10%;
     }
     .table-width1 tr th:nth-child(6) {
         width: 10%;
     }
     .table-width1 tr th:nth-child(7) {
         width: 10%;
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

     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
     }

     .srch-icon {
         padding: 0 !important;
         width: 40px;
         height: 40px;
     }

     .modal-lg-plus {
         max-width: 600px;
     }

     #blockDateModal .ui-datepicker {
         width: 100% !important;
         font-size: 14px;
         box-shadow: none;
         border: 1px solid #e9ecef;
         border-radius: 6px;
         padding: 10px;
     }
     #blockDateModal .ui-datepicker table {
         width: 100%;
     }
     #blockDateModal .ui-datepicker td a,
     #blockDateModal .ui-datepicker td span {
         text-align: center;
         padding: 8px 4px;
     }
     #blockDateModal .ui-datepicker .ui-datepicker-header {
         background: #f8f9fa;
         border: none;
         border-radius: 4px;
         margin-bottom: 5px;
     }
     #blockDateModal .ui-state-highlighted {
         background-color: #dc3545 !important;
         color: #fff !important;
         border-radius: 50%;
         border: none !important;
     }
     .block-date-chips-wrapper {
         max-height: 220px;
         overflow-y: auto;
         border: 1px solid #e9ecef;
         border-radius: 6px;
         padding: 8px;
         background: #f8f9fa;
     }
     .block-date-chip {
         display: inline-flex;
         align-items: center;
         background: #dc3545;
         color: #fff;
         border-radius: 20px;
         padding: 3px 10px;
         margin: 3px 4px;
         font-size: 12px;
         font-weight: 500;
     }
     .block-date-chip .chip-remove {
         margin-left: 6px;
         cursor: pointer;
         font-size: 14px;
         font-weight: bold;
         opacity: 0.8;
     }
     .block-date-chip .chip-remove:hover {
         opacity: 1;
     }
     .block-date-count {
         display: inline-block;
         background: #dc3545;
         color: #fff;
         border-radius: 12px;
         padding: 1px 8px;
         font-size: 12px;
         font-weight: 600;
         margin-left: 6px;
         vertical-align: middle;
     }

 </style>
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Location List</h5>
             <div class="page-rightbtns">
                 <div>
                     @can('location-add')
                         <a href="<?php echo URL::to('/location/add'); ?>" class="btn btn-primary btn-rounded btn-fw btn-sm"><i
                                 class="mdi mdi-plus"></i>Add Location </a>
                     @endcan
                 </div>
             </div>
         </div>

         <div class="col-12 grid-margin-top">
             @if (Session::has('success'))
                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('success') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
             @endif
             @if (Session::has('error'))
                 <div class="alert alert-warning alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('error') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
             @endif
         </div>

         <div class="row">
             <div class="col-12">
                 <table id="order-listing1" class="table table-bordered table-width1">
                     <thead>
                         <tr>
                             <th>#</th>
                             <th>Address</th>
                             <th>Address2</th>
                             <th>Short Name</th>
                             <th>City</th>
                             <th>State</th>
                             <th>Zipcode</th>
                             <th>Action</th>
                         </tr>
                     </thead>
                     <tbody>

                         <?php if ($query->total() != 0) {
                        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                        foreach ($query as $row) {  ?>
                         <tr>
                             <th scope="row"><?= $i++ ?></th>
                             <td><?= $row->address1 ?></td>
                             <td><?= $row->address2 ?></td>
                             <td><?= $row->location_name ?></td>
                             <td><?= $row->city ?></td>
                             <td><?= $row->state ?></td>
                             <td><?= $row->zip_code ?></td>
                             <td>
                                 @can('location-edit')
                                     <a href="<?php echo URL::asset('/'); ?>location/edit/<?= $row->id ?>" data-toggle="tooltip"
                                         title="Sentence Edit"><i class="fa fa-edit"></i></a>
                                 @endcan

                                 @can('location-schedule')
                                     <a href="<?php echo URL::asset('/'); ?>location-schedule/<?= $row->id ?>" data-toggle="tooltip"
                                         title="Schedule"><i class="mdi mdi-calendar"></i></a>
                                 @endcan

                                 @can('location-delete')
                                     <a href="<?php echo URL::asset('/'); ?>location/delete/<?= $row->id ?>" data-toggle="tooltip"
                                         title="Sentence Delete"
                                         onclick="return confirm('Are you sure remove this record?')"><i
                                             class="mdi mdi-delete"></i></a>
                                 @endcan

                                 <a href="<?php echo URL::to('/'); ?>/location/locationLog/<?php echo $row->id; ?>"
                                     data-lid="{{ $row->id }}" id="logln<?php echo $row->id; ?>" title="Log List"><i
                                         class="fa fa-list"></i></a>

                                @can('location-block-schedule')
                                     <a href="javascript:void(0)" data-toggle="tooltip" title="Block Dates"
                                         onclick="openBlockDateModal('{{$row->id}}', '{{addslashes($row->location_name)}}')"><i class="mdi mdi-calendar-remove"></i></a>
                                 @endcan
                             </td>
                         </tr>
                         <?php }
                      } else { ?>
                         <tr>
                             <td colspan="12">
                                 <center><b>Data not found</b></center>
                             </td>
                         </tr>
                         <?php } ?>
                     </tbody>
                 </table>
                 <div class="pull-right pegination-margin">
                     {{ $query->links('pagination::bootstrap-4') }}
                 </div>
             </div>
         </div>
     </div>
     <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

    <!-- Block Date Modal -->
    <div class="modal fade" id="blockDateModal" tabindex="-1" role="dialog" aria-labelledby="blockDateModalLabel" style="display: none; z-index:1050 !important" aria-hidden="true">
        <div class="modal-dialog modal-lg-plus modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color:#000000 !important">
                    <h5 class="modal-title font-weight-bold" id="blockDateModalLabel">
                        <i class="mdi mdi-calendar-remove mr-2"></i>Block Dates — <span id="block_date_modal_loc_name"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="block_date_location_id">

                    <!-- Status Toggle -->
                    <div class="form-group mb-3">
                        <label for="status" class="font-weight-semibold d-block">Status</label>
                        <div class="d-flex align-items-center mt-1">
                            <label class="toggle-switch toggle-switch-success">
                                <input type="checkbox" name="block_date_status" id="block_date_status" class="statusActiveDeactive" checked>
                                <span class="toggle-slider round"></span>
                            </label>
                        </div>
                        <small class="text-muted mt-1 d-block" id="statusHelpText"><strong>If the Status is disabled, the changes related to the block days (mentioned below) will no longer be applied to the Location.</strong></small>
                    </div>

                    <hr class="mt-2 mb-3">

                    <!-- Inline Calendar -->
                    <div class="form-group mb-2">
                        <label for="block-dates-calendar" class="font-weight-semibold">
                            <i class="mdi mdi-calendar-multiple mr-1"></i>Click dates to select / deselect <span class="text-danger">*</span>
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <div id="block_dates_calendar"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="block-date-chips" class="font-weight-semibold">
                                    Selected Dates <span class="block-date-count d-none" id="block_date_count_badge">0</span>
                                </label>
                                <div class="block-date-chips-wrapper" id="block_date_chips">
                                    <span class="text-muted" id="block_date_no_selection">No dates selected. Click on the calendar above.</span>
                                </div>
                            </div>
                        </div>
                        <span class="error mt-1 text-danger d-block" id="block_date_error"></span>
                    </div>

                    <!-- Selected Dates Chips -->
                    <div class="form-group mb-0">
                        
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <div class="d-flex justify-content-end align-items-center w-100">
                        <button type="button" class="btn btn-success btn-sm px-4 mr-2" onclick="saveBlockDates()" id="btnSaveBlockDates">
                            <span class="spinner-border spinner-border-sm d-none" id="loaderBlockDate" role="status" aria-hidden="true"></span>
                            <span id="btn-save-block-date-text">Save</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

     @include('include/footer')

<script>
var blockSelectedDates = [];

function openBlockDateModal(locationId, locationName) {
    blockSelectedDates = [];
    $('#block_date_location_id').val(locationId);
    $('#block_date_modal_loc_name').text(locationName);
    $('#block_date_error').html('');
    $('#block_date_status').prop('checked', true);

    // Load existing dates
    $.ajax({
        url: '{{ url("/location/get-block-dates") }}',
        type: 'GET',
        data: { location_id: locationId },
        success: function(response) {
            if (response.status && response.dates.length > 0) {
                blockSelectedDates = response.dates;
                if(response.disable_status == 1){
                    $('#block_date_status').prop('checked', false);
                }
            }else{
                $('#block_date_status').prop('checked', 1);
            }
            initBlockDatepicker();
            renderDateChips();
            $('#blockDateModal').modal('show');
            $('#blockDateModal').css({ zIndex: '99999' });
        },
        error: function() {
            initBlockDatepicker();
            renderDateChips();
            $('#blockDateModal').modal('show');
            $('#blockDateModal').css({ zIndex: '99999' });
        }
    });
}

$('#blockDateModal').on('shown.bs.modal', function () {
    initBlockDatepicker();
});

function initBlockDatepickerold() {

    // Properly destroy previous instance
    if ($('#block_dates_calendar').hasClass('hasDatepicker')) {
        $('#block_dates_calendar').datepicker('destroy');
    }

    $('#block_dates_calendar').datepicker({
        dateFormat: 'mm/dd/yy',

        onSelect: function(dateText) {
            var index = blockSelectedDates.indexOf(dateText);

            if (index > -1) {
                blockSelectedDates.splice(index, 1);
            } else {
                blockSelectedDates.push(dateText);
            }

            renderDateChips();

            // Refresh to reapply highlight
            $('#block_dates_calendar').datepicker('refresh');
        },

        beforeShowDay: function(date) {
            var formattedDate = $.datepicker.formatDate('mm/dd/yy', date);

            if (blockSelectedDates.indexOf(formattedDate) > -1) {
                return [true, 'ui-state-highlighted'];
            }

            return [true, ''];
        }
    });
}

function initBlockDatepicker() {

    let isEnabled = $('#block_date_status').is(':checked'); // check toggle state

    // Destroy previous instance
    if ($('#block_dates_calendar').hasClass('hasDatepicker')) {
        $('#block_dates_calendar').datepicker('destroy');
    }

    $('#block_dates_calendar').datepicker({
        dateFormat: 'mm/dd/yy',

        // ❌ Prevent selecting when disabled
        onSelect: function(dateText) {

            if (!$('#block_date_status').is(':checked')) {
                return false; // stop selection
            }

            var index = blockSelectedDates.indexOf(dateText);

            if (index > -1) {
                blockSelectedDates.splice(index, 1);
            } else {
                blockSelectedDates.push(dateText);
            }

            renderDateChips();
            $('#block_dates_calendar').datepicker('refresh');
        },

        // ❌ Disable all dates visually when status OFF
        beforeShowDay: function(date) {

            if (!$('#block_date_status').is(':checked')) {
                return [false, 'calendar-disabled']; // make unclickable
            }

            var formattedDate = $.datepicker.formatDate('mm/dd/yy', date);

            if (blockSelectedDates.indexOf(formattedDate) > -1) {
                return [true, 'ui-state-highlighted'];
            }

            return [true, ''];
        }
    });

    // Add overall disabled feel
    $('#block_dates_calendar').toggleClass('calendar-wrapper-disabled', !isEnabled);
}

$('#block_date_status').on('change', function () {
    initBlockDatepicker(); // reload calendar with new state
    renderDateChips();
});

function renderDateChips() {
    var container = $('#block_date_chips');
    container.empty();

    var isEnabled = $('#block_date_status').is(':checked'); // check status

    if (blockSelectedDates.length === 0) {
        container.html('<span class="text-muted" id="block_date_no_selection">No dates selected. Click on the calendar above.</span>');
        $('#block_date_count_badge').addClass('d-none').text('0');
        return;
    }

    // Sort dates chronologically
    var sorted = blockSelectedDates.slice().sort(function(a, b) {
        return new Date(a) - new Date(b);
    });

    for (var i = 0; i < sorted.length; i++) {

        var chip = '<span class="block-date-chip">' + sorted[i];

        // Only allow remove when status is ON
        if (isEnabled) {
            chip += '<span class="chip-remove" onclick="removeBlockDate(\'' + sorted[i] + '\')">&times;</span>';
        }

        chip += '</span>';

        container.append(chip);
    }

    $('#block_date_count_badge').removeClass('d-none').text(blockSelectedDates.length);

    // Optional: add disabled look
    $('#block_date_chips').toggleClass('disabled-chips', !isEnabled);
}


function removeBlockDate(date) {

    if (!$('#block_date_status').is(':checked')) {
        return false; // prevent removing when disabled
    }

    blockSelectedDates = blockSelectedDates.filter(d => d !== date);
    renderDateChips();
    $('#block_dates_calendar').datepicker('refresh');
}


function saveBlockDates() {
    var locationId = $('#block_date_location_id').val();
    var status = $('#block_date_status').is(':checked') ? 0 : 1;

    $('#block_date_error').html('');

    if(status == 1){
        if (blockSelectedDates.length === 0) {
        $('#block_date_error').html('Please select at least one date');
            return false;
        }
    }

    var dates = blockSelectedDates.join(', ');

    $('#loaderBlockDate').removeClass('d-none');
    $('#btn-save-block-date-text').text('Saving...');
    $('#btnSaveBlockDates').prop('disabled', true);

    $.ajax({
        url: '{{ url("/location/save-block-dates") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            location_id: locationId,
            dates: dates,
            status: status
        },
        success: function(response) {
            toastr.success(response.error_msg);
            $('#blockDateModal').modal('hide');
            $('#loaderBlockDate').addClass('d-none');
            $('#btn-save-block-date-text').text('Save');
            $('#btnSaveBlockDates').prop('disabled', false);
            location.reload();
        },
        error: function(jqXHR) {
            toastr.error(jqXHR.responseJSON.error_msg);
            $('#loaderBlockDate').addClass('d-none');
            $('#btn-save-block-date-text').text('Save');
            $('#btnSaveBlockDates').prop('disabled', false);
        }
    });
}
</script>
