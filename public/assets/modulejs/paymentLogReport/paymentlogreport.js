paymentLogReport(1);
function paymentLogReport(page=1){
    $('#payment_log_reponse_id').html("")
    $('.hideClass').removeClass('d-none');
    var agency_fk = $('#agency_fk').val();
    var portal_id = $('#patient_id').val();
    var status_payment_type = $('#status_payment_type').val();
    var status_location_id = $('#status_location_id').val();
    var status_insurance_id = $('#status_insurance_id').val();
    var services = $('#services').val();
    $.ajax({
        url: _PAYMENT_LOG_LIST+"?page=" + page,
        type: "get",
        data:{
            'portal_id':portal_id,
            'status_payment_type':status_payment_type,
            'status_location_id':status_location_id,
            'status_insurance_id':status_insurance_id,
            'agency_fk':agency_fk,
            'services':services,
            'created_date' : $('#created_date').val(),
        },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            $('#payment_log_reponse_id').html("")
            $('#payment_log_reponse_id').html(response);
        }
    });
}

$(document).on('click', '.payment_log_paginate .pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1]; 
    paymentLogReport(page);
});

function refresh(){
    $('#patient_id').val('') 
    $('#agency_fk').val('').trigger("change")
    $('#status_payment_type').val('').trigger("change")
    $('#status_location_id').val('').trigger("change")
    $('#status_insurance_id').val('').trigger("change")
    $('#services').val('').trigger("change");
    $('#created_date').val('').trigger("change");
    paymentLogReport(1);
}

function exportCsv()
{
    $('.hideClass').removeClass('d-none');
    $.ajax({
        url: _PAYMENT_LOG_CSV,
        type: "get",
       data:{
        'portal_id':$('#patient_id').val(),
        'agency_fk':$('#agency_fk').val(),
        'status_payment_type' : $('#status_payment_type').val(),
        'status_location_id' : $('#status_location_id').val(),
        'services' : $('#services').val(),
        'created_date' : $('#created_date').val(),
       },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            var blob = new Blob([response]);
            console.log(response);
            if(response == ""){
                toastr.error('Please check there is no data to export.');
            }else{
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                var form_name = "payment_log_"+_DATE_TIME;
                link.download = form_name + ".csv";
                link.click();
            }
            
        }
    });
}

// Open Sidebar
function openSidebar() {
    document.getElementById('logSidebar').classList.add('show');
    document.getElementById('overlay').classList.add('show');
}

// Close Sidebar
function closeSidebar() {
    document.getElementById('logSidebar').classList.remove('show');
    document.getElementById('overlay').classList.remove('show');
}

function showLogDetails(rowId) {
    $('#logContent').html("");
    $.ajax({
        type: "GET",
        url: _GENARARE_PAYMENT_HISTORY,
        data: {
            "id": rowId,
        },
        success: function (res) {
            details = res.data;
            if (details) {
                let totalAmount = 0;
                let totalReceived = 0;
                let totalRemaining = 0;
                let logHtml = `<div class="details"><h6><strong>Basic Details:</strong></h6>
                    <p><strong>Payment Type:</strong> ${details.payment_type}</p>
                    <p><strong>Portal Id:</strong> <a href="${PATIENT_URL}/${details.patient_id}" target="_blank">${details.patient_id}</a></p>
                    <p><strong>Portal name:</strong> ${details.patient_name}</p>
                    <p><strong>Agency name:</strong> ${details.agency_name}</p>
                    <p><strong>Created Date:</strong> ${moment(details.created_at).format('MM/DD/YYYY hh:mm A')}</p>
                    <p class="mb-3"><strong>Created By:</strong> ${details.created_by}</p></div>`;

                    if((details.paymentLogData.length != 0)){
                        logHtml += `<h6><strong>Service Amount Breakdown:</strong></h6>
                        <table class="table table-bordered">
                            <thead>
                                <th>Service Name</th>
                                <th>Price</th>
                                <th>Received Amount</th>
                                <th>Remaining Amount</th>
                            </thead>
                        `;

                        details.paymentLogData.forEach(log => {
                            totalAmount += parseFloat(log.total_amount);
                            totalReceived += parseFloat(log.received_amount);
                            totalRemaining += parseFloat(log.remaining_amount);
                            if(details.payment_type == 'Caregiver Pay'){
                                totalAmountTxt = formatUSD(log.total_amount);
                            }else{
                                totalAmountTxt = 'N/A';
                            }
                            logHtml += `
                                    <tr>
                                        <td>${log.service_name}</td>
                                        <td>${totalAmountTxt}</td>
                                        <td>${formatUSD(log.received_amount)}</td>
                                        <td>${formatUSD(log.remaining_amount)}</td>
                                    </tr>
                            `;
                        });
                        
                        if(details.payment_type == 'Caregiver Pay'){
                            logHtml += `<tr style="background-color: #cfcfcf;border: 1px solid #ccc;"><td>Total</td><td><b>${formatUSD(totalAmount)}</b></td><td><b>${formatUSD(totalReceived)}</b></td><td><b>${formatUSD(totalRemaining)}</b></td></table>`;
                        }else{
                            logHtml += `<tr style="background-color: #cfcfcf;border: 1px solid #ccc;"><td colspan="2">Total</td><td><b>${formatUSD(totalReceived)}</b></td><td><b>${formatUSD(totalRemaining)}<b></td></table>`;
                        }
                        logHtml += `</table>`;
                    }
                logHtml += `<strong><h6 class="mt-3">Amount Summary:</strong></h6>
                            <div class="amount-summary mt-0">
                                <p><strong>Total Amount:</strong> ${formatUSD(totalAmount)}</p>
                                <p style="color: green;"><strong>Total Received Amount:</strong> ${formatUSD(totalReceived)}</p>
                                <p style="color: red;"><strong>Total Remaining Amount:</strong> ${formatUSD(totalRemaining)}</p>
                            </div>`;
                $('#logContent').html(logHtml);
                openSidebar(); // Show Sidebar        
            } else {
                $('#logContent').html('<p>No details available.</p>');
            }
        }
    });
}

