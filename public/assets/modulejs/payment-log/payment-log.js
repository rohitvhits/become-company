serviceReqList();
getService();
function getPaymentData(page = 1) {
    $('.paymentLoader').attr('style', '');
    $("#payment_response_id").html("");
    $('.payment_table_tab').attr('style', 'display:none');
    $.ajax({
        url: _PAYMENT_LIST + "?page=" + page,
        type: "get",
        data: {
            'id': _RECORD_ID
        },
        success: function (response) {
            $('.paymentLoader').attr('style', 'display:none');
            $('.payment_table_tab').attr('style', '');
            $('#payment_response_id').html("")
            $('#payment_response_id').html(response);
        }
    });
}

$(document).on('click', '.payment_paginate .pagination a', function (e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    getPaymentData(page);
});


function exportCsv() {
    $('.hideClass').removeClass('d-none');
    $.ajax({
        url: _PAYMENT_CSV,
        type: "get",
        data: {
            'id': _RECORD_ID
        },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            var blob = new Blob([response]);
            if (response == "") {
                toastr.error('Please check there is no data to export.');
            } else {
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                var form_name = "payment_" + _DATE_TIME;
                link.download = form_name + ".csv";
                link.click();
            }

        }
    });
}

function editData(id) {
    $('.amount_document_div').html("");
    $('.edit_amount_document_div').html("");
    $('#add_status_payment_type').val('').change();
    $('#add_status_location_id').val('').change();
    $('#pay_request_service_id').val('').change();
    $('#pay_service_id').val('').change();
    $('#add_status_location_error').html('');
    $('#add_payment_type_error').html('');
    $('#status_service_error').html('');
    $('.amount_document_div').html("");
    $('.edit_amount_document_div').html("");
    $('#edit_payment_type_error').html("");
    $('#edit_status_location_error').html("");
    $('#edit_service_error').html("");
    $('#edit_service_req_error').html("");
    $("#edit_pay_service_id").val('').change();
    $.ajax({
        url: PAYMENT_PAY_URL + '/' + id,
        type: "get",
        success: function (response) {
            if (response.data != "") {
                $('#edit_payment_id').val(id);
                $('#edit_status_payment_type option[value="' + response.data.payment_type + '"]').prop('selected', true);
                $('#edit_status_location_id option[value="' + response.data.location_id + '"]').prop('selected', true);
                serviceReqList('edit', response.data.service_requested_id);
                var payHtml = '';
                if (response.data.payment_log_deatil != '') {
                    payHtml += `<h5> Payment Details</h5><table id="payTable" class="table table-bordered"><thead><tr><th>No</th><th>Services</th>
                                                <th>Service Amount(USD)</th>
                                                <th>Received Amount(USD)</th>
                                                <th>Remaining Amount(USD)</th>
                                            </tr></thead>
                                        <tbody>`;
                    amountVal = 0;
                    service_id_array = [];
                    countKey = 0;
                    if (response.data.payment_type == '866') {
                        $.each(response.data.payment_log_deatil, function (key, value) {
                            amount = value.total_amount??0.00
                            var totalAmount = parseFloat(amount).toFixed(2);
                            amountVal += parseFloat(totalAmount);
                            service_id_array.push(value.service_details.id);
                            payHtml += ` <tr>
                                            <td>${countKey + 1}</td>
                                            <td data-name="${value.service_details.id}">${value.service_details.name}</td>
                                            <td id="total_amount_${value.service_details.id}" data-amount="${amount}">${formatUSD(totalAmount)}</td>
                                            <td id="received_amount_${value.service_details.id}" data-reciveamount="${value.received_amount}">
                                                <input type="text" class="form-control pastecls" name="received_amount[]" id="received_amount${value.service_details.id}" onchange="setRemainingAmt(${value.service_details.id},${amount})" value="${value.received_amount}"><span id="payment_remain_error_${value.service_details.id}" class="error mt-2"></span>
                                            </td>
                                            <td id="remaining_amount_${value.service_details.id}" data-remainamount ="${value.remaining_amount}" value="${value.remaining_amount}">${formatUSD(value.remaining_amount)}</td>
                                        </tr>`;     
                                        countKey++;  
                        });
                    }else{
                        $.each(response.data.payment_log_deatil, function (key, value) {
                            amount = value.total_amount??0.00
                            var totalAmount = parseFloat(amount).toFixed(2);
                            amountVal += parseFloat(totalAmount);
                            service_id_array.push(value.service_details.id);
                            payHtml += ` <tr>
                                            <td>${countKey + 1}</td>
                                            <td data-name="${value.service_details.id}">${value.service_details.name}</td>
                                            <td id="total_amount_${value.service_details.id}" data-amount="${amount}">N/A</td>
                                            <td id="received_amount_${value.service_details.id}" data-reciveamount="${value.received_amount}">
                                                <input type="text" class="form-control pastecls" name="received_amount[]" id="received_amount${value.service_details.id}" onchange="setRemainingAmt(${value.service_details.id},${amount})" value="${value.received_amount}"><span id="payment_remain_error_${value.service_details.id}" class="error mt-2"></span>
                                            </td>
                                            <td id="remaining_amount_${value.service_details.id}" data-remainamount ="${value.remaining_amount}" value="${value.remaining_amount}">${formatUSD(value.remaining_amount)}</td>
                                        </tr>`;     
                                        countKey++;  
                        });
                    }
                    payHtml += `</tbody> </table>`;
                    $("#edit_pay_service_id").val('').change();
                    getService('edit',service_id_array);
                    $('.edit_amount_document_div').html(payHtml);
                    addTotalRow(amountVal);
                }
            }
        }
    });
}

