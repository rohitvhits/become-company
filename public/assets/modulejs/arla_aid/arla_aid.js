$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    visitingAidList(1);
});

function visitingAidList(page) {
    $.ajax({
        url: _THIRD_PARTY_VISITING_AID_LIST+"?page=" + page,
        type: "GET",
        data: {
            'full_name': $('#full_name').val(),
            'agency_id': $('#agency_id').val(),
            'dob': $('#dob').val(),
            'gender': $('#gender').val(),
            'patient_status': $('#patient_status').val(),
            'status': $('#status').val(),
            'created_date': $('#created_date').val(),
            'due_date': $('.due_date').val(),
            'sorting_column': $('#sorting_column').val(),
            'sorting_order': $('#sorting_order').val(),
            'debug': _DEBUG_MODE,
            'mobile': $('#mobile').val()
        },
        success: function (res) {

            $('#resp').html("");
            $('#resp').html(res)
            initialize();
        }
    })
    return false;
}

function initialize() {
    var start = moment().subtract(0, 'days');
    var end = moment();

    $('.dob').datepicker();
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
    }, function (chosen_date, end_date) {

        $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })

    $('.due_date').daterangepicker({
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
    }, function (chosen_date, end_date) {

        $('.due_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
}
$('body').on('click', '#cboxid', function (e) {
    var checked = $(this).is(":checked");
    if (checked == true) {
        $('.cbox').prop('checked', true);
    } else {
        $('.cbox').prop('checked', false);
    }
})
$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    visitingAidList(page);
});

$('body').on('click', '.record_id', function (e) {
    var fields = $(this).attr('data-field');
    var sort = $(this).attr('data-sort');

    $('#sorting_column').val(fields);
    $('#sorting_order').val(sort);
    visitingAidList(1);
})

function addAppointment(id, type,agency_id) {
    $('#appointment_type').val(type)
    $('#appointment_ids').val(id)
    if (type == 'single') {
        $('#' + id).prop("checked", true);
        $('#link-modal-popup').removeClass('hide');
        loadExistingData(id);
        $('#show_modal_popup').modal('show');
        

    } else {
        var checked = $('.cbox').is(":checked");
        if (checked == false) {
            toastr.error("Please select checkbox");
            return false;
        } else {
            // importData();
        }
    }

    getBasicDetails(id,agency_id);
}


function importData() {

    var finalArray = [];
    var appointment_type = $('#appointment_type').val();
    if (appointment_type == 'single') {
        var id = $('#modal_appointment_ids').val();
        finalArray.push(id);
    } else {
        $('.cbox').each(function (i, v) {
            var schecked = $(this).is(":checked");
            if (schecked == true) {
                var values = $(this).val();
                finalArray.push(values);
            }
        });
    }


    if (finalArray.length != 0) {
        $.ajax({
            async: false,
            global: false,
            url: _THIRD_PARTY_ADD_APPOINTMENT,
            type: "post",
            data: {
                'appointment_ids': finalArray,
                '_token': _CSRF_TOKEN,

            },
            success: function (res) {
                finalArray.pop();
                toastr.success(res.error_msg);
                $('#show_modal_popup').modal('hide');
                visitingAidList(1);
            },
            error: function (xhr, status, error) {
                toastr.error(xhr.responseJSON.error_msg);
            }
        });
    }

}

function clearData() {
    $('#submitId')[0].reset();
    $('.error').html("")
    $('#service_id').html("")
    $('#agency_id').html("")
}

function loadExistingData(id) {
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _CHECK_EXISTING_DATA,
        data: {
            'id': id,
            'agency_fk': $('#' + id).val()
        },
        success: function (res) {
            var json = res.data;
            var tableResponse = "";
            $('#modal_appointment_ids').val(id);
            if (json.length != 0) {
                var cnt = 1;
                $.each(json, function (i, v) {
                    console.log(v.type)
                    tableResponse += `<tr><td>${cnt++}</td><td>${v.id}</td><td>${v.agency_detail.agency_name}</td><td><a href="${_PATIENT_VIEW}/${v.id}" target="_blank">${v.first_name + ' ' + v.last_name}</a></td><td>${v.type}</td><td>${moment(v.dob).format('MM/DD/YYYY')}</td><td>${v.status}</td><td><input type="radio" name="existing[]" value="${v.id}"></td></tr>`
                    console.log(v);
                })
            } else {
                $('#link-modal-popup').addClass('hide');
                tableResponse = `<tr><td colspan="7">No record available</td></tr>`
            }

            $('#existing_record_id').html("");
            $('#existing_record_id').html(tableResponse);
        }
    })
}