function showLogDetails(rowId) {
    $('#logContent').html("");
    $.ajax({
        type: "GET",
        url: _GENARARE_PAYMENT_HISTORY,
        data: {
            "id": rowId,
        },
        success: function (res) {
            details = res.data;
            if (details) {
                let totalAmount = 0;
                let totalReceived = 0;
                let totalRemaining = 0;

                let logHtml = `<div class="section-ui">
                                    <h4><i class="mdi mdi-information"></i> Basic Information</h4>
                                    <ul>
                                        <li><strong>Payment Type:</strong> ${details.payment_type}</li>
                                        <li><strong>Portal Id:</strong> <a href="${PATIENT_URL}/${details.patient_id}" target="_blank">${details.patient_id}</a></li>
                                        <li><strong>Portal name:</strong> ${details.patient_name}</li>
                                        <li><strong>Agency name:</strong> ${details.agency_name}</li>
                                        <li><strong>Created Date:</strong> ${moment(details.created_at).format('MM/DD/YYYY hh:mm A')}</li>
                                        <li><strong>Created By:</strong> ${details.created_by}</li>
                                    </ul>
                                </div>`;

                // logHtml += `<div class="details"><h6><strong>Basic Information:</strong></h6>
                //     <p><strong>Payment Type:</strong> ${details.payment_type}</p>
                //     <p><strong>Created Date:</strong> ${moment(details.created_at).format('MM/DD/YYYY hh:mm A')}</p>
                //     <p class="mb-3"><strong>Created By:</strong> ${details.created_by}</p></div>`;
                if((details.paymentLogData.length != 0)){
                    logHtml += `<div class="section-ui"><h4><i class="mdi mdi-settings"></i> Service Breakdown</h4>
                        <table class="table table-bordered">
                            <thead>
                                <th>Service Name</th>
                                <th>Price</th>
                                <th>Received Amount</th>
                                <th>Remaining Amount</th>
                                </thead>
                    `;

                    details.paymentLogData.forEach(log => {
                        totalAmount += parseFloat(log.total_amount);
                        totalReceived += parseFloat(log.received_amount);
                        totalRemaining += parseFloat(log.remaining_amount);
                        if(details.payment_type == 'Caregiver Pay'){
                            totalAmountTxt = formatUSD(log.total_amount);
                        }else{
                            totalAmountTxt = 'N/A';
                        }
                        logHtml += `
                                <tr>
                                    <td>${log.service_name}</td>
                                    <td>${totalAmountTxt}</td>
                                    <td>${formatUSD(log.received_amount)}</td>
                                    <td>${formatUSD(log.remaining_amount)}</td>
                                </tr>
                        `;
                    });
                    if(details.payment_type == 'Caregiver Pay'){
                        logHtml += `<tr style="background-color: #cfcfcf;border: 1px solid #ccc;"><td>Total</td><td><b>${formatUSD(totalAmount)}</b></td><td><b>${formatUSD(totalReceived)}</b></td><td><b>${formatUSD(totalRemaining)}</b></td></table>`;
                    }else{
                        logHtml += `<tr style="background-color: #cfcfcf;border: 1px solid #ccc;"><td colspan="2">Total</td><td><b>${formatUSD(totalReceived)}</b></td><td><b>${formatUSD(totalRemaining)}<b></td></table>`;
                    }
                }    
                    
                logHtml += `</div><div class="section-ui"><h4><i class="mdi mdi-wallet"></i> Payment Summary</h4>
                            
                                <ul>
                                    <li><strong><i class="mdi mdi-calculator" style="color:rgb(19, 112, 235); font-size: 17px;"></i> Total Amount:</strong> <span style="color:rgb(19, 112, 235);">${formatUSD(totalAmount)}</span> </li>
                                    <li><i class="mdi mdi-check-circle" style="color: green;"></i> <strong>Received Amount:</strong><span style="color:green;"> ${formatUSD(totalReceived)} </span></li>
                                    <li><i class="mdi mdi-close-circle" style="color: red;"></i> <strong>Remaining Amount:</strong> <span style="color:red;">${formatUSD(totalRemaining)} </span></li>
                                </ul>
                            </div>`;
                $('#logContent').html(logHtml);
                openSidebar(); // Show Sidebar        
            } else {
                $('#logContent').html('<p>No details available.</p>');
            }
        }
    });
}
function formatUSD(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}
$(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#created_date').daterangepicker({
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
    }, function(chosen_date, end_date) {

        $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
});

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});