function editHandlePaymentChange(select) {
    $('.edit_amount_document_div').html("")
    showInvoiceDetails('edit');
    // getService('edit');
}

$('#edit_payment').on('click', function () {
    editcnt = 0;
    var payment_type = $('#edit_status_payment_type').val();
    var location = $('#edit_status_location_id').val();
    var payment_id = $('#edit_payment_id').val();
    var pay_request_service_id = $('#edit_pay_request_service_id').val();
    $('#edit_payment_type_error').html("");
    $('#edit_status_location_error').html("");
    $('#edit_service_error').html("");
    $('#edit_service_req_error').html("");

    if (payment_type.trim() == '') {
        $('#edit_payment_type_error').html("Please select Payment type");
        editcnt++;
    }
    if (location.trim() == '') {
        $('#edit_status_location_error').html("Please select Location");
        editcnt++;
    }

    if (pay_request_service_id == '') {
        $('#edit_service_req_error').html("Please select Service Request");
        editcnt++;
    }

    var pay_service_id = $('#edit_pay_service_id').val()
    if (pay_service_id == '') {
        $('#edit_service_error').html("Please select Service");
        editcnt++;
    }

    $('#edit-payment-data td[data-name]').each(function () {
        var service_id = $(this).attr('data-name');
        var receivedAmount = $("#received_amount"+service_id).val();
        var totalAmount = $("#total_amount_"+service_id).attr('data-amount');
        if(receivedAmount == ""){
            $('#payment_remain_error_' + service_id).html('Please enter Received Amount');
            editcnt++;
        }
        if(receivedAmount == ".") {
            $('#payment_remain_error_' + service_id).html('Please enter valid Received Amount.');
        } 
        if (payment_type == '866' ){
            if(parseFloat(totalAmount) < parseFloat(receivedAmount)){
                $('#payment_remain_error_' + service_id).html('Ensured Received Amount cannot exceed Total Service Amount.');
                editcnt++;
            }
        }
    });

    if (editcnt > 0) {
        return false;
    } else {
        // if (payment_type == '866') {
            var payment_service = {
                service_id: [],
                service_amount: [],
                recive_amount: [],
                remain_amount: [],
            };
            $('#payTable tbody tr td').each(function () {
                if ($(this).attr('data-name') != undefined) {
                    payment_service['service_id'].push($(this).attr('data-name'));
                }
                if ($(this).attr('data-amount') != undefined) {
                    payment_service['service_amount'].push($(this).attr('data-amount'));
                }
                if ($(this).attr('data-reciveamount') != undefined) {
                    payment_service['recive_amount'].push($(this).attr('data-reciveamount'));
                }
                if ($(this).attr('data-remainamount') != undefined) {
                    payment_service['remain_amount'].push($(this).attr('data-remainamount'));
                }
            });
        // }
        $.confirm({
            title: 'Confirmation',
            columnClass: "col-md-6",
            content: 'Are you sure you want to change the payment details?',
            buttons: {
                formSubmit: {
                    text: 'Yes',
                    btnClass: 'btn-primary',
                    action: function () {
                        $.ajax({
                            async: false,
                            global: false,
                            url: _CHANGE_PAYMENT_DETAILS,
                            type: "POST",
                            data: {
                                '_token': _CSRF_TOKEN,
                                'patient_id': _RECORD_ID,
                                'id': payment_id,
                                'payment_type': payment_type,
                                'location': location,
                                'service_requested_id': pay_request_service_id,
                                'payment_service': payment_service ?? '',
                            },

                            success: function (response) {
                                toastr.success(response.error_msg);
                                $('#edit-payment-data').click();
                                $(".edit_amount_document_div").html("");
                                if (response.status == 1) {
                                    editcnt = 0;
                                    getPaymentData();
                                }
                            },
                            error: function (jqr) {
                                toastr.error(jqr.responseJSON.error_msg);
                            },
                        });
                    }
                },
                cancel: {
                    'text': 'No'
                },
            }
        });
    }
});

