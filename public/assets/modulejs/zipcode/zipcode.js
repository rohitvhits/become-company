$(document).ready(function() {
    loadZipCode(1)
});

function loadZipCode(page){
    $('#zipcode-wise-data-loader').attr('style','display:flex');
    $('#zipcode-list').html('');
    var zipcode = $('#zipcode').val();
    var county = $('#county').val();
    $.ajax({
        url: ZIPCODE_AJAX,
        type: 'GET',
        data: {
            'page': page,
            'zipcode' : zipcode,
            'county' : county,
        },
        success: function(response) {
            $('#zipcode-list').html(response);
            $('#zipcode-wise-data-loader').attr('style','display:none');
        }
    });
}

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

$('body').on('click', '.pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadZipCode(page);
});

function refresh(){
    $('#search-form')[0].reset();
    loadZipCode(1);
}

// Confirm Delete
function statusUpdate(id,status) {
    var msg = status == 0 ? 'you want to enable the ZIP code and activate the SMS service' : 'you want to disable the ZIP code and deactivate the SMS service';
    $.confirm({
        title: "Are you sure?",
        content: msg,
        type: 'blue',
        columnClass: 'col-md-6',
        buttons: {
            submit: {
                text: 'Confirm',
                btnClass: 'btn-info',
                action: function () {
                   $.ajax({
                    url: ZIPCODE_STATUS,
                    type: 'POST',
                    data: {
                        _token: _CSRF_TOKEN,
                        'id' : id,
                        'status': status
                    },
                    success: function(response) {
                        toastr.success(response.error_msg);
                        loadZipCode(1);
                    },
                    error: function(xhr) {
                        showErrorAndLoginRedirection(xhr);
                    },
                    complete: function() {
                        $('#statusUpdateBtn').prop('disabled', false);
                        $('#statusUpdateBtn .spinner-border').addClass('d-none');
                    }
                });
                }
            },
            cancel: function() {
                //close
                if(status == 1){
                    $('#row_last_status'+id).prop("checked",true);
                }else{
                    $('#row_last_status'+id).prop("checked",false);
                }
            },
        }
    });
}