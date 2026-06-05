// $('#fu_date').datepicker();
function getAppointmentData(type) {
    $('.checkBtn').removeClass('next');
    var selectDateVal = $('#fu_date').val(); 
    var type = $('#search_day').val();
    if (type == 'daily') {
        $('#daily').addClass('next')
        if (selectDateVal) {
            var selectDateVal = $('#fu_date').val();
            var startOfWeek = moment(selectDateVal).format('YYYY-MM-DD')
            var endOfWeek = moment(selectDateVal).format('YYYY-MM-DD')
        } else {
            var startOfWeek = moment().format('YYYY-MM-DD');
            var endOfWeek = moment().format('YYYY-MM-DD');
        }
    } else if (type == 'monthly') {
        $('#monthlyId').addClass('next')
        var startOfWeek = moment().startOf('month').toDate();
        startOfWeek = moment(startOfWeek).format('YYYY-MM-DD');
        var endOfWeek = moment().endOf('month').toDate();
        endOfWeek = moment(endOfWeek).format('YYYY-MM-DD');
        if(selectDateVal != ''){
            startOfWeek = moment(selectDateVal).startOf('month').format('YYYY-MM-DD');
            endOfWeek = moment(selectDateVal).endOf('month').format('YYYY-MM-DD');
        } 
    } else {
        $('#weeklyId').addClass('next')
        var startOfWeek = moment().startOf('week').toDate();
        startOfWeek = moment(startOfWeek).format('YYYY-MM-DD');
        var endOfWeek = moment().endOf('week').toDate();
        endOfWeek = moment(endOfWeek).format('YYYY-MM-DD');
        if(selectDateVal != ''){
            startOfWeek = moment(selectDateVal).startOf('week').format('YYYY-MM-DD');
            endOfWeek = moment(selectDateVal).endOf('week').format('YYYY-MM-DD');
        } 
    }
    if(type == 'monthly'){
        var id = $('#emc_id').val();
        var location_id = $('#location_id').val();
        var agency_id = $('#agency_id').val();
        var status = $('#status').val();
        var appointemnt_type = $('#appointemnt_type').val();
        getMonthlyAppoitmentData(id,location_id,agency_id,status,type,appointemnt_type,startOfWeek,endOfWeek);
    } 
    else {
        showCalendarShimmer();
        // $('.loader-sec').show();
        $.ajax({
            url: _GET_APPOINTMENT_DATA,
            type: 'GET',
            dataType: 'json',
            data: {
                id: $('#emc_id').val(),
                location_id: $('#location_id').val(),
                agency_id: $('#agency_id').val(),
                type: type,
                startOfWeek: startOfWeek,
                endOfWeek: endOfWeek,
                status: $('#status').val(),
                appointemnt_type: $('#appointemnt_type').val(),
            },
            success: function (response) {
                var timeJson = response.data.time;
                var weekJson = response.data.week;
                var responseJson = response.data.finalArray;
                var tableThead = "<thead>";
                if (timeJson.length != 0) {
                    tableThead = '<th>All-Days</th>';
                }

                if (weekJson.length != 0) {
                
                    $.each(weekJson, function (i, v) {
                        tableThead += '<th>' + moment(v).format('dddd') +' (' + moment(v).format('MM/DD')+')</th>';
                    })
                }

                tableThead += '</thead>';
                var tableTbody = "<tbody>";
                var times = [];
                if (timeJson.length != 0) {
                    $.each(timeJson, function (i, vs) {
                        var responseTDs = [];
                        var responseTD = "";
                        var colorClass = ''; 
                        $.each(weekJson, function (i, vkt) {
                        
                            if (responseJson[vkt][vs] != "") {
                                
                                responseTDs[vkt] = [];
                               
                                $.each(responseJson[vkt][vs], function (i, rs) {
                                    colorClass = ''; 
                                   
                                    if (rs.patient.status === 'Pending') {
                                        colorClass = 'badge-warning';
                                    }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                                        colorClass = 'badge-info'; 
                                    }else if(rs.patient.status === 'completed'){
                                        colorClass = 'badge-success'; 
                                    }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                                        colorClass = 'badge-danger'; 
                                    } else if(rs.patient.status === 'noshow'){
                                        colorClass = 'badge-light'; 
                                    }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                                        colorClass = 'badge-primary'; 
                                    }else if(rs.patient.status === 'hospitalized/rehab'){
                                        colorClass = 'badge-secondary';
                                    }else {
                                        colorClass = 'badge-dark';
                                    }


                                    // var colorClass = (rs.status === 'Pending') ? 'bg-secondary' : 'bg-success';
                                    var data = '<a  class="event_label" onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span></a><br>'
                                    responseTDs[vkt] = (responseTDs[vkt] ? responseTDs[vkt] + ' ' : '') + data;
                                    // responseTDs[vkt].push(helloMagicWorldString)
                                })
                            } else {
                                responseTDs[vkt] = '';
                            }
                            responseTD += "<td>" + responseTDs[vkt] +"</td>";
                        });
                        tableTbody += '<tr><td>' + vs + '</td>' + responseTD + '</tr>';
                    

                    });

                }

                var tableHtml = "<table class='table table-bordered'>" + tableThead + tableTbody +"</table>";
                $('#calender_response').html("");
                $('#calender_response').html(tableHtml)
                // $('.loader-sec').hide();
                hideCalendarShimmer();

            },
            error: function (xhr, status, error) {
                console.log(status);
                console.error('AJAX request failed:', status, error);
            }
        });
    }   
    refreshData();
}