$('#add_payment').on('click', function () {
    addcnt = 0;
    var payment_type = $('#add_status_payment_type').val();
    var location = $('#add_status_location_id').val();
    var pay_request_service_id = $('#pay_request_service_id').val();
    var pay_service_id = $('#pay_service_id').val();
    $('#add_payment_type_error').html("");
    $('#add_status_location_error').html("");
    $('#add_service_req_error').html("");
    $('#add_service_error').html("");

    if (payment_type == '') {
        $('#add_payment_type_error').html("Please select Payment type");
        addcnt++;
    }
    if (location == '') {
        $('#add_status_location_error').html("Please select Location");
        addcnt++;
    }
    if (pay_request_service_id == '') {
        $('#add_service_req_error').html("Please select Service Request");
        addcnt++;
    }

    if (pay_service_id == '') {
        $('#add_service_error').html("Please select Service");
        addcnt++;
    }

    
       
        $('#add-payment-data td[data-name]').each(function () {
            var service_id = $(this).attr('data-name');
            var receivedAmount = $("#received_amount"+service_id).val();
            var totalAmount = $("#total_amount_"+service_id).attr('data-amount');
            if(receivedAmount == ""){
                $('#payment_remain_error_' + service_id).html('Please enter Received Amount');
                addcnt++;
            }
            if(receivedAmount == ".") {
                $('#payment_remain_error_' + service_id).html('Please enter valid Received Amount.');
            } 
            if (payment_type == '866' ){
                if(parseFloat(totalAmount) < parseFloat(receivedAmount)){
                    $('#payment_remain_error_' + service_id).html('Ensured Received Amount cannot exceed Total Service Amount.');
                    addcnt++;
                }
            }
        });
   
    if (addcnt > 0) {
        return false;
    } else {
        //get Payment details data
        // if (payment_type == '866') {
            var payment_service = {
                service_id: [],
                service_amount: [],
                recive_amount: [],
                remain_amount: [],
            };
            $('#payTable tbody tr td').each(function () {
                if ($(this).attr('data-name') != undefined) {
                    payment_service['service_id'].push($(this).attr('data-name'));
                }
                if ($(this).attr('data-amount') != undefined) {
                    payment_service['service_amount'].push($(this).attr('data-amount'));
                }
                if ($(this).attr('data-reciveamount') != undefined) {
                    payment_service['recive_amount'].push($(this).attr('data-reciveamount'));
                }
                if ($(this).attr('data-remainamount') != undefined) {
                    payment_service['remain_amount'].push($(this).attr('data-remainamount'));
                }
            });
        // }
        // return false;
        $.ajax({
            async: false,
            global: false,
            url: _ADD_PAYMENT_DETAILS,
            type: "POST",
            data: {
                '_token': _CSRF_TOKEN,
                'patient_id': _RECORD_ID,
                'payment_type': payment_type,
                'location': location,
                'service_requested_id': pay_request_service_id,
                'payment_service': payment_service ?? '',
            },

            success: function (response) {
                toastr.success(response.error_msg);
                $('#add-payment-data').click();
                if (response.status == 1) {
                    getPaymentData();
                    $(".amount_document_div").html("");
                }
            },
            error: function (jqr) {
                toastr.error(jqr.responseJSON.error_msg);
            },
        });
    }
});

