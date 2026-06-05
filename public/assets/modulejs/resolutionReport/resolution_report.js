loadresolution(1);

function loadresolution(page){
    $('#ajax_response_id').html("")
    $('.hideClass').removeClass('d-none');
    $.ajax({
        type:"GET",
        url:_RESOLUTION_LOG_AJAX,
        data:{
            'created_date':$('#created_date').val(),
            'team':$('#team').val(),
            'resolution':$('#resolution').val(),
            'cancel_reason':$('#cancel_reason').val(),
            'refuse_reason':$('#refuse_reason').val(),
            'agency_fk':$('#agency_fk').val(),
            'agency_filter_type':$('#agency_filter_type').val(),
            'assigned_to':$('#assigned_to').val(),
            'page':page
        },
        success:function(res){
            $('.hideClass').addClass('d-none');
            $('#ajax_response_id').html(res);
        }
    })
}

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

$(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#created_date').daterangepicker({
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
        $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('#created_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
});

function exportCsv(page){

    $.ajax({
        type:"GET",
        url:_RESOLUTION_LOG_EXPORT,
        data:{
            'created_date':$('#created_date').val(),
            'team':$('#team').val(),
            'resolution':$('#resolution').val(),
            'cancel_reason':$('#cancel_reason').val(),
            'refuse_reason':$('#refuse_reason').val(),
            'agency_fk':$('#agency_fk').val(),
            'agency_filter_type':$('#agency_filter_type').val(),
            'assigned_to':$('#assigned_to').val(),

        },
        success:function(response){
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            var form_name = "resolution_log_"+_DATE_TIME;
            link.download = form_name + ".csv";
            link.click();
        }
    })
}

function refresh(){
    $('#created_date').val('');
    $('#team').val('').change();
    $('#resolution').val('').change();
    $('#cancel_reason').val('');
    $('#refuse_reason').val('');
    $('#agency_fk').val(null).trigger('change');
    $('#assigned_to').val(null).trigger('change');
    loadresolution(1);
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadresolution(page);
});

// Agency Filter Toggle Button
$(document).ready(function() {
    const $toggleBtn = $('#agencyToggleBtn');
    const $toggleLabel = $('#agencyToggleLabel');
    const $filterTypeInput = $('#agency_filter_type');

    if ($toggleBtn.length === 0) {
        console.warn('Agency toggle button not found!');
        return;
    }

    // Update button appearance, label, and hidden input
    function updateButton(mode) {
        $toggleBtn.attr('data-mode', mode);
        $filterTypeInput.val(mode);

        if (mode === 'include') {
            $toggleBtn.html('<i class="mdi mdi-plus"></i>');
            $toggleBtn.attr('title', 'Include - Click to switch to Exclude');
            $toggleLabel.text('Include Agency').removeClass('mode-exclude').addClass('mode-include');
        } else {
            $toggleBtn.html('<i class="mdi mdi-minus"></i>');
            $toggleBtn.attr('title', 'Exclude - Click to switch to Include');
            $toggleLabel.text('Exclude Agency').removeClass('mode-include').addClass('mode-exclude');
        }
    }

    // Toggle on click with event delegation to handle dynamic content
    $(document).on('click', '#agencyToggleBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const currentMode = $(this).attr('data-mode');
        const newMode = currentMode === 'include' ? 'exclude' : 'include';
        updateButton(newMode);
    });
});