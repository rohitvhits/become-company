if(typeof RECORD_LIST !="undefined"){
    hubList(1);
}

function hubList(page=1){
    $('#list_res').html("")
    $('.hideClass').removeClass('d-none');
    let agency_fk = $('#agency_fk').val();
    let created_date = $('#created_date').val();
    let created_by = $('#created_by_ny_id').val();

    $.ajax({
        url: RECORD_LIST+"?page=" + page,
        type: "get",
        data:{
            'agency_fk':agency_fk,
            'created_date':created_date,
            'created_by':created_by
        },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            $('#list_res').html("")
            $('#list_res').html(response);
        
        }
    });
}

$(document).on('click', '.list_paginate .pagination a', function(e) {
    e.preventDefault();
    let page = $(this).attr('href').split('page=')[1]; 
    hubList(page);
});

function refresh(){
    $('#agency_fk').val('').trigger("change")
    $('#created_date').val('').trigger("change")
    $('#created_by_ny').val('').trigger("change")
    hubList(1);
}

function exportCsv()
{
    $('.hideClass').removeClass('d-none');
    let agency_fk = $('#agency_fk').val();
    let created_date = $('#created_date').val();
    let created_by = $('#created_by_ny_id').val();
  
    $.ajax({
        url: HUB_RECORD_CSV,
        type: "get",
        data:{
            'agency_fk':agency_fk,
            'created_date':created_date,
            'created_by':created_by,
        },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            let blob = new Blob([response]);
            console.log(response);
            if(response == ""){
                toastr.error('Please check there is no data to export.');
            }else{
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                let form_name = "hub_record_"+_DATE_TIME;
                link.download = form_name + ".csv";
                link.click();
            }
        }
    });
}

$(function() {
    let start = moment().subtract(0, 'days');
    let end = moment();
    if(typeof _FLAG =='undefined'){
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
    }
    
});

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

$(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    if(typeof _FLAG =='undefined'){
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
    }
    
});

function clearModal(){
    $('#add_new_hub')[0].reset();
    $("#locationId").val('').trigger("change");
}

function isNumber(evt) {

    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {

        return false;
    }
    return true;
}


if(typeof urlToken !="undefined"){
    $("#created_by_ny").tokenInput(urlToken, {
        tokenLimit: 1,
        zindex: 9999,
        onAdd: function (item) {
            $('#created_by_ny_id').val(item.id);
            $('#created_by_ny_name').val(item.name);
        },
        onDelete:function(item){
            $('#created_by_ny_id').val('');
            $('#created_by_ny_name').val('');
        }
    });
}


function saveImport() {
    $('#import-save').attr('disabled','');
    $('#importLoader').show();
    $('#importResponseMsg').hide().html("");
    var agency_ids = $('#agency_ids').val();
    var fimagesG = $('input[name="images"]').prop('files');
    var cnt = 0;
    $('#images_error').html("");
    $('#agency_error').html("");
    if(agency_ids == null || agency_ids == '') {
        $('#agency_error').html("Please select agency.");       
        cnt = 1;
    }
    if (fimagesG.length == 0) {
        $('#images_error').html("Csv file is required.");
        cnt = 1;
    } else {
        var FileUploadPath = fimagesG[0].name;
        var Extension = FileUploadPath.substring(
            FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
        if (Extension == 'csv') {
        } else {
            $('#images_error').html("Only csv file allowed");
            cnt = 1;
        }
    }

    if (cnt == 1) {
        $('#importLoader').hide();
        return false;
    } else {
        var foms = $('#formnew')[0];
        var formData = new FormData(foms);
        formData.append("_token", _CSRF_TOKEN);
        $('#importResponseMsg').html('').show();
        $.ajax({
            processData: false,
            contentType: false,
            type: "POST",
            url: IMPORT_DATA,
            data: formData,
            success: function(res) {
                let msg = '';
                if (res.status && res.summary) {
                    msg = `<div class='alert alert-success'>Imported: ${res.summary.imported}, Updated: ${res.summary.updated}, Skipped: ${res.summary.skipped}, Deactivated: ${res.summary.deactivated}`;
                    if(res.summary.errors && res.summary.errors.length > 0) {
                        msg += `<br><b>Errors:</b><ul style='text-align:left;'>`;
                        res.summary.errors.forEach(function(e){ msg += `<li>${e}</li>`; });
                        msg += '</ul>';
                    }
                    msg += '</div>';
                } else if(res.message) {
                    msg = `<div class='alert alert-danger'>${res.message}</div>`;
                } else {
                    msg = `<div class='alert alert-danger'>Import failed.</div>`;
                }
                $('#importLoader').hide();
                $('#importResponseMsg').html(msg).show();
                if (res.status && res.summary && res.summary.imported > 0) {
                    hubList(1);
                    $('#importModal').modal('hide');
                }
                $('#import-save').attr('disabled','disabled');
            },
            error: function(jqr) {
                $('#importLoader').hide();
                let msg = `<div class='alert alert-danger'>Import failed. Please try again.</div>`;
                if(jqr.responseJSON && jqr.responseJSON.message) {
                    msg = `<div class='alert alert-danger'>${jqr.responseJSON.message}</div>`;
                }
                $('#importResponseMsg').html(msg).show();
            }
        })
    }
}

$(document).on('click', '.hub_record_log .pagination a', function(e) {
    e.preventDefault();
    let page = $(this).attr('href').split('page=')[1]; 
    console.log(page);
    loadImport(page);
});

function refreshImport(){
    $('#file-name').val('');
    $('#status').val('');
    $('#date-range').val('');
    loadImport(1);
}

function openImportModal() {
    $('#importModal').modal('show');
    $('#importResponseMsg').hide().html("");
    $('#importLoader').hide();
    $('#agency_ids').val('').trigger("change");
    $("#images_error").html("");
    $("#agency_error").html("");
    $('#formnew')[0].reset();
    $('#import-save').attr('disabled',false);
}

function openCreateModel(){
    $('#add_new_hub')[0].reset();
    $('#hubModal').modal('show');
    $('#agency_name_error').html("");
    $('#last_name_error').html("");
    $('#phone_error').html("");
    $('#mobile_error').html("");
    $('#dob_error').html("");
    $('#radio_type_error').html("");
    $('#email_error').html("");
    $('#other_name_error').html("");
    $('#address2_error').html("");
}

$('#agency_ids,#timeidnew').on('change', function() {
    $('#import-save').attr('disabled',false);
});

$('#dep_ssn').keyup(function() {
    var val = this.value.replace(/\D/g, '');
    val = val.replace(/^(\d{3})/, '$1-');
    val = val.replace(/-(\d{2})/, '-$1-');
    val = val.replace(/(\d)-(\d{4}).*/, '$1-$2');
    this.value = val;
});