$('#saveId').click(function (e) {
    importData();
})

$('#link-modal-popup').click(function (e) {
    var record_id = $('input[name="existing[]"]:checked').val();
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _LINK_THIRD_PARTY_APPOINTMENT,
        data: {
            'id': record_id,
            'third_party_id': $('#modal_appointment_ids').val()
        },
        success: function (res) {
            toastr.success(res.error_msg);
            $('#submitId')[0].reset();
            $('#show_modal_popup').modal('hide');
        },
        error: function (jqr) {
            toastr.error("Sorry, something went wrong. Please try again.")
        }
    });
})

function linkPatient(id,agencyId){
    $('#form_link_patient_reset')[0].reset();
    $('#show_link_patient').modal('show')
    $('.search_patient').attr('id','search_patient_'+id)
    $('.token-input-list').remove();
    $('#show_modal_id').val(id)
    $('#agencyId').val(agencyId)
    $('#existing_patient_record_id').html("");
    $('#existing_patient_record_id').html("<tr><td colspan='6'>No record available</td></tr>");

    getBasicDetails(id,agencyId);
    reinitSelect2(id,agencyId)
}

function searchPatient(){
    var id = $('#show_modal_id').val()
    var search = $('#search_patient_'+id).val();
    if(search.trim() !=""){
        $('#load_link_patient_loader').attr('style','');
        $.ajax({
           
            type: "GET",
            url: _SEARCH_PATIENT,
            data: {
                'search_patient': search
            },
            success: function (res) {
                $('#load_link_patient_loader').attr('style','display:none');
                var json = res.data;
               
                var tableResponse = "";
               
                if (json.length != 0) { 
                    var cnt = 1;
                    $.each(json, function (i, v) {
                        var date ="";
                        if(v.dob !=null && v.dob !="0000-00-00"){
                            date=moment(v.dob).format('MM/DD/YYYY');
                        }
                        var link = _PATIENT_VIEW+'/'+v.id;
                        tableResponse += `<tr><td>${cnt++}</td><td><a href="${link}" target="_blank">${v.id}</a></td><td><a href="${link}" target="_blank">${v.first_name} ${v.last_name}</a></td><td>${date}</td><td>${v.mobile}</td><td>${v.status}</td><td><input type="radio" name="link_patients" id="ls${v.id}" class="selected_visiting_aid" value="${v.id}"></td></tr>`
                    })
                } else {
                    tableResponse = `<tr><td colspan="7">No record available</td></tr>`
                }
    
                $('#existing_patient_record_id').html("");
                $('#existing_patient_record_id').html(tableResponse);
            }
        })
    }
    
}

$('#linkToPatientVisitModal').click(function(e){
    var record_id = $('.search_patient').val();
   var cnt =0;
   $('.search_patient_error').html("");
   if(record_id ==""){
    $('.search_patient_error').html("Please select");
    cnt =1;
   }

   if(cnt ==1){
    return false;
   }else{
        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _UPDATE_LINK_THIRD_PARTY_APPOINTMENT,
            data: {
                'id': record_id,
                'third_party_id': $('#show_modal_id').val(),
                '_token':_CSRF_TOKEN
            },
            success: function (res) {
                toastr.success(res.error_msg);
                visitingAidList(1);
                $('#close-modal-patient').click();
            },
            error: function (jqr) {
                toastr.error("Sorry, something went wrong. Please try again.")
            }
        });
   }
    
    
})