function getMonthlyAppoitmentData(id,location_id,agency_id,status,type,appointemnt_type,startOfWeek,endOfWeek){
    month = moment(startOfWeek).format('MMMM');
    prevMonth = moment(startOfWeek).subtract(1, 'months').endOf('month').format('MM');
    prevMonthText = moment(startOfWeek).subtract(1, 'months').endOf('month').format('MMMM');
    prevYear = moment(startOfWeek).subtract(1, 'months').endOf('month').format('YYYY');
    prevMonthFull = moment(startOfWeek).subtract(1, 'months').endOf('month').format('YYYY-MM-DD');
    // $('.loader-sec').show();
    showCalendarShimmer();
    $.ajax({
        url: _GET_MONTHLY_APPOINTMENT_DATA,
        type: 'GET',
        dataType: 'json',
        data: {
            id: id,
            location_id: location_id,
            agency_id: agency_id,
            type: type,
            startOfWeek: startOfWeek,
            endOfWeek: endOfWeek,
            status: status,
            appointemnt_type: appointemnt_type,
        },
        success: function (response) {
            var weekJson = response.data.weekDayArray;
            var firstWeek = response.data.firstWeek;
            var weekcount = response.data.firstWeek;
            var responseJson = response.data.monthlyData;
            var previousMonthJson = response.data.previousdata;
            var tableThead = "<thead>";
            
            if (weekJson.length != 0) {
                $.each(weekJson, function (i, v) {
                    tableThead += '<th class="center">' + v +'</th>';
                })
            }
            tableThead += '</thead>';
            var tableTbody = "<tbody>";
            var times = [];
            var responseTD = "<tr>";
            if (responseJson.length != 0) {
                responseTD += "<tr>";
                let lastDayOfPreviousMonth = response.data.previousMonthLastDay;
                for (var i = 0; i < firstWeek; i++) {
                    responseTD += "<td class='top-right'>";
                    responseTD += "<div class='top-right light-gray-color'>"+(lastDayOfPreviousMonth - (weekcount-1))+" ";
                    responseTD += "<div class='row'>";
                    content = '';
                    date = prevYear+'-'+prevMonth+'-'+(lastDayOfPreviousMonth - (weekcount-1));
                    if(previousMonthJson[date].data != null && previousMonthJson[date].data.length != 0){
                        lengthData = previousMonthJson[date].data.length - 5; 
                        $.each(previousMonthJson[date].data, function(i,rs){
                            var data = '';
                            var colorClass = ''; 
                            if (rs.patient.status === 'Pending') {
                                var colorClass = 'badge-warning';
                            }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                                var colorClass = 'badge-info'; 
                            }else if(rs.patient.status === 'completed'){
                                var colorClass = 'badge-success'; 
                            }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                                var colorClass = 'badge-danger'; 
                            } else if(rs.patient.status === 'noshow'){
                                var colorClass = 'badge-light'; 
                            }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                                var colorClass = 'badge-primary'; 
                            }else if(rs.patient.status === 'hospitalized/rehab'){
                                var colorClass = 'badge-secondary';
                            }else {
                                var colorClass = 'badge-dark';
                            } 
                              
                            if(i <= 4){
                                data += '<a  class="event_label" onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span></a><br>';
                            }else{
                                if(i==5){
                                    data += '<a class="" data-id="' + previousMonthJson[date]['day']+prevMonthText + '" id="openPopupBtn" onclick="openPopup(event,\'' + (lastDayOfPreviousMonth - (weekcount-1)) + '\',\'' + prevMonthText + '\',\'' + previousMonthJson[date]['weekday'] + '\');"><i class="fa fa-plus-circle" aria-hidden="true"></i> '+lengthData+' more </a>' ;
                                }
                            }
                            content += data;
                        });

                        content += '<div class="events_'+previousMonthJson[date]['day']+prevMonthText
                        +'" style="display:none">';
                        $.each(previousMonthJson[date].data, function(i,rs){
                            var hideContentData = colorClass = ''; 
                            if (rs.patient.status === 'Pending') {
                                var colorClass = 'badge-warning';
                            }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                                var colorClass = 'badge-info'; 
                            }else if(rs.patient.status === 'completed'){
                                var colorClass = 'badge-success'; 
                            }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                                var colorClass = 'badge-danger'; 
                            } else if(rs.patient.status === 'noshow'){
                                var colorClass = 'badge-light'; 
                            }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                                var colorClass = 'badge-primary'; 
                            }else if(rs.patient.status === 'hospitalized/rehab'){
                                var colorClass = 'badge-secondary';
                            }else {
                                var colorClass = 'badge-dark';
                            } 
                            hideContentData += '<a onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span></a><br>';

                            content += hideContentData;
                        });
                        content += '</div>';
                        responseTD += content;
                        responseTD += "</div>";
                        responseTD += "</div>";
                    }
                    
                    responseTD += "</td>";
                    weekcount--;
                }
                $.each(responseJson, function (i, vs) {
                    if ((firstWeek + vs['day'] - 1) % 7 == 0 && vs['day'] != 1) {
                        responseTD += "</tr><tr>";
                    }
                    responseTD += "<td class='top-right'>";
                    responseTD += "<div class='top-right'>"+vs['day']+" ";
                    responseTD += "<div class='row'>";
                    content = '';
                    if(vs['data'] != null && vs['data'].length != 0){
                        lengthData = vs['data'].length - 5; 
                        $.each(vs['data'], function(i,rs){
                            var data = '';
                            var colorClass = ''; 
                            if (rs.patient.status === 'Pending') {
                                var colorClass = 'badge-warning';
                            }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                                var colorClass = 'badge-info'; 
                            }else if(rs.patient.status === 'completed'){
                                var colorClass = 'badge-success'; 
                            }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                                var colorClass = 'badge-danger'; 
                            } else if(rs.patient.status === 'noshow'){
                                var colorClass = 'badge-light'; 
                            }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                                var colorClass = 'badge-primary'; 
                            }else if(rs.patient.status === 'hospitalized/rehab'){
                                var colorClass = 'badge-secondary';
                            }else {
                                var colorClass = 'badge-dark';
                            } 
                              
                            if(i <= 4){
                                data += '<a  class="event_label" onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span></a><br>';
                            }else{
                                if(i==5){
                                    data += '<a class="" data-id="' + vs['day']+month + '" id="openPopupBtn" onclick="openPopup(event,\'' + vs['day'] + '\',\'' + month + '\',\'' + vs['weekday'] + '\');"><i class="fa fa-plus-circle" aria-hidden="true"></i> '+lengthData+' more </a>' ;
                                }
                            }
                            content += data;
                        });
                    }
                    content += '<div class="events_'+vs['day']+month+'" style="display:none">';
                    $.each(vs['data'], function(i,rs){
                        var hideContentData = colorClass = ''; 
                        if (rs.patient.status === 'Pending') {
                            var colorClass = 'badge-warning';
                        }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                            var colorClass = 'badge-info'; 
                        }else if(rs.patient.status === 'completed'){
                            var colorClass = 'badge-success'; 
                        }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                            var colorClass = 'badge-danger'; 
                        } else if(rs.patient.status === 'noshow'){
                            var colorClass = 'badge-light'; 
                        }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                            var colorClass = 'badge-primary'; 
                        }else if(rs.patient.status === 'hospitalized/rehab'){
                            var colorClass = 'badge-secondary';
                        }else {
                            var colorClass = 'badge-dark';
                        } 
                        hideContentData += '<a onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span></a><br>';

                        content += hideContentData;
                    });
                    content += '</div>';
                    responseTD += content;
                    responseTD += "</div>";
                    responseTD += "</div>";
                    responseTD += "</td>";
                });

                tableTbody += responseTD;
            }
            var tableHtml = "<table class='table table-bordered'>" + tableThead + tableTbody +"</table>";
            $('#calender_response').html("");
            $('#calender_response').html(tableHtml);
            // $('.loader-sec').hide();
            hideCalendarShimmer()

            loadRecentNotes(response.data.patient.id);
        },
        error: function (xhr, status, error) {
            console.log(status);
            console.error('AJAX request failed:', status, error);
        }
    });
}

