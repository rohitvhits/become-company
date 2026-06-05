countData();
locationWiseData();
agencyWiseData();
serviceWiseData();
$('#agency_id').change(function(){
    countData();
    locationWiseData();
    agencyWiseData();
    serviceWiseData();
    drawChart();
    drawMonthlyChart();
  });

function countData(){
    var agency_id = $('#agency_id').val();
    $('#total_pay').html('$0.00');
    $('#remaining_pay').html('$0.00');
    $('#recieved_pay').html('$0.00');
    $('.bg-info,.bg-success,.bg-danger').addClass('shimmer');
    $.ajax({
        type: "GET",
        url: COUNT_DATA,
        data: {
            'agency_id' : agency_id
         },
        success: function (res) {
            $('#total_pay').html(res.data.total);
            $('#remaining_pay').html(res.data.remaining_amount);
            $('#recieved_pay').html(res.data.received_amount);
            agency_id = getUrl('agency_fk', agency_id);
            $('#total_payment_link').attr('href',_PAYMENT_URL+'?'+agency_id);
            $('#total_remaining_link').attr('href',_PAYMENT_URL+'?'+agency_id);
            $('#total_recieved_link').attr('href',_PAYMENT_URL+'?'+agency_id);
            $('.bg-info,.bg-success,.bg-danger').removeClass('shimmer');
        }
    })
}

function locationWiseData(){
    var agency_id = $('#agency_id').val();
    $('#location_wise_payment_data').html("");
    $('.location-wise-data-loader').attr('style','display:flex');
    $.ajax({
        type: "GET",
        url: LOCATION_WISE_DATA,
        data: {
            'agency_id' : agency_id
         },
        success: function (res) {
            $('#location_wise_payment_data').html(res);
            $('.location-wise-data-loader').attr('style','display:none');
        }
    })
}

function agencyWiseData(){
    var agency_id = $('#agency_id').val();
    $('#agency_wise_payment_data').html("");
    $('.agency-wise-data-loader').attr('style','display:flex');
    $.ajax({
        type: "GET",
        url: AGENCY_WISE_DATA,
        data: {
            'agency_id' : agency_id
         },
        success: function (res) {
            $('#agency_wise_payment_data').html(res);
            $('.agency-wise-data-loader').attr('style','display:none');
        }
    })
}

function serviceWiseData(){
    var agency_id = $('#agency_id').val();
    $('#service_wise_payment_data').html("");
    $('.service-wise-data-loader').attr('style','display:flex');
    $.ajax({
        type: "GET",
        url: SERVICE_WISE_DATA,
        data: {
            'agency_id' : agency_id
         },
        success: function (res) {
            $('#service_wise_payment_data').html(res);
            $('.service-wise-data-loader').attr('style','display:none');
        }
    })
}
function getUrl(field, fieldValue) {
    var iscomm = isCommaSeparated(fieldValue);
    if(fieldValue != undefined){
        if (iscomm) {
            str = '';
            $.each(fieldValue, function (i, v) {
                str += '&' + field + '[]=' + v
            });
        } else {
            str = '&' + field + '[]=' + fieldValue;
        }
    }else{
        str = '&' + field + '[]='; 
    }

    return str;
}

function isCommaSeparated(str) {
    return /^([^,]+,)+[^,]+$/.test(str);
}

function generateColorPalette(length) {
    var defaultColors = ["#4CAF50", "#FF9800", "#E91E63", "#3F51B5", "#9C27B0"];
    var colors = [];
    for (var i = 0; i < length; i++) {
        colors.push(defaultColors[i % defaultColors.length]);
    }
    return colors;
}