function linkServiceRequest(id,patientId){
    closeVisitingAid();
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _LINK_PATIENT_SERVICES,
        data: {
            'id': id,
            'patientId': patientId,
      
        },
        success: function (res) {
            
          
           var json = res.data;
           var html = "<option value=''>Select Request Services</option>";
           $.each(json,function(i,v){
            html +='<option value="'+v.id+'">'+v.name+'</option>'
           })

           $('#request_service_id').html("")
           $('#request_service_id').html(html)
           $('#patient_id').val(patientId)
           $('#third_party_id').val(id)
           $('#show_link_service').modal('show');
        },
        error: function (jqr) {
            toastr.error("Sorry, something went wrong. Please try again.")
        }
    });
}

$('#linkToServiceRequestModal').click(function(e){
    var request_service_id = $('#request_service_id').val();
    var cnt =0;
    $('.request_service_id_error').html("");
    if(request_service_id ==''){
        $('.request_service_id_error').html("Please select Request Services");
        cnt =1;
    }

    if(cnt ==1){
        return false;
    }else{

        $.ajax({
            async: false,
            global: false,
            type: "POST",
            url: _UPDATE_PATIENT_SERVICES,
            data: {
                'patient_id': $('#patient_id').val(),
                'third_party_id': $('#third_party_id').val(),
                'request_service_id':$('#request_service_id').val(),
                '_token':_CSRF_TOKEN
            },
            success: function (res) {
                toastr.success(res.error_msg)
                $('#show_link_service').modal('hide');
                visitingAidList(1)
                closeVisitingAid()
            },
            error: function (jqr) {
                toastr.error(jqr.responseJSON.error_msg)
            }
        });
    }
})

function closeVisitingAid(){
    $('.request_service_id_error').html("");
    $('#form_link_service_request')[0].reset();

}

function getBasicDetails(id,agencyId){
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _THIRD_PART_DETAILS,
        data: {
            'id': id,
            'agencyId': agencyId,
      
        },
        success: function (res) {
            const fullName = `${res.data.first_name || 'N/A'} ${res.data.last_name || 'N/A'}`;
            var address1 = res.data.address1 ? res.data.address1 : 'N/A';
            var address2 = res.data.address2 ? res.data.address2 : 'N/A';
            var city = res.data.city ? res.data.city : 'N/A';
            var state = res.data.state ? res.data.state : 'N/A';
            var county = res.data.county ? res.data.county : 'N/A';
            var zip_code = res.data.zip_code ? res.data.zip_code : 'N/A';
            var full_address  = address1+','+address2+', '+city+', '+state+', '+county+', '+zip_code;
            $('.patient_code').text(res.data.patient_code ?res.data.patient_code:"N/A");
            $('.firstnName').text(fullName);
            $('.middlenName').text(res.data.middle_name ? res.data.middle_name : 'N/A');
            $('.lastName').text(res.data.last_name ? res.data.last_name : 'N/A');
            $('.gender').text(res.data.gender ? res.data.gender : 'N/A');
            $('.mobile').text(res.data.mobile ? res.data.mobile : 'N/A');
            $('.email').text(res.data.email ? res.data.email : 'N/A');
            $('.full_address').text(full_address);
            $('.city').text(res.data.city ? res.data.city : 'N/A');
        
            $('.zipCode').text(res.data.zip_code ? res.data.zip_code : 'N/A');
            $('.serviceName').text(res.data.service_name ? res.data.service_name : 'N/A');
            $('.agencyName').text(res.data.agency_details.agency_name ? res.data.agency_details.agency_name : 'N/A');
            $('.phone').text(res.data.phone ? res.data.phone : 'N/A');
            $('.patient_type').text(res.data.type ? res.data.type : 'N/A');
            
            $('.statusid').text(res.data.status ? res.data.status : 'N/A');
            $('.language').text(res.data.language ? res.data.language : 'N/A');
            $('.discipline').text(res.data.diciplin ? res.data.diciplin : 'N/A');
            $('.dobId').text(res.data.dob ? moment(res.data.dob).format('MM/DD/YYYY') : 'N/A');
            $('.created_date').text(res.data.created_date ? moment(res.data.created_date).format('MM/DD/YYYY H:m A') : 'N/A');

            $('.cin').text(res.data.cin ? res.data.cin : 'N/A');
            $('.emergency_contact_name').text(res.data.emergency_contact_name ? res.data.emergency_contact_name : 'N/A');
            $('.ssn_no').text(res.data.ssn ? res.data.ssn : 'N/A');
            $('.emergency_contact_no').text(res.data.emergency_phone ? res.data.emergency_phone : 'N/A');
            $('.platform_id').text(res.data.platform_id ? res.data.platform_id : 'N/A');
        },
        error: function (jqr) {
            toastr.error("Sorry, something went wrong. Please try again.")
        }
    });
}

