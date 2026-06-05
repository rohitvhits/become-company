let selectedDates = [];
$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadDisableDateList(1);
});


function loadDisableDateList(page) {
    $.ajax({
        url: DISABLE_DATE_LIST + "?page=" + page,
        type: "get",
        data: {
        },
        success: function (response) {
            $('#resp').html("")
            $('#resp').html(response);
        }
    });
}

function getDisableDate() {
    $("#disable_date")[0].reset();
    $("#disable_date_error").html("");
    $('#disableDateModal').modal('show');
    
    $('#disableDateModal').css({
        zIndex: '99999'
    })
    selectedDates = [];
    loadReinitDatepicker();
}

function getEditDisableDate(id) {
    $('#id').val(id);
    $('#disableEditModal').modal('show');
    $('#disableEditModal').css({
        zIndex: '99999'
    }) 
    $("#disable_date_edit_error").html("");
    $("#disableEditDate")[0].reset();
    getModalData(id);
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadDisableDateList(page);
});

function getModalData(id) {
    $.ajax({
        async: false,
        global: false,
        url: DISABLE_DATE_BY_ID,
        type: "get",
        data: {
            'id': id
        },
        success: function (response) {
            var json = response.data;
            selectedDates = [];
            let datesString = json.disable_dates; // Use mm/dd/yyyy format
             selectedDates = datesString.split(',').map(date => date.trim());
            $('input[value="'+json.type+'"]').prop("checked",true)
            // Initialize the datepicker
            $("#disable_edit_date").datepicker({
                onSelect: function (dateText) {
                    // Toggle date selection
                    const index = selectedDates.indexOf(dateText);
                    if (index > -1) {
                        selectedDates.splice(index, 1); // Remove if already selected
                    } else {
                        selectedDates.push(dateText); // Add if not selected
                    }
                    // Display selected dates in input
                    $(this).val(selectedDates.join(", "));
                },
                beforeShowDay: function (date) {
                    const formattedDate = $.datepicker.formatDate("mm/dd/yy", date);
                    
                    if (selectedDates.includes(formattedDate)) {
                
                        return [true, "ui-state-highlighted"]; // Highlight selected dates
                    }
                    return [true, ""];
                },
            });

            // Populate the input field with pre-selected dates for the edit case
            $("#disable_edit_date").val(selectedDates.join(", "));
            $('#edit_time_id').val(json.time)
        }
    })
}
function save(){
    $('#loaderAddDisableDate').removeClass('d-none');
    $('#btn-save-disable-date').text('Saving...');

    var disable_date = $("#disable_dates").val();

    $("#disable_date_error").html("");
    var cnt = 0;

    if (disable_date.trim() == "") {
        $("#disable_date_error").html("Please enter date");
        cnt = 1;
    }
    if (cnt == 0) {
        
        $("#disableDateSave").prop("disabled", true);
        var formData = new FormData($("#disable_date")[0]);
        formData.append('_token', _CSRF_TOKEN)
        $.ajax({
            type: "POST",
            url: DISABLE_DATE,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                
                $("#disable_date")[0].reset();
                $("#disableDateSave").prop("disabled", false);
                $("#disableDateModal").modal("hide");
                $("#id").val("");
                $('#loaderAddDisableDate').addClass('d-none');
                $('#btn-save-disable-date').text('Save');
                
                loadDisableDateList(1);
            },
            error: function (jqXHR) {
                $("#disableDateSave").prop("disabled", false);
                showErrorAndLoginRedirection(jqXHR)
            },
        });
    } else {
        $('#loaderAddDisableDate').addClass('d-none');
        $('#btn-save-disable-date').text('Save');
        return false;
    }
}

function update()
{

    $('#loaderEditDisable').removeClass('d-none');
    $('#btn-update-disable-date').text('Updating...');
    var cnt = 0;
    var disable_date = $("#disable_edit_date").val();
    $("#disable_date_edit_error").html("");
    if (disable_date === "") {
        $("#disable_date_edit_error").html("Please enter date");
        cnt = 1;
    }

    var id = $("#id").val();

    if (cnt == 0) {
       
       
        $("#disableDateUpdate").prop("disabled", true);
        var formData = new FormData($("#disableEditDate")[0]);
        formData.append('_token', $('input[name=_token]').val());
        formData.append('_method', 'PUT');
        $.ajax({
            url: DISABLE_DATE + '/' + id,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                toastr.success(res.error_msg);
                $("#disableEditDate")[0].reset();
                $("#disableDateUpdate").prop("disabled", false);
                $("#disableEditModal").modal("hide");
                $("#id").val(id)
                $('#loaderEditDisable').addClass('d-none');
                $('#btn-update-disable-date').text('Update');
                loadDisableDateList(1);

            },
            error: function (jqXHR) {
                $("#disableDateUpdate").prop("disabled", false);
                showErrorAndLoginRedirection(jqXHR);
            },
        });
    } else {
        return false;
    }
}

function deleteDisableDate(id) {
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
                            url: DISABLE_DATE + '/' + id,
                            type: "DELETE",
                            data: {
                                '_token': _CSRF_TOKEN
                            },
                            success: function (response) {
                                toastr.success(response.error_msg);
                                loadDisableDateList(1);
                            },
                            error: function (xhr, status, error) {
                                showErrorAndLoginRedirection(xhr);
                            
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



$(document).ready(function () {
   loadReinitDatepicker();
});

function loadReinitDatepicker(){
    selectedDates = [];

    $(".date").datepicker({
        onSelect: function (dateText, inst) {
            // Toggle date selection
           
            const index = selectedDates.indexOf(dateText);
            if (index > -1) {
                selectedDates.splice(index, 1); // Remove the date if already selected
            } else {
                selectedDates.push(dateText); // Add the date if not selected
            }
        
            // Display selected dates
            $(this).val(selectedDates.join(", "));
        },
        beforeShowDay: function (date) {
            const formattedDate = $.datepicker.formatDate("mm/dd/yy", date);
            
            if (selectedDates.includes(formattedDate)) {
                return [true, "ui-state-highlighted"]; // Highlight selected dates
            }
            return [true, ""];
        },
    });
}
