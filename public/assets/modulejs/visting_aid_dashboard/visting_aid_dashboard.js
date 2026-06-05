$(function () {
    $('.js-example-basic-multiple').select2();
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#range_date').daterangepicker({
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
        $('#range_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
        loadVisitingAidListData();
        loadTotalVisitingData();
        drawAgencyWiseDataChart();
        drawTypeWiseChart();
        drawServicesStatusWiseChart();
    })
});

loadVisitingAidListData();
loadTotalVisitingData();

function loadTotalVisitingData(){
    $('.visting-total-listing-loader1').attr('style', 'display:flex');
    var range_date = $('#range_date').val();
    $.ajax({
        type: "GET",
        url: _VISTING_AID_COUNT_DATA,
        data: {
            'range_date': range_date
        },
        success: function (response) {
            response = response.data;
            $('#total_visits').html(response.visiting_aid);
            $('#total_agencies').html(response.total_agencies);
            $('#total_patient').html(response.total_patients);
            $('#pending_requests').html(response.pending);
            $('#completed_services').html(response.completed);
            $('#overdue').html(response.overdue);
            $('.visting-total-listing-loader1').attr('style', 'display:none');
        }
    })
}

function loadVisitingAidListData(page) {
    $('#visiting_aid_list').html("")
    $('.visting-listing-loader1').attr('style', 'display:flex');
    var range_date = $('#range_date').val();
    $.ajax({
        type: "GET",
        url: _VISTING_AID_LIST_DATA,
        data: {
            'page': page,
            'range_date': range_date
        },
        success: function (response) {
            $('#visiting_aid_list').html("")
            $('#visiting_aid_list').html(response);
            $('.visting-listing-loader1').attr('style', 'display:none');
        }
    })
}


$(document).on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadVisitingAidListData(page);
});

var chartResponse;
function drawAgencyWiseDataChart(){
    $('#agency_wise_chart').html('');
    $('.agency-order-listing-loader1').attr('style', 'display:flex');
    var range_date = $('#range_date').val();
    $.ajax({
        url: _VISTING_AGENCY_WISE + '?range_date=' + range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#agency-no-data').css('display', 'block');
                $('.agency-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Agency');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                datalist.data.forEach(function (item, val) {
                    chartData.push([item.name, item.count, colors[val]]);
                });
                datas.addRows(chartData);
                var options = {
                    title: '',
                    is3D: true,
                    hAxis: {
                        title: '',
                        slantedText: true, // Enable slanted text for labels
                        slantedTextAngle: 45, // Rotate the labels by 45 degrees
                        textStyle: {
                            fontSize: 14,  // Smaller text size for the legend
                        }
                    },
                    legend: { position: "none" },
                    bar: { groupWidth: "20%" }, // Increase the width of bars
                    chartArea: {
                        left: 50,
                    },
                    vAxis: {
                        title: '',
                        gridlines: { count: 5 },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('agency_wise_chart'));
                chart.draw(datas, options);
                $('.agency-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

var chartTypeResponse;
function drawTypeWiseChart(){
    if (chartTypeResponse) {
        chartTypeResponse.destroy();
    }
    $('.type-order-listing-loader1').attr('style', 'display:flex');
    var range_date = $('#range_date').val();
    $('#type_wise_chart').html('')
    $.ajax({
        url: _VISTING_TYPE_DATA + '?range_date=' + range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartTypeResponse) {
                    chartTypeResponse.destroy();
                }
                $('#type-no-data').css('display', 'block');
                $('.type-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Type');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                datalist.data.forEach(function (item, val) {
                    chartData.push([item.type, item.count, colors[val]]);
                });
                datas.addRows(chartData);
                var options = {
                    title: '',
                    is3D: true,
                    hAxis: {
                        title: '',
                        slantedText: true, // Enable slanted text for labels
                        slantedTextAngle: 45, // Rotate the labels by 45 degrees
                        textStyle: {
                            fontSize: 14,  // Smaller text size for the legend
                        }
                    },
                    legend: { position: "none" },
                    bar: { groupWidth: "50%" }, // Increase the width of bars
                    vAxis: {
                        title: '',
                        gridlines: { count: 5 },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                };
                var chart = new google.visualization.BarChart(document.getElementById('type_wise_chart'));
                chart.draw(datas, options);

                $('.type-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}
var chartServicesResponse;
function drawServicesStatusWiseChart(){
    $('#status_donut_chart').html('');
    $('.services-order-listing-loader1').attr('style', 'display:flex');
    range_date = $('#range_date').val();
    $('#services_table_list').html('');
    $.ajax({
        url: _VISTING_SERVICE_STATUS + '?range_date=' + range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartServicesResponse) {
                    chartServicesResponse.destroy();
                }
                $('#services-no-data').css('display', 'block');
                $('.services-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [['Priority', 'Total']];
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                // Loop through the data and add it to the chartData array
                data.forEach(function (item) {
                    chartData.push([item.status, item.count]);
                });
                var data = google.visualization.arrayToDataTable(chartData);
                var options = {
                    colors: colors,
                    pieHole: 0.4,
                    width:'100%',
                    hight:'100%',
                    legend: { position: "top", maxLines: 5 },
                };
                var chartd = new google.visualization.PieChart(document.getElementById('status_donut_chart'));
                chartd.draw(data, options);
                $('.services-order-listing-loader1').attr('style', 'display:none');
                var html = '';
                html += `<div class="card-footer bg-light p-0">
                            <ul class="nav nav-pills flex-column">`;
                datalist.data.forEach(function (item,key) {
                    html += ` <li class="nav-item">                                
                            ${item.status}
                                  <span class="float-right text-info" style="color:${colors[key]} !important">
                                    ${item.count}</span>
                              </li>`;
                });

                html += `</ul>
                              </div>`;
                $('#services_table_list').html(html);

            }
        }
    });
}

function drawServicesWiseChart(){
    
}

function generateColorPalette(length) {
    var predefinedColors = [
        '#00BBE0', '#ffc107', '#003366', '#8B0000', '#4B0082', '#36454F', '#2F4F4F', '#191970', '#006D5B', '#2a3439' , '#556B2F', '#1D2951','#008B8B '
    ];

    var colors = [];

    // Cycle through predefined colors if length exceeds predefined array
    for (var i = 0; i < length; i++) {
        if (i < predefinedColors.length) {
            colors.push(predefinedColors[i]);
        } else {
            const randomColor = Math.floor(Math.random() * 0xFFFFFF);
            colorsPallete = '#' + randomColor.toString(16).padStart(6, '0');
            colors.push(colorsPallete);
        }
    }
    return colors;
}
