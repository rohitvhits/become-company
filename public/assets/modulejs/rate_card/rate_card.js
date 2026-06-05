$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadRateCardList(1);
});

$(document).ready(function() {
    $('.js-example-basic-single').select2();
  });

function loadRateCardList(page) {
    $('.rate-card-wise-data-loader').attr('style','display:');
    $('#resp').html("")
    $.ajax({
        url: _RATECARD_LIST + "?page=" + page,
        type: "get",
        data: {
        },
        success: function (response) {
            $('.rate-card-wise-data-loader').attr('style','display:none');
            $('#resp').html("")
            $('#resp').html(response);
        }
    });
}

function getRateCard() {
    $('#ratecardModal').modal('show');
    $('#ratecardModal').css({
        zIndex: '99999'
    })
    $("#service_error").html("");
    $("#add_service_id").val("").change();
    $("#amount").val("");
    $("#amount_error").html("");
}

function getEditRateCard(id) {
    $('#edit_id').val(id);
    $('#rateCardEditModal').modal('show');
    $('#rateCardEditModal').css({
        zIndex: '99999'
    })
    $("#service_edit_error").html("");
    $("#amount_edit_error").html("");
    $("#edit_rate_card_form")[0].reset();
    $("#edit_service_id").val("").change();
    $("#edit_amount").val("");
    getModalData(id);
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadRateCardList(page);
});

function getModalData(id) {
    $.ajax({
        async: false,
        global: false,
        url: _RATECARD_BY_ID,
        type: "get",
        data: {
            'id': id
        },
        success: function (response) {
            var json = response.data;
            if (json) {
                $('#edit_service_id').val(json.service_id).change();
                $('#edit_amount').val(json.amount);
            }
        }
    })
}
function save(){
    var service = $("#add_service_id").val();
    var amount = $("#amount").val();
    $("#service_error").html("");
    $("#amount_error").html("");
    var cnt = 0;

    if (service == "") {
        $("#service_error").html("Please select Service");
        cnt = 1;
    }
    if (amount.trim() == "") {
        $("#amount_error").html("Please enter Amount");
        cnt = 1;
    }
    if (amount != "" && (amount == "." || amount <= 0)) {
        $("#amount_error").html("Please enter valid Amount");
        cnt = 1;
    }
    if (cnt == 0) {
        $("#rateCardSave").prop("disabled", true);
        var formData = new FormData($("#rate_card_form")[0]);
        $.ajax({
            type: "POST",
            url: _RATECARD,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#rate_card_form")[0].reset();
                $("#rateCardSave").prop("disabled", false);
                $("#ratecardModal").modal("hide");
                $("#edit_id").val("").change();
                loadRateCardList(1);
            },
            error: function (jqXHR) {
                $("#rateCardSave").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
}

function update(){
    var service = $("#edit_service_id").val();
    var amount = $("#edit_amount").val();
    var id = $("#edit_id").val();
    $("#service_edit_error").html("");
    $("#amount_edit_error").html("");
    var cnt = 0;

    if (service == "") {
        $("#service_edit_error").html("Please select Service");
        cnt = 1;
    }
    if (amount.trim() == "") {
        $("#amount_edit_error").html("Please enter Amount");
        cnt = 1;
    }
    if (amount != "" && (amount == "." || amount <= 0)) {
        $("#amount_edit_error").html("Please enter valid Amount");
        cnt = 1;
    }
    if (cnt == 0) {
        $("#rateCardUpdate").prop("disabled", true);
        var formData = new FormData($("#edit_rate_card_form")[0]);
        formData.append('_token', $('input[name=_token]').val());
        formData.append('_method', 'PUT');
        $.ajax({
            url: _RATECARD + '/' + id,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#edit_rate_card_form")[0].reset();
                $("#rateCardUpdate").prop("disabled", false);
                $("#rateCardEditModal").modal("hide");
                $("#rate_id").val("").change();
                loadRateCardList(1);

            },
            error: function (jqXHR) {
                $("#rateCardUpdate").prop("disabled", false);
                toastr.error(jqXHR.responseJSON.error_msg);
            },
        });
    } else {
        return false;
    }
}

function deleteRateCard(id) {
    if (id != '') {
        $.confirm({
            title: 'Are you sure?',
            content: 'you want to delete this record.',
            type: 'blue',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    action: function () {
                        $.ajax({
                            global: false,
                            url: _RATECARD + '/' + id,
                            type: "DELETE",
                            data: {
                                '_token': _CSRF_TOKEN
                            },
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadRateCardList(1);
                            },
                            error: function (xhr, status, error) {
                                toastr.error(xhr.responseJSON.error_msg);
                            }
                        });
                    }
                },
                cancel: function () {

                }
            }
        })

    }
    return false;
}

function isNumber(evt) {

    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

        return false;
    }
    return true;
}

$('#amount').on('keypress', function (evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;

    // Allow only numbers (48-57) and single dot (46)
    if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {
        return false;
    }
    var maxLengthBeforeDot = 8;  // Max digits before dot
    var maxLengthTotal = 10; 
    // Check if the current value is valid
    var currentValue = $(this).val();
    // Check if currentValue is not undefined or null
    if (currentValue === undefined || currentValue === null) {
        currentValue = ''; // Assign an empty string if it's undefined or null
    }
    var decimalIndex = currentValue.indexOf('.');
    var beforeDotLength = decimalIndex === -1 ? currentValue.length : decimalIndex;
    var totalLength = currentValue.replace('.', '').length;
    if ((beforeDotLength >= maxLengthBeforeDot) || (totalLength > maxLengthTotal)) {
        return false;  // Block invalid input
    }
    return true;
});

$('#amount').on('paste', function (evt) {
    evt.preventDefault();
});

$('#edit_amount').on('keypress', function (evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;

    // Allow only numbers (48-57) and single dot (46)
    if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {
        return false;
    }

    var maxLengthBeforeDot = 8;  // Max digits before dot
    var maxLengthTotal = 10; 
    // Check if the current value is valid
    var currentValue = $(this).val();
    // Check if currentValue is not undefined or null
    if (currentValue === undefined || currentValue === null) {
        currentValue = ''; // Assign an empty string if it's undefined or null
    }
    var decimalIndex = currentValue.indexOf('.');
    var beforeDotLength = decimalIndex === -1 ? currentValue.length : decimalIndex;
    var totalLength = currentValue.replace('.', '').length;
    if ((beforeDotLength >= maxLengthBeforeDot) || (totalLength > maxLengthTotal)) {
        return false;  // Block invalid input
    }
    return true;
});

$('#edit_amount').on('paste', function (e) {
    e.preventDefault();
});