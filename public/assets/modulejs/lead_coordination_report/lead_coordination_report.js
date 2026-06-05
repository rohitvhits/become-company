/**
 * Load AJAX list with filters and pagination
 */
function loadAjaxList(page = 1) {
    var full_name = $('#full_name').val();
    var phone = $('#phone').val();
    var agency_name = $('#agency_name').val();
    var service_requested = $('#service_requested').val();
    var appointment_date_from = $('#appointment_date_from').val();
    var appointment_date_to = $('#appointment_date_to').val();
    var created_date_from = $('#created_date_from').val();
    var created_date_to = $('#created_date_to').val();

    $('.shimmer_id').removeClass('hide');
    $('#response_requested_id').html("");
    $('.location-wise-data-loader').attr('style', 'display:flex');

    $.ajax({
        url: _LOAD_DATA_URL,
        data: {
            'page': page,
            'full_name': full_name,
            'phone': phone,
            'agency_name': agency_name,
            'service_requested': service_requested,
            'appointment_date_from': appointment_date_from,
            'appointment_date_to': appointment_date_to,
            'created_date_from': created_date_from,
            'created_date_to': created_date_to
        },
        type: "GET",
        success: function (res) {
            $('.shimmer_id').addClass('hide');
            $('#response_requested_id').html(res);
            $('.location-wise-data-loader').attr('style', 'display:none');
        },
        error: function (xhr) {
            $('.shimmer_id').addClass('hide');
            $('.location-wise-data-loader').attr('style', 'display:none');
            showErrorAndLoginRedirection(xhr);
        }
    });
}

/**
 * Refresh/Reset filter form
 */
function refresh() {
    $('#search-form')[0].reset();
    $('#appointment_date_range').val('');
    $('#appointment_date_from').val('');
    $('#appointment_date_to').val('');
    $('#created_date_range').val('');
    $('#created_date_from').val('');
    $('#created_date_to').val('');
    loadAjaxList(1);
}

/**
 * Export CSV with applied filters
 */
function exportCSV() {
    var full_name = $('#full_name').val();
    var phone = $('#phone').val();
    var agency_name = $('#agency_name').val();
    var service_requested = $('#service_requested').val();
    var appointment_date_from = $('#appointment_date_from').val();
    var appointment_date_to = $('#appointment_date_to').val();
    var created_date_from = $('#created_date_from').val();
    var created_date_to = $('#created_date_to').val();

    var params = new URLSearchParams({
        full_name: full_name || '',
        phone: phone || '',
        agency_name: agency_name || '',
        service_requested: service_requested || '',
        appointment_date_from: appointment_date_from || '',
        appointment_date_to: appointment_date_to || '',
        created_date_from: created_date_from || '',
        created_date_to: created_date_to || ''
    });

    window.location.href = _EXPORT_CSV + '?' + params.toString();
}

$(document).ready(function () {
    // Load data on page load
    loadAjaxList(1);
    var start = moment().subtract(0, 'days');
    var end = moment();
    // Filter button toggle
    $('#filter-btn').on('click', function () {
        $('#search-filter-btn').slideToggle();
    });

    // Appointment Date Range Picker
    $('#appointment_date_range').daterangepicker({

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
    });

    $('#appointment_date_range').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        $('#appointment_date_from').val(picker.startDate.format('MM/DD/YYYY'));
        $('#appointment_date_to').val(picker.endDate.format('MM/DD/YYYY'));
    });

    $('#appointment_date_range').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#appointment_date_from').val('');
        $('#appointment_date_to').val('');
    });

    // Created Date Range Picker
    $('#created_date_range').daterangepicker({
        
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
    });

    $('#created_date_range').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        $('#created_date_from').val(picker.startDate.format('MM/DD/YYYY'));
        $('#created_date_to').val(picker.endDate.format('MM/DD/YYYY'));
    });

    $('#created_date_range').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#created_date_from').val('');
        $('#created_date_to').val('');
    });

    // Pagination click handler
    $('body').on('click', '.pagination a', function (event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        loadAjaxList(page);
    });
});