function reinitSelect2(id,agencyId){
    var agencyId = agencyId;
    var urlToken =_SEARCH_PATIENT+ "?agency_id="+agencyId;
    $("#search_patient_"+id).tokenInput(urlToken, {
        tokenLimit: 1,
        zindex: 9999,
        onAdd: function (item) {
            
            var selectedAlaycareId = item.id;
            var name = item.name;
                $('#third_party_ids').val(selectedAlaycareId);
                $('#third_party_ids_names').val(name);
                
            },
    });


}

function resetVisitingAidList(){
    $('#due_date').val("")
    $('#agency_id').val("")
    $('#full_name').val("")
    $('#dob').val("")
    $('#gender').val("")
    $('#patient_status').val("")
    $('#status').val("")
    visitingAidList(1);
}

function viewLogFiles(id,agencyId){
    
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _UPLOAD_DOCUMENT_LIST_LINKED,
        data: {
            'id': id,
            'agencyId': agencyId,
      
        },
        success:function(res){
            var json = res.data.documents;
            var htmlResponse = "";
            var cnt =1;
            $('#existing_log_patient_record_id').html("");
            if(json.length !=0){
                $.each(json,function(i,v){
                    var links = _PATIENT_VIEW+'/'+v.patient_id;
                    htmlResponse +='<tr><td>'+cnt+++'</td><td><a href="'+links+'" target="_blank">'+v.patient_id+'</a></td><td>'+v.document_name+'</td></tr>'
                })
                
            }else{
                htmlResponse ='<tr><td colspan="4">No record available</td></tr>'
            }
            $('#existing_log_patient_record_id').html(htmlResponse);
            $('#show_log_link_patient').modal('show')

        }

    })
}

$('#third_party_patient_export').on('click', function (e) {

    var full_name = $('#full_name').val();
    var agency_id = $('#agency_id').val();
    var dob = $('#dob').val();
    var mobile = $('#mobile').val();
    var gender = $('#gender').val();
    var patient_status = $('#patient_status').val();
    var status = $('#status').val();
    var created_date = $('#created_date').val();
    var sorting_column = $('#sorting_column').val();
    var sorting_order = $('#sorting_order').val();

    $.ajax({
      
        type: "GET",
        url: _THIRD_PARTY_PATIENT_EXPORT,
        data: {
            agency_id:agency_id,
            full_name:full_name,
            dob:dob,
            mobile:mobile,
            gender:gender,
            patient_status:patient_status,
            status:status,
            created_date:created_date,
            'due_date': $('#due_date').val(),
           
        },

        success:function(res){
            var blob = new Blob([res]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);


            link.download = "arlaaids"+_DATE_TIME+".csv";
            link.click();
        }
    });

});

function viewPortalLogs(id){
    
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _VIEW_PORTAL_LOG,
        data: {
            'id': id,
         
      
        },
        success:function(res){
            $('.dataContainer').html('');
           var json = res.data;
            $('#exampleModal-4').modal('show');
            
            let content = '';
            content += ` <pre>{<br>`;
            $.each(JSON.parse(json.new_response), function(key, value) {
                if(key !="platform_id"){
                var values = "-";
                    if (value === undefined || value === null || value === "") {
                    
                    }else{
                        values = value;
                    
                    }
                    content += `<span class="key">"${capitalizeFirstLetter(key.replace('_', ' '))}"</span>: <span class="string">"${values}"</span>,<br>`;
                }
            });
            content += ` } <pre>`;
            
            $('.dataContainer').html(content);
         
        }

    })
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}