function addHandlePaymentChange(select) {
    $('.amount_document_div').html("")
    showInvoiceDetails();
}

function serviceReqList(type = "", service_req_id = "") {
    var jsonencode = [];
    $.ajax({
        type: "GET",
        url: _PATIENT_REQUEST_SERVICES,
        data: {
            "id": _RECORD_ID,
            "jsonencode": jsonencode
        },
        success: function (res) {
            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp = '<option value="">No record available</option>';
            }

            if (type == "edit") {
                $('#edit_pay_request_service_id').html(htmlsresp);
                // $('#edit_pay_request_service_id').val(service_req_id).change();
                $('#edit_pay_request_service_id option[value="' + service_req_id + '"]').prop('selected', true);
            } else {
                $('#pay_request_service_id').html(htmlsresp);
            }

        }
    })
}

function showInvoiceDetails(type="") {
    payHtml = '';
    if(type == 'edit'){
        var pay_service_id = $('#edit_pay_service_id').val();
        paymentType = $('#edit_status_payment_type').val();
        idSelect = 'edit_pay_service_id';
    }else{
        var pay_service_id = $('#pay_service_id').val();
        paymentType = $('#add_status_payment_type').val();
        idSelect = 'pay_service_id';
    }
    $('.edit_amount_document_div').html("");
    $('.amount_document_div').html("");
    if (pay_service_id != "" && paymentType != "") {
        if(paymentType == '866'){
            $.ajax({
                type: "GET",
                url: _GENARARE_AMOUNT_DETAILS,
                data: {
                    "agency_id": _AGENCYID,
                    "pay_service_id": pay_service_id
                },
                success: function (res) {
                    if (res.status === 0 && res.data) {
                        payHtml += `<h5> Payment Details</h5><table id="payTable" class="table table-bordered"><thead><tr><th>No</th><th>Services</th>
                                                    <th>Service Amount(USD)</th>
                                                    <th>Received Amount(USD)</th>
                                                    <th>Remaining Amount(USD)</th>
                                                </tr></thead>
                                            <tbody>`;
                        amountVal = 0;
                        countKey = 0;
                        $.each(res.data, function (key, value) {
                            amount  = value.amount??0.00;
                            var totalAmount = parseFloat(amount).toFixed(2);
                            amountVal += parseFloat(totalAmount);
                            
                            payHtml += ` <tr>
                                            <td>${countKey + 1}</td>
                                            <td data-name="${value.service_id}">${value.name}</td>
                                            <td id="total_amount_${value.service_id}" data-amount="${amount}">${formatUSD(totalAmount)}</td>
                                            <td id="received_amount_${value.service_id}"><input type="text" class="form-control pastecls" name="received_amount[]" id="received_amount${value.service_id}" onchange="setRemainingAmt(${value.service_id},${amount})"><span id="payment_remain_error_${value.service_id}" class="error mt-2"></span></td>
                                            <td id="remaining_amount_${value.service_id}"></td>
                                        </tr>`;
                                        countKey++;
                        });
                        payHtml += `</tbody> </table>`;
                    }
                    if(type=='edit'){
                        $('.edit_amount_document_div').html(payHtml);
                    }else{
                        $('.amount_document_div').html(payHtml);
                    }
                    addTotalRow(amountVal);
                }
            })
        }else if(pay_service_id != ""){
            payHtml += `<h5> Payment Details</h5><table id="payTable" class="table table-bordered"><thead><tr><th>No</th><th>Services</th>
                        <th>Service Amount(USD)</th>
                        <th>Received Amount(USD)</th>
                        <th>Remaining Amount(USD)</th>
                    </tr></thead>
                <tbody>`;
            amountVal = 0;
            countKey = 0;
            $.each(pay_service_id, function (key, value) {
                console.log(pay_service_id);
                amount  = 0.00;
                totalAmount = 'N/A';
                amountVal += 0.00;
                var service_name = $('#'+idSelect+' option[value="'+value+'"]').text();
                payHtml += ` <tr>
                    <td>${countKey + 1}</td>
                    <td data-name="${value}">${service_name}</td>
                    <td id="total_amount_${value}" data-amount="${amount}">${totalAmount}</td>
                    <td id="received_amount_${value}"><input type="text" class="form-control pastecls" name="received_amount[]" id="received_amount${value}" onchange="setRemainingAmt(${value},${amount})"><span id="payment_remain_error_${value}" class="error mt-2"></span></td>
                    <td id="remaining_amount_${value}"></td>
                </tr>`;
                countKey++;
            });
            payHtml += `</tbody> </table>`;
            if(type=='edit'){
                $('.edit_amount_document_div').html(payHtml);
            }else{
                $('.amount_document_div').html(payHtml);
            }
            addTotalRow(amountVal);
        }
    }

}


