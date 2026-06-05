function agencyAjax(page) {
    $('.shimmer_id').removeClass('hide');
    $('#response_agency_list').html('');
    $('.agency-data-loader').attr('style', 'display:flex');

    $.ajax({
        url: _AGENCY_AJAX + '?page=' + page,
        type: 'get',
        data: {
            'agency_name': $('#agency_name').val(),
            'email': $('#email').val(),
            'phone': $('#phone').val(),
            'city': $('#city').val(),
            'is_sms': $('#is_sms').val(),
        },
        success: function(res) {
            $('.shimmer_id').addClass('hide');
            $('#response_agency_list').html(res);
            $('.agency-data-loader').attr('style', 'display:none');
        },
        error: function(xhr, status, error) {
            $('.shimmer_id').addClass('hide');
            $('.agency-data-loader').attr('style', 'display:none');
            toastr.error('An error occurred. Please try again.');
        }
    });

    return false;
}

function agencyReset() {
    $('#agency_name').val('');
    $('#email').val('');
    $('#phone').val('');
    $('#city').val('');
    $('#is_sms').val('');
    agencyAjax(1);
}

function agencyExportData() {
    var agency_name = $('#agency_name').val();
    var email = $('#email').val();
    var phone = $('#phone').val();
    var city = $('#city').val();
    var is_sms = $('#is_sms').val();
    var url = _AGENCY_EXPORT + '?agency_name=' + agency_name + '&email=' + email + '&phone=' + phone + '&city=' + city + '&is_sms=' + is_sms;
    $('#btn_export_agency').attr('href', url);
}

$(document).ready(function() {
    agencyAjax(1);

    $('#filter-btn').on('click', function() {
        $('#search-filter-btn').toggle();
    });

    $('#search-data').on('click', function() {
        var agency_name = $('#agency_name').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var city = $('#city').val();
        var is_sms = $('#is_sms').val();
        $('#error_all').html('');

        if (agency_name == '' && email == '' && phone == '' && city == '' && is_sms == '') {
            $('#error_all').html('Please enter any one search text');
            return false;
        }
        agencyAjax(1);
    });

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        agencyAjax(page);
    });
});