function drawChart() {
    $('.chart-shimmer').attr('style', 'display:flex');
    var agency_id = $('#agency_id').val();
    $('#payment_no_data').css('display', 'none');
    $('#piechart').html('');
    $('#payment_type_table_list').html('');
    $('#piechart').attr('style', 'display:block');
    $('#payment_type_table_list').attr('style', 'display:block');
    // Data based on your example
    $.ajax({
        url: _PAYMENT_CHART,
        dataType: "json",
        type: "GET",
        data: {
            'agency_id' : agency_id
         },
        success: function(res){
            var resdata = res.data;
            if (resdata.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#piechart').attr('style', 'display:none');
                $('#payment_type_table_list').attr('style', 'display:none');
                $('#payment_no_data').css('display', 'block');
                $('.chart-shimmer').attr('style', 'display:none');
            } else{
                console.log(resdata);
                var chartData = [['Priority', 'Total']];
                resdata.forEach(function (item) {
                    chartData.push([item.type, item.count]);
                });
                var data = google.visualization.arrayToDataTable(chartData);
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                var options = {
                    colors: colors,
                    pieHole: 0.4,
                    width:'100%',
                    hight:'100%',
                    is3D: true,
                    legend: { position: "top", maxLines: 5 },
                };
                var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                chart.draw(data, options);

                var html = '';
                html += `<div class="card-footer bg-light p-0">
                            <ul class="nav nav-pills flex-column">`;
                resdata.forEach(function (item,key) {
                    html += ` <li class="nav-item">                                
                            ${item.type}
                                <span class="float-right text-info" style="color:${colors[key]} !important">
                                    ${item.count}</span>
                            </li>`;
                });

                html += `</ul>
                            </div>`;
                $('#payment_type_table_list').html(html);
                $('.chart-shimmer').attr('style', 'display:none');
            }
        }
    });
}

var chartResponse;
function drawMonthlyChart() {
    var agency_id = $('#agency_id').val();
    select_type = $('#patient_type').val();
    $('#monthlyChart').attr('style', 'display:block');
    $('#monthly_table_list').attr('style', 'display:block');
    $('.chart-shimmer').attr('style', 'display:flex');
    $('#monthly_no_data').css('display', 'none');
    $('#monthlyChart').html('');
    $('#monthly_table_list').html('');
    $.ajax({
        url: _MONTHLY_DATA,
        dataType: "json",
        type: "GET",
        data: {
            'agency_id' : agency_id
         },
        success: function (datalist) {
            var data = datalist.data;
            var colors = [];
            var colorpallate = (generateColorPalette(data.length));
            var colors = colors.concat(colorpallate);
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#monthlyChart').attr('style', 'display:none');
                $('#monthly_table_list').attr('style', 'display:none');
                $('#monthly_no_data').css('display', 'block');
                $('.chart-shimmer').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Month');
                datas.addColumn('number', 'Amount');
                datalist.data.forEach(function (item) {
                    chartData.push([item.month_name, parseFloat(item.sum)]);
                });
                datas.addRows(chartData);
                var formatter = new google.visualization.NumberFormat({
                    prefix: '$',       // Add $ sign
                    fractionDigits: 2  // Show 2 decimal places
                });
                formatter.format(datas, 1); // 1 is the column index for Amount
                var options = {
                    title: '',
                    curveType: 'function',
                    legend: { position: 'none'},
                    vAxis: {
                        format: 'currency' // Optional, ensures $ sign on Y-axis
                    }    
                };
          
                var chart = new google.visualization.ColumnChart(document.getElementById('monthlyChart'));
          
                chart.draw(datas, options);

                var html = '';
                html += `<div class="card-footer bg-light p-0">
                            <ul class="nav nav-pills flex-column">`;
                datalist.data.forEach(function (item,key) {
                    html += ` <li class="nav-item">                                
                            ${item.month_name}
                                <span class="float-right text-info" style="color:${colors[key]} !important">
                                    ${formatUSD(item.sum)}</span>
                            </li>`;
                });

                html += `</ul>
                            </div>`;
                $('#monthly_table_list').html(html);

                $('.chart-shimmer').attr('style', 'display:none');
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