function addTotalRow(amountVal) {
    var receivedAmt = 0;
    var remainingAmt = 0;
    var totAmount = 0;
    $('#payTable tbody tr td').each(function () {
        if ($(this).attr('data-amount') != undefined) {
            var totAmt = parseFloat($(this).attr('data-amount')).toFixed(2);
            totAmount += parseFloat(totAmt);
        }
        if ($(this).attr('data-reciveamount') != undefined) {
            var recAmount = parseFloat($(this).attr('data-reciveamount')).toFixed(2);
            receivedAmt += parseFloat(recAmount);
        }
        if ($(this).attr('data-remainamount') != undefined) {
            var remAmount = parseFloat($(this).attr('data-remainamount')).toFixed(2);
            remainingAmt += parseFloat(remAmount);
        }
    });
    if($('#add_status_payment_type').val() == '866' || $('#edit_status_payment_type').val() == '866'){
        var totalRow = `
        <tr class="table-success">
            <td colspan="2" style="text-align:right"><strong>Total</strong></td>
            <td id="total_sum"><strong>${formatUSD(totAmount.toFixed(2))}</strong></td>
            <td id="total_received_sum"><strong>${formatUSD(receivedAmt.toFixed(2))}</strong></td>
            <td id="total_remaining_sum"><strong>${formatUSD(remainingAmt.toFixed(2))}</strong></td>
        </tr>`;
    }else{
        var totalRow = `
        <tr class="table-success">
            <td colspan="3" style="text-align:right"><strong>Total</strong></td>
            <td id="total_received_sum"><strong>${formatUSD(receivedAmt.toFixed(2))}</strong></td>
            <td id="total_remaining_sum"><strong>${formatUSD(remainingAmt.toFixed(2))}</strong></td>
        </tr>`;
    }
   

    // Append the total row after all rows
    $('#payTable tbody .table-success').html("");
    $('#payTable tbody').append(totalRow);
}

function formatUSD(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}