function getAppointmentDetails(eventId) {
    // $('.loader-sec').show();
    showCalendarShimmer();
    $.ajax({
        type: "GET",
        url: _GET_APPOINTMENT_DETAILS,
        data: {
            id: eventId,
        },
        success: function (response) {
            if(response.data.appointment_date != null){
                var appointment_date = moment(response.data.appointment_date).format('DD-MM-YYYY');
            }else if(response.data.telehealth_date != null){
                var appointment_date = moment(response.data.telehealth_date).format('DD-MM-YYYY');
            }
            $('#appointment_date_set').text(appointment_date);

            if(response.data.appointment_time != null){
                var appointment_time = moment(response.data.appointment_time, 'HH:mm:ss').format('HH:mm');
            }else if(response.data.appointment_slot){
                var appointment_time = response.data.appointment_slot;
            }

            if(response.data.location_id != null){
                var appointment_time = moment(response.data.appointment_time, 'HH:mm:ss').format('HH:mm');
            }else if(response.data.appointment_slot){
                var appointment_time = response.data.appointment_slot;
            }
            $('#appointment_time_set').text(appointment_time);

            var firstName = response.data.patient.first_name;
            var middleName  ="";
            if(response.data.patient.middle_name !=null && response.data.patient.middle_name !=""){
            middleName = response.data.patient.middle_name;
            }
            var middleName = middleName;
            var lastName = response.data.patient.last_name;
            var address1 = response.data.patient.address1??'';
            var address2 = response.data.patient.address1??'';
            var zip_code = response.data.patient.zip_code??'';
            var city = response.data.patient.city??'';
            var state = response.data.patient.state??'';
            var county = response.data.patient.county??'';


            var fullName = firstName + ' ' + middleName + ' ' + lastName;
            var fullAddress = address1 + ',' + address2 + ',' + city + ',' + state + ',' + county;

            var dob = '';
            if(response.data.patient.dob){
                dob = moment(response.data.appointment_date).format('DD-MM-YYYY');
            }

            var created_date = '';
            if(response.data.patient.created_date){
                created_date = moment(response.data.created_date).format('DD-MM-YYYY');
            }
            var portal_id_set_url =_VIEW_URL+"/"+response.data.patient_id;
            var location ='';
            if(response.data.appointment_date != null && response.data.location != null){
                var location = response.data.location.address1 + ' ' + response.data.location.address2 + ' ' + response.data.location.city + ' ' + response.data.location.state + ' ' +response.data.location.zip_code;
            }
            $('#full_name_set').text(fullName);
            $('#dob_set').text(dob);
            $('#diciplin_set').text(response.data.patient.diciplin);
            $('#mobile_set').text(response.data.patient.mobile);
            $('#type_set').text(response.data.patient.type);
            $('#email_set').text(response.data.patient.email);
            $('#full_address_set').text(fullAddress);
            $('#ssn_set').text(response.data.patient.ssn);
            $('#created_date_set').text(created_date);
            $('#patient_code_set').text(response.data.patient.patient_code);
            $('#service_name_set').text(response.data.serviceName);
            $('#portal_id_set').html('<a href="'+portal_id_set_url+'">Record #'+response.data.patient_id);
            $('#agency_name_set').text(response.data.patient.agency_detail.agency_name);
            $('#status_set').text(response.data.patient.status);
            $('#appointment_date_new_set').text(appointment_date);
            $('#appointment_time_new_set').text(appointment_time);
            $('#location_set').text(location);
            $('#location_show_div').css('display', 'inline');
            $('#nurse_show_div').css('display', 'none');
            if(location == ''){
                $('#location_set').text('');
                $('#location_show_div').css('display', 'none');
            }
            if(response.data.appointment_schedule_nurse != null && response.data.appointment_schedule_nurse.full_name != null && response.data.appointment_schedule_nurse.full_name != ''){
                $('#nurse_show_div').css('display', 'inline');  
                $('#nurse_set').text(response.data.appointment_schedule_nurse.full_name); 
            }
            // $('.loader-sec').hide();
            hideCalendarShimmer();
            // loadRecentNotes(response.data.patient.id);
        }
    });
}

