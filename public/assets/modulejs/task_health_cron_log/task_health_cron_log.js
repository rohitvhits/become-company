$(function () {
    loadTaskHealthCronLogList(1);

    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepickernn').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
        }
    }, function (chosen_date, end_date) {
        $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
    });
});

function loadTaskHealthCronLogList(page) {
    $('#resp').html('');
    $('#loadertag').attr('style', 'display:block');

    $.ajax({
        url: _TASK_HEALTH_CRON_LOG_LIST + '?page=' + page,
        type: 'get',
        data: {
            task_id:      $('#task_id').val(),
            patient_id:   $('#patient_id').val(),
            patient_name: $('#patient_name').val(),
            agency_id:    $('#agency_id').val(),
            type:         $('#type').val(),
            cron_name:    $('#cron_name').val(),
            created_date: $('#created_date').val(),
        },
        success: function (response) {
            $('#resp').html(response);
            $('#loadertag').attr('style', 'display:none');
        }
    });
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadTaskHealthCronLogList(page);
});

function showCronLogData(id) {
    var message = $('#cron-log-' + id).html();
    $('.dataContainer').html('');

    if (!message) return;

    try {
        var data = JSON.parse(message);
    } catch (e) {
        $('.dataContainer').html('<pre>' + message + '</pre>');
        $('#cronLogModal').modal('show');
        return;
    }

    var content = '<pre>{\n';

    $.each(data, function (key, value) {
        var display = '-';

        if (value !== undefined && value !== null && value !== '') {
            if (typeof value === 'object') {
                display = JSON.stringify(value, null, 2);
            } else if (typeof value === 'string') {
                try {
                    var parsed = JSON.parse(value);
                    display = JSON.stringify(parsed, null, 2);
                } catch (e) {
                    try {
                        var cleaned = value.replace(/\\"/g, '"');
                        var parsed2 = JSON.parse(cleaned);
                        display = JSON.stringify(parsed2, null, 2);
                    } catch (err) {
                        display = value;
                    }
                }
            } else {
                display = value;
            }
        }

        content += '"' + capitalizeFirstLetter(key.replace(/_/g, ' ')) + '": ' + display + ',\n';
    });

    content += '}\n</pre>';
    $('.dataContainer').html(content);
    $('#cronLogModal').modal('show');
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