function setRemainingAmt(service_id, totalServiceamt) {
    var received_amt = $('#received_amount' + service_id).val();
    $('#payment_remain_error_' + service_id).html('');
    $('#remaining_amount_' + service_id).val('');
    $('#remaining_amount_' + service_id).html('');
    $('#remaining_amount_' + service_id).attr('data-remainamount', '0.00');
    $('#received_amount_' + service_id).attr('data-reciveamount', '0.00');
    if (received_amt == "") {
        $('#payment_remain_error_' + service_id).html('Please enter Received Amount.');
    } else if(received_amt == ".") {
        $('#payment_remain_error_' + service_id).html('Please enter valid Received Amount.');
    } else {
        if($('#edit_status_payment_type').val() == '866' || $('#add_status_payment_type').val() == '866'){
            if (parseFloat(received_amt) > parseFloat(totalServiceamt)) {
                $('#payment_remain_error_' + service_id).html('Ensured Received Amount cannot exceed Total Service Amount.');
            } else {
                var remaining_amt = totalServiceamt - received_amt;
                $('#remaining_amount_' + service_id).val(remaining_amt);
                $('#remaining_amount_' + service_id).html(formatUSD(remaining_amt));
                $('#remaining_amount_' + service_id).attr('data-remainamount', remaining_amt);
                $('#received_amount_' + service_id).attr('data-reciveamount', received_amt);
                addTotalRow(totalServiceamt);
            }
        }else{
            var remaining_amt = 0;
            $('#remaining_amount_' + service_id).val(remaining_amt);
            $('#remaining_amount_' + service_id).html(formatUSD(remaining_amt));
            $('#remaining_amount_' + service_id).attr('data-remainamount', remaining_amt);
            $('#received_amount_' + service_id).attr('data-reciveamount', received_amt);
            addTotalRow(totalServiceamt);
        }
        
    }
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

                let logHtml = `<div class="section">
                                    <h4><i class="mdi mdi-information"></i> Basic Information</h4>
                                    <ul>
                                        <li><strong>Payment Type:</strong> ${details.payment_type}</li>
                                        <li><strong>Created:</strong> ${moment(details.created_at).format('MM/DD/YYYY hh:mm A')} </li>
                                        <li><strong>Created By:</strong> ${details.created_by} </li>
                                    </ul>
                                </div>`;

                // logHtml += `<div class="details"><h6><strong>Basic Information:</strong></h6>
                //     <p><strong>Payment Type:</strong> ${details.payment_type}</p>
                //     <p><strong>Created Date:</strong> ${moment(details.created_at).format('MM/DD/YYYY hh:mm A')}</p>
                //     <p class="mb-3"><strong>Created By:</strong> ${details.created_by}</p></div>`;
                if((details.paymentLogData.length != 0)){
                    logHtml += `<div class="section"><h4><i class="mdi mdi-settings"></i> Service Breakdown</h4>
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
                    
                logHtml += `</div><div class="section"><h4><i class="mdi mdi-wallet"></i> Payment Summary</h4>
                            
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

$('#add_payment_data').on('click', function () {
    $('#add_status_payment_type').val('').change();
    $('#add_status_location_id').val('').change();
    $('#pay_request_service_id').val('').change();
    $('#pay_service_id').val('').change();
    $('#add_status_location_error').html('');
    $('#add_payment_type_error').html('');
    $('#status_service_error').html('');
    $('.amount_document_div').html("");
    $('.edit_amount_document_div').html("");
});

function getService(type="" , service_id_arry="") {
    $('.edit_amount_document_div').html("");
    $('.amount_document_div').html("");
    $("#edit_pay_service_id").val('').change();
   
    $.ajax({
        type: "GET",
        url: _GET_SERVICES,
        data: {
            "type": _RECORD_TYPE,
        },
        success: function (res) {
            if (res != '') {
                htmlsresp = '';
                $.each(res.data, function(key,val){
                    htmlsresp += `<option value="${key}">${val}</option>`;
                });
                if (type == 'edit' ) {
                    $("#edit_pay_service_id").html(htmlsresp);
                    $('#edit_pay_service_id').val(service_id_arry);
                }else{
                    $('#pay_service_id').html(htmlsresp);
                }
            } else {
                htmlsresp = '<option value="">No record available</option>';
                if(type == 'edit'){
                    $("#edit_pay_service_id").html(htmlsresp);
                }else{
                    $('#pay_service_id').html(htmlsresp);
                }
            }

        }
    })
}

$(document).on('paste', '.pastecls', function (e) {
    var pastedData = e.originalEvent.clipboardData.getData('text');
    
    // Check if pasted data is a valid number with single '.'
    if (!/^\d*\.?\d*$/.test(pastedData) || (pastedData.split('.').length > 2)) {
        e.preventDefault(); // Prevent invalid paste
    }

    var charCode = (e.which) ? e.which : e.keyCode;
    if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {
        e.preventDefault(); // Block invalid characters
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
        e.preventDefault();  // Block invalid input
    }
    return true;
});

$(document).on('keypress', '.pastecls', function (e) {
    var charCode = (e.which) ? e.which : e.keyCode;
    var currentValue = $(this).val();
    // Allow only one '.' and restrict invalid characters
    if ((charCode === 46 && currentValue.indexOf('.') !== -1) || (charCode !== 46 && (charCode < 48 || charCode > 57))) {
        e.preventDefault(); // Block invalid characters or multiple dots
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
        e.preventDefault();  // Block invalid input
    }
});

$("#add_payment_data").on('click',function(){
    $('.amount_document_div').html("");
    $('.edit_amount_document_div').html("");
    $('#add_status_payment_type').val('').change();
    $('#edit_status_payment_type').val('').change();
    $('#add_status_location_id').val('').change();
    $('#pay_request_service_id').val('').change();
    $('#pay_service_id').val('').change();
    $('#add_status_location_error').html('');
    $('#add_payment_type_error').html('');
    $('#status_service_error').html('');
    $('.amount_document_div').html("");
    $('.edit_amount_document_div').html("");
    $('#edit_payment_type_error').html("");
    $('#edit_status_location_error').html("");
    $('#edit_service_error').html("");
    $('#edit_service_req_error').html("");
    $("#edit_pay_service_id").val('').change();
})