$(document).ready(function () {
    $('#telehealth_nurse').closest('.form-group').hide();
     $("#fu_date").datepicker({
         inline: true,
                onSelect: function(dateText, inst) {
                    $('#search_day').val('daily')
                    $('#tele_search_day').val('daily')
                    if ($('.appointment-calendar-card').css('display') === 'none'){
                        getTelehealthCalendarData('daily');
                    }else{
                        getAppointmentData('daily');
                    }
                    let current = $('#fu_date').datepicker('getDate');
                    let title = moment(current).format('MMMM YYYY'); // E.g. July 2025
                    $('#calendarHeader').text(title);
                },
            });
});

function refreshData(){
    $('#full_name_set').text('');
    $('#portal_id_set').html('');
    $('#agency_name_set').text('');
    $('#status_set').text('');
    $('#appointment_date_new_set').text('');
    $('#appointment_time_new_set').text('');
    $('#location_set').text('');
    $('#type_set').text('');
    $('#appointment_date_set').text('');
    $('#appointment_time_set').text('');
    $('#service_name_set').text('');
    $('#ssn_set').text('');
    $('#location_show_div').css('display', 'inline');
    $('#nurse_show_div').css('display', 'none');
    $('#nurse_set').text('');
    loadRecentNotes("")
}

var popup = document.getElementById("popup");

popup.style.display = "none";

function openPopup(e,time,month,day) {
    var popup = document.getElementById("popup");
    var panel = document.getElementById("container");

    // Clear previous content
    panel.innerHTML = "";
    // Get the events from the calendar cell
    var events = $('.events_'+time+month).html();
    console.log(events);
    $('#fc-title').html(day+', '+month+' '+time);
    panel.innerHTML += events;
    popup.style.display = "block";
    popup.style.left = (e.pageX-48) + "px";
    popup.style.top = (e.pageY-221) + "px";
}

var closeButton = document.getElementById("fc-close");
closeButton.onclick = function() {
    popup.style.display = "none";
}

window.addEventListener('click', function(event) {
    var openPopupBtn = $(event.target).attr('data-id');
    if (event.target === popup) {
        popup.style.display = "none";
    } else if (!popup.contains(event.target) && openPopupBtn == undefined) {
        popup.style.display = "none";
    }
});

function loadRecentNotes(patient_id=""){
    $.ajax({
        url: _LOAD_RECENT_NOTES,
        type: 'GET',
        data: {
            'patient_id' : patient_id
        },
        success:function(res){
            var json = res.data;
            var htmlResponse ="";
            if(res.data.length !=0){
                $.each(json,function(i,v){
                    var urls =_VIEW_URL+"/"+v.patient_id;
                    var agencyName = "";

                    if(v.user_details.agency_details !=undefined){
                        if(v.user_details !='null'){
                            agencyName = v.user_details?.agency_details.agency_name;
                        }
                    }
                    htmlResponse +=`<div class="d-flex align-items-center py-2 border-bottom">
                            <div class="ml-1">
                                <h6 class="mb-1"><a target="_blank" href="${urls}">Record #${v.patient_id} ${v.patient.first_name+' '+v.patient.last_name}</a></h6>
                                <p style="white-space: pre-wrap;">${v.message}</p>
                                <p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i>${agencyName} ${v.created_date}</p>
                                <div class="row">
                                <p class="text-muted mb-0 tx-12" style="margin-left:12px;">${v.user_details.first_name+' '+v.user_details.last_name}</p>
                                
                                </div>
                                
                            </div>
                            </div>`
                })
            }else{
                htmlResponse +=`<div class="d-flex align-items-center py-2">
                                <div class="ml-1">
                                    <h6 class="mb-1">No record found.</h6>
                                </div>
                            </div>`;
            }

            $('#recent_notes_response').html("");
            $('#recent_notes_response').html(htmlResponse);

        }
    });
    return false;
}
loadRecentNotes();

function getAppointmentSearchData(type){
    $('#search_day').val(type);
    getAppointmentData(type);
}

$(document).ready(function() {
    getAppointmentSearchData('daily');
    
    // Handle calendar type switching
    $('input[name="calendarType"]').change(function() {
        var calendarType = $(this).val();
        // You can add your logic here to switch between appointment and telehealth calendars
        if (calendarType === 'appointment') {
            // Show appointment calendar
            $('#calender_response').removeClass('telehealth-calendar');
            getAppointmentSearchData($('#search_day').val());
            $('.telehealth-calendar-card').attr('style','display:none');
            $('.appointment-calendar-card').attr('style','display:block');
        } else {
            // Show telehealth calendar
            $('#calender_response').addClass('telehealth-calendar');
            // Add your telehealth calendar loading logic here
            getTelehealthCalendarData($('#tele_search_day').val());
            $('.appointment-calendar-card').attr('style','display:none');
            $('.telehealth-calendar-card').attr('style','display:block');
        }
    });
});

// Function to load telehealth calendar data
function getTelehealthCalendarData(type) {
    showTelehealthShimmer();
    $('#tele_calender_response').html("");
    $('.checkBtn').removeClass('next');
        var selectDateVal = $('#fu_date').val(); 
        $('#tele_search_day').val(type);
        if (type == 'daily') {
            $('#teledaily').addClass('next')
            if (selectDateVal) {
                var selectDateVal = $('#fu_date').val();
                var startOfWeek = moment(selectDateVal).format('YYYY-MM-DD')
                var endOfWeek = moment(selectDateVal).format('YYYY-MM-DD')
            } else {
                var startOfWeek = moment().format('YYYY-MM-DD');
                var endOfWeek = moment().format('YYYY-MM-DD');
            }
        } else if (type == 'monthly') {
            $('#telemonthlyId').addClass('next')
            var startOfWeek = moment().startOf('month').toDate();
            startOfWeek = moment(startOfWeek).format('YYYY-MM-DD');
            var endOfWeek = moment().endOf('month').toDate();
            endOfWeek = moment(endOfWeek).format('YYYY-MM-DD');
            if(selectDateVal != ''){
                startOfWeek = moment(selectDateVal).startOf('month').format('YYYY-MM-DD');
                endOfWeek = moment(selectDateVal).endOf('month').format('YYYY-MM-DD');
            } 
        } else {
            $('#teleweeklyId').addClass('next')
            var startOfWeek = moment().startOf('week').toDate();
            startOfWeek = moment(startOfWeek).format('YYYY-MM-DD');
            var endOfWeek = moment().endOf('week').toDate();
            endOfWeek = moment(endOfWeek).format('YYYY-MM-DD');
            if(selectDateVal != ''){
                startOfWeek = moment(selectDateVal).startOf('week').format('YYYY-MM-DD');
                endOfWeek = moment(selectDateVal).endOf('week').format('YYYY-MM-DD');
            } 
        }
        if(type == 'monthly'){
            var location_id = $('#tele_location_id').val();
            var appointemnt_type = $('#tele_appointemnt_type').val();
            getMonthlyTeleAppoitmentData(location_id,type,appointemnt_type,startOfWeek,endOfWeek);
        } 
        else {
            $.ajax({
                url: _GET_TELE_APPOINTMENT_DATA,
                type: 'GET',
                dataType: 'json',
                data: {
                    location_id: $('#tele_location_id').val(),
                    appointemnt_type: $('#tele_appointemnt_type').val(),
                    startOfWeek: startOfWeek,
                    endOfWeek: endOfWeek,
                    type:type,
                    telehealth_nurse: $('#telehealth_nurse').val(),
                },
                success: function (response) {
                    var timeJson = response.data.time;
                    var weekJson = response.data.week;
                    var responseJson = response.data.finalArray;
                    var tableThead = "<thead>";
                    if (timeJson.length != 0) {
                        tableThead = '<th>All-Days</th>';
                    }
    
                    if (weekJson.length != 0) {
                    
                        $.each(weekJson, function (i, v) {
                            tableThead += '<th>' + moment(v).format('dddd') +' (' + moment(v).format('MM/DD')+')</th>';
                        })
                    }
    
                    tableThead += '</thead>';
                    var tableTbody = "<tbody>";
                    var times = [];
                    if (timeJson.length != 0) {
                        $.each(timeJson, function (i, vs) {
                            var responseTDs = [];
                            var responseTD = "";
                            var colorClass = ''; 
                            $.each(weekJson, function (i, vkt) {
                            
                                if (responseJson[vkt][vs] != "") {
                                    
                                    responseTDs[vkt] = [];
                                   
                                    $.each(responseJson[vkt][vs], function (i, rs) {
                                        colorClass = ''; 
                                       
                                        if (rs.patient.status === 'Pending') {
                                            colorClass = 'badge-warning';
                                        }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                                            colorClass = 'badge-info'; 
                                        }else if(rs.patient.status === 'completed'){
                                            colorClass = 'badge-success'; 
                                        }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                                            colorClass = 'badge-danger'; 
                                        } else if(rs.patient.status === 'noshow'){
                                            colorClass = 'badge-light'; 
                                        }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                                            colorClass = 'badge-primary'; 
                                        }else if(rs.patient.status === 'hospitalized/rehab'){
                                            colorClass = 'badge-secondary';
                                        }else {
                                            colorClass = 'badge-dark';
                                        }
    
                                        var tooltipHtml = '<span class="tooltip-text">' + 
                                        rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + '<br/>' + 
                                        (rs.appointment_schedule_nurse != null ? 'Nurse: ' + rs.appointment_schedule_nurse.full_name : '') + 
                                        '</span>';
                                        var data = '<a  class="event_label tooltip-wrapper" onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span><br/>'+tooltipHtml+'</a><br>'
                                        responseTDs[vkt] = (responseTDs[vkt] ? responseTDs[vkt] + ' ' : '') + data;
                                    })
                                } else {
                                    responseTDs[vkt] = '';
                                }
                                responseTD += "<td>" + responseTDs[vkt] +"</td>";
                            });
                            tableTbody += '<tr><td>' + vs + '</td>' + responseTD + '</tr>';
                        
    
                        });
    
                    }
    
                    var tableHtml = "<table class='table table-bordered'>" + tableThead + tableTbody +"</table>";
                    $('#tele_calender_response').html("");
                    $('#tele_calender_response').html(tableHtml)
                    hideTelehealthShimmer();
    
                },
                error: function (xhr, status, error) {
                    console.log(status);
                    console.error('AJAX request failed:', status, error);
                    hideTelehealthShimmer();
                }
            });
        }   
        refreshData();
}

function getMonthlyTeleAppoitmentData(location_id,type,appointemnt_type,startOfWeek,endOfWeek){
    $('#tele_calender_response').html("");
    showTelehealthShimmer();
    month = moment(startOfWeek).format('MMMM');
    prevMonth = moment(startOfWeek).subtract(1, 'months').endOf('month').format('MM');
    prevMonthText = moment(startOfWeek).subtract(1, 'months').endOf('month').format('MMMM');
    prevYear = moment(startOfWeek).subtract(1, 'months').endOf('month').format('YYYY');
    prevMonthFull = moment(startOfWeek).subtract(1, 'months').endOf('month').format('YYYY-MM-DD');
    $.ajax({
        url: _GET_MONTHLY_TELE_APPOINTMENT_DATA,
        type: 'GET',
        dataType: 'json',
        data: {
            location_id: location_id,
            type: type,
            startOfWeek: startOfWeek,
            endOfWeek: endOfWeek,
            appointemnt_type: appointemnt_type,
            telehealth_nurse: $('#telehealth_nurse').val(), 
        },
        success: function (response) {
            var weekJson = response.data.weekDayArray;
            var firstWeek = response.data.firstWeek;
            var weekcount = response.data.firstWeek;
            var responseJson = response.data.monthlyData;
            var previousMonthJson = response.data.previousdata;
            var tableThead = "<thead>";
            
            if (weekJson.length != 0) {
                $.each(weekJson, function (i, v) {
                    tableThead += '<th class="center">' + v +'</th>';
                })
            }
            tableThead += '</thead>';
            var tableTbody = "<tbody>";
            var times = [];
            var responseTD = "<tr>";
            if (responseJson.length != 0) {
                responseTD += "<tr>";
                let lastDayOfPreviousMonth = response.data.previousMonthLastDay;
                for (var i = 0; i < firstWeek; i++) {
                    responseTD += "<td class='top-right'>";
                    responseTD += "<div class='top-right light-gray-color'>"+(lastDayOfPreviousMonth - (weekcount-1))+" ";
                    responseTD += "<div class='row'>";
                    content = '';
                    date = prevYear+'-'+prevMonth+'-'+(lastDayOfPreviousMonth - (weekcount-1));
                    if(previousMonthJson[date].data != null && previousMonthJson[date].data.length != 0){
                        lengthData = previousMonthJson[date].data.length - 5; 
                        $.each(previousMonthJson[date].data, function(i,rs){
                            var data = '';
                            var colorClass = ''; 
                            if (rs.patient.status === 'Pending') {
                                var colorClass = 'badge-warning';
                            }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                                var colorClass = 'badge-info'; 
                            }else if(rs.patient.status === 'completed'){
                                var colorClass = 'badge-success'; 
                            }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                                var colorClass = 'badge-danger'; 
                            } else if(rs.patient.status === 'noshow'){
                                var colorClass = 'badge-light'; 
                            }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                                var colorClass = 'badge-primary'; 
                            }else if(rs.patient.status === 'hospitalized/rehab'){
                                var colorClass = 'badge-secondary';
                            }else {
                                var colorClass = 'badge-dark';
                            } 
                              
                            if(i <= 4){
                                var tooltipHtml = '<span class="tooltip-text">' + 
                                        rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + '<br/>' + 
                                        (rs.appointment_schedule_nurse != null ? 'Nurse: ' + rs.appointment_schedule_nurse.full_name : '') + 
                                        '</span>';
                                data += '<a  class="event_label tooltip-wrapper" onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span><br/>'+tooltipHtml+'</a><br>';
                            }else{
                                if(i==5){
                                    data += '<a class="" data-id="' + previousMonthJson[date]['day']+prevMonthText + '" id="openPopupBtn" onclick="openPopup(event,\'' + (lastDayOfPreviousMonth - (weekcount-1)) + '\',\'' + prevMonthText + '\',\'' + previousMonthJson[date]['weekday'] + '\');"><i class="fa fa-plus-circle" aria-hidden="true"></i> '+lengthData+' more </a>' ;
                                }
                            }
                            content += data;
                        });

                        content += '<div class="events_'+previousMonthJson[date]['day']+prevMonthText
                        +'" style="display:none">';
                        $.each(previousMonthJson[date].data, function(i,rs){
                            var hideContentData = colorClass = ''; 
                            if (rs.patient.status === 'Pending') {
                                var colorClass = 'badge-warning';
                            }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                                var colorClass = 'badge-info'; 
                            }else if(rs.patient.status === 'completed'){
                                var colorClass = 'badge-success'; 
                            }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                                var colorClass = 'badge-danger'; 
                            } else if(rs.patient.status === 'noshow'){
                                var colorClass = 'badge-light'; 
                            }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                                var colorClass = 'badge-primary'; 
                            }else if(rs.patient.status === 'hospitalized/rehab'){
                                var colorClass = 'badge-secondary';
                            }else {
                                var colorClass = 'badge-dark';
                            } 
                            var tooltipHtml = '<span class="tooltip-text">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + '<br/>' + 
                                        (rs.appointment_schedule_nurse != null ? 'Nurse: ' + rs.appointment_schedule_nurse.full_name : '') + 
                                        '</span>';
                            hideContentData += '<a onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span></br>'+tooltipHtml+'</a><br>';

                            content += hideContentData;
                        });
                        content += '</div>';
                        responseTD += content;
                        responseTD += "</div>";
                        responseTD += "</div>";
                    }
                    
                    responseTD += "</td>";
                    weekcount--;
                }
                $.each(responseJson, function (i, vs) {
                    if ((firstWeek + vs['day'] - 1) % 7 == 0 && vs['day'] != 1) {
                        responseTD += "</tr><tr>";
                    }
                    responseTD += "<td class='top-right'>";
                    responseTD += "<div class='top-right'>"+vs['day']+" ";
                    responseTD += "<div class='row'>";
                    content = '';
                    if(vs['data'] != null && vs['data'].length != 0){
                        lengthData = vs['data'].length - 5; 
                        $.each(vs['data'], function(i,rs){
                            var data = '';
                            var colorClass = ''; 
                            if (rs.patient.status === 'Pending') {
                                var colorClass = 'badge-warning';
                            }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                                var colorClass = 'badge-info'; 
                            }else if(rs.patient.status === 'completed'){
                                var colorClass = 'badge-success'; 
                            }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                                var colorClass = 'badge-danger'; 
                            } else if(rs.patient.status === 'noshow'){
                                var colorClass = 'badge-light'; 
                            }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                                var colorClass = 'badge-primary'; 
                            }else if(rs.patient.status === 'hospitalized/rehab'){
                                var colorClass = 'badge-secondary';
                            }else {
                                var colorClass = 'badge-dark';
                            } 
                              
                            if(i <= 4){
                                var tooltipHtml = '<span class="tooltip-text">' + 
                                    rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + '<br/>' + 
                                    (rs.appointment_schedule_nurse != null ? 'Nurse: ' + rs.appointment_schedule_nurse.full_name : '') + 
                                    '</span>';
                                data += '<a  class="event_label tooltip-wrapper" onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span><br/>'+tooltipHtml+'</a><br>';
                            }else{
                                if(i==5){
                                    data += '<a class="" data-id="' + vs['day']+month + '" id="openPopupBtn" onclick="openPopup(event,\'' + vs['day'] + '\',\'' + month + '\',\'' + vs['weekday'] + '\');"><i class="fa fa-plus-circle" aria-hidden="true"></i> '+lengthData+' more </a>' ;
                                }
                            }
                            content += data;
                        });
                    }
                    content += '<div class="events_'+vs['day']+month+'" style="display:none">';
                    $.each(vs['data'], function(i,rs){
                        var hideContentData = colorClass = ''; 
                        if (rs.patient.status === 'Pending') {
                            var colorClass = 'badge-warning';
                        }else if((rs.patient.status === 'booked') || (rs.patient.status === 'processing')){
                            var colorClass = 'badge-info'; 
                        }else if(rs.patient.status === 'completed'){
                            var colorClass = 'badge-success'; 
                        }else if((rs.patient.status === 'cancelled') || (rs.patient.status === 'refused')){
                            var colorClass = 'badge-danger'; 
                        } else if(rs.patient.status === 'noshow'){
                            var colorClass = 'badge-light'; 
                        }else if((rs.patient.status === 'arrived') || (rs.patient.status === 'checkin') || (rs.patient.status === 'not interested') || (rs.patient.status === 'unabletocontact') ){
                            var colorClass = 'badge-primary'; 
                        }else if(rs.patient.status === 'hospitalized/rehab'){
                            var colorClass = 'badge-secondary';
                        }else {
                            var colorClass = 'badge-dark';
                        } 
                        var tooltipHtml = '<span class="tooltip-text">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + '<br/>' + 
                        (rs.appointment_schedule_nurse != null ? 'Nurse: ' + rs.appointment_schedule_nurse.full_name : '') + 
                        '</span>';
                        hideContentData += '<a onclick="getAppointmentDetails(' + rs.id + ')"><span class="badge ' + colorClass + ' ">' + rs.appointment_times + ' ' + rs.patient.first_name + '-' + rs.patient.type + ' ' + '</span><br/>'+tooltipHtml+'</a><br>';

                        content += hideContentData;
                    });
                    content += '</div>';
                    responseTD += content;
                    responseTD += "</div>";
                    responseTD += "</div>";
                    responseTD += "</td>";
                });

                tableTbody += responseTD;
            }
            var tableHtml = "<table class='table table-bordered'>" + tableThead + tableTbody +"</table>";
            $('#tele_calender_response').html("");
            $('#tele_calender_response').html(tableHtml);
            hideTelehealthShimmer();

            // loadRecentNotes(response.data.patient.id);
        },
        error: function (xhr, status, error) {
            console.log(status);
            console.error('AJAX request failed:', status, error);
        }
    });
}

function getTelehealthCalendarSearchData(){
    let type = $('#tele_search_day').val();
    getTelehealthCalendarData(type);
    let appType = $('#tele_appointemnt_type').val();
    $('#telehealth_nurse').closest('.form-group').hide();
    if(appType == 'patient'){
        $('#telehealth_nurse').closest('.form-group').show();
    }
}

// Function to show shimmer loader
function showTelehealthShimmer() {
    document.querySelector('.shimmer-calender-loader').style.display = 'block';
}

// Function to hide shimmer loader
function hideTelehealthShimmer() {
    document.querySelector('.shimmer-calender-loader').style.display = 'none';
}

function showCalendarShimmer() {
    document.querySelector('.shimmer-loader-schedule').style.display = 'block';
}

// Function to hide shimmer loader
function hideCalendarShimmer() {
    document.querySelector('.shimmer-loader-schedule').style.display = 'none';
}

$('#tele_next_btn').click(function(){
    var selectDateVal = $('#fu_date').val();
    var currentDate = moment(selectDateVal);
    let type = $('#tele_search_day').val();
    if (type === 'daily') {
        nextDate = currentDate.clone().add(1, 'days');
    } else if (type === 'weekly') {
        nextDate = currentDate.clone().add(1, 'weeks');
    } else if (type === 'monthly') {
        nextDate = currentDate.clone().add(1, 'months');
    }
    nextDate = nextDate.format('MM/DD/YYYY');
    $('#fu_date').val(nextDate);
    $('#fu_date').datepicker('setDate', nextDate);
    getTelehealthCalendarData(type);
    let current = $('#fu_date').datepicker('getDate');
    let title = moment(current).format('MMMM YYYY'); // E.g. July 2025
    $('#calendarHeader').text(title);
    console.log(nextDate);
});

$('#tele_prev_btn').click(function(){
    var selectDateVal = $('#fu_date').val();
    var currentDate = moment(selectDateVal);
    let type = $('#tele_search_day').val();
    if (type === 'daily') {
        prevDate = currentDate.clone().subtract(1, 'days');
    } else if (type === 'weekly') {
        prevDate = currentDate.clone().subtract(1, 'weeks');
    } else if (type === 'monthly') {
        prevDate = currentDate.clone().subtract(1, 'months');
    }
    prevDate = prevDate.format('MM/DD/YYYY');
    console.log(prevDate);
    $('#fu_date').val(prevDate);
    $('#fu_date').datepicker('setDate', prevDate);
    let current = $('#fu_date').datepicker('getDate');
    let title = moment(current).format('MMMM YYYY'); // E.g. July 2025
    $('#calendarHeader').text(title);
    getTelehealthCalendarData(type);
});
