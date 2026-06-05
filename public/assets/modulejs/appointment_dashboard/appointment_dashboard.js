loadTotalCount();
loadAgencyAppoitmentData();
var chartResponse;
function drawStatusDataChart() {
    range_date = $('#range_date').val();
    $('.status-order-listing-loader1').attr('style', 'display:flex');
    $('#status_donut_chart').html('');
    $('#status-no-data').css('display', 'none');
    $.ajax({
        url: STATUS_CHART_DATA + '?range_date=' + range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#status-no-data').css('display', 'block');
                $('.status-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [['Status', 'Total']];
                var colors = [];
                var colorpallate = (generateColorPalette(Object.keys(data).length));
                var colors = colors.concat(colorpallate);
                // Loop through the data and add it to the chartData array
                Object.keys(data).forEach(function (key) {
                    chartData.push([key, data[key]]);
                });
                var data = google.visualization.arrayToDataTable(chartData);
                var options = {
                    colors: colors,
                    is3D: true,
                    height:270,
                    width:350,
                    legend: { position: 'right'},
                };
                var chartd = new google.visualization.PieChart(document.getElementById('status_donut_chart'));
                chartd.draw(data, options);
                google.visualization.events.addListener(chartd, 'select', selectHandlers);

                function selectHandlers(e) {
                    var selectedItem = chartd.getSelection()[0];
                    if (selectedItem) {
                        var Url = _PATIENT_LIST + '?status=' + data.getValue(selectedItem.row, 0)
                        window.open(Url, '_blank');
                    }
                }
                $('.status-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

var chartResponse;
function drawPatientMonthlyChart() {
    select_type = $('#patient_type').val();
    year = $('#patient_year').val();
    month = $('#patient_month').val();
    week = $('#patient_week').val();
    $('.patient-monthly-order-listing-loader1').attr('style', 'display:flex');
    $('#patient_monthly_view_chart').html('');
    $('#patient-monthly-no-data').css('display', 'none');
    $.ajax({
        url: _PATIENT_APPOINTMENT_MOTHLY + '?type='+select_type+'&year='+year+'&month='+month+'&week='+week,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#patient-monthly-no-data').css('display', 'block');
                $('.patient-monthly-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', select_type);
                datas.addColumn('number', 'Appointment');
                datalist.data.forEach(function (item) {
                    chartData.push([item.name, item.total_records]);
                });
                datas.addRows(chartData);
                var options = {
                    title: '',
                    curveType: 'function',
                    legend: { position: 'none'},
                    height:310,
                    width:500
                };
          
                var chart = new google.visualization.LineChart(document.getElementById('patient_monthly_view_chart'));
          
                chart.draw(datas, options);
                
                $('.patient-monthly-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

var chartResponse;
function drawAgencyChart() {
    $('.agency-order-listing-loader1').attr('style', 'display:flex');
    $('#agency_view_chart').html('');
    $('#agency-no-data').css('display', 'none');
    var agency_range_date = $('#agency_range_date').val();
    $.ajax({
        url: _AGENCY_WISE_APPOINTMENT_DATA,
        dataType: "json",
        type: "GET",
        data: {
            'agency_range_date' : agency_range_date 
        },
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
                datas.addColumn('string', 'Agnecy');
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
                    bar: {groupWidth: "95%"}, // Increase the width of bars
                    chartArea: {
                        left: 50,   // Reduce left margin
                        width: '80%',
                        height: '80%'
                    },
                    vAxis: {
                        title: '',
                        gridlines: { count: 7 },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                    height:358,
                    width: 2000
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('agency_view_chart'));
                chart.draw(datas, options);
                google.visualization.events.addListener(chart, 'select', selectHandlers);

                function selectHandlers(e) {
                    var selectedItem = chart.getSelection()[0];
                    var c = chart.getSelection()[0].row;
                    if (selectedItem) {
                        agency_id = datalist.data[c].agency_id
                        var Url = _PATIENT_LIST + '?agency_fk=' + agency_id+'&created_date='+agency_range_date
                        window.open(Url, '_blank');
                    }
                }
                $('.agency-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

function drawLocationChart() {
    $('.location-order-listing-loader1').attr('style', 'display:flex');
    $('#location_view_chart').html('');
    $('#location-no-data').css('display', 'none');
    var location_range_date = $('#location_range_date').val();
    $.ajax({
        url: _LOCATION_WISE_APPOINTMENT_DATA,
        dataType: "json",
        type: "GET",
        data: {
            'location_range_date' : location_range_date 
        },
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#location-no-data').css('display', 'block');
                $('.location-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Location');
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
                    bar: {groupWidth: "30%"}, // Increase the width of bars
                    vAxis: {
                        title: '',
                        gridlines: { count: 7 },
                        // viewWindow: {
                        //     min: 0,
                        //     max: 10000 // Set the vertical axis to range from 0 to 1,000.
                        // },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                    height:340,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('location_view_chart'));
                chart.draw(datas, options);
                google.visualization.events.addListener(chart, 'select', selectHandlers);

                function selectHandlers(e) {
                    var selectedItem = chart.getSelection()[0];
                    var c = chart.getSelection()[0].row;
                    if (selectedItem) {
                        location_id = datalist.data[c].location_id
                        var Url = _PATIENT_LIST + '?locationId=' + location_id+'&created_date='+location_range_date
                        window.open(Url, '_blank');
                    }
                }
                $('.location-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

var chartResponse;
function drawUserChart() {
    $('.user-order-listing-loader1').attr('style', 'display:flex');
    $('#user_view_chart').html('');
    $('#user-no-data').css('display', 'none');
    var user_range_date = $('#user_range_date').val();
    $.ajax({
        url: _USER_WISE_APPOINTMENT_DATA,
        dataType: "json",
        type: "GET",
        data : {
            'user_range_date' : user_range_date
        },
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#user-no-data').css('display', 'block');
                $('.user-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Agnecy');
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
                    chartArea: {
                        left: 50,   // Reduce left margin
                    },
                    legend: { position: "none" },
                    bar: {groupWidth: "200%"}, // Increase the width of bars
                    vAxis: {
                        title: '',
                        gridlines: { count: 7 },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                    height:330,
                    width:7000
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('user_view_chart'));
                chart.draw(datas, options);
                google.visualization.events.addListener(chart, 'select', selectHandlers);

                function selectHandlers(e) {
                    var selectedItem = chart.getSelection()[0];
                    var c = chart.getSelection()[0].row;
                    if (selectedItem) {
                        created_by = datalist.data[c].created_by
                        cname = datalist.data[c].name
                        var Url = _PATIENT_LIST + '?created_by_ny_id='+created_by+'&created_by_ny_name='+cname+'&created_date='+user_range_date
                        window.open(Url, '_blank');
                    }
                }
                $('.user-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

var chartResponse;
function drawServicesChart() {
    $('.services-order-listing-loader1').attr('style', 'display:flex');
    $('#services_chart').html('');
    $('#services-no-data').css('display', 'none');
    $.ajax({
        url: _SERVICES_WISE_APPOINTMENT_DATA,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#services-no-data').css('display', 'block');
                $('.services-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [['Services', 'Total']];
                var colors = [];
                var colorpallate = (generateColorPalette(Object.keys(data).length));
                var colors = colors.concat(colorpallate);
                // Loop through the data and add it to the chartData array
                datalist.data.forEach(function (item) {
                    chartData.push([item.name, item.count]);
                });
                var data = google.visualization.arrayToDataTable(chartData);
                var options = {
                    colors: colors,
                    pieHole: 0.4,
                    height: 225,
                    width: 250,
                    legend: { position: 'top',maxLines: 3},
                };
                var chartd = new google.visualization.PieChart(document.getElementById('services_chart'));
                chartd.draw(data, options);
                $('.services-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

function loadTotalCount() {
    $('.order-total-case-loader1').attr('style', 'display:flex');
    var range_date = $('#range_date').val();
    $.ajax({
        type: "GET",
        url: TOTAL_COUNTS_DATA,
        success: function (res) {
            var json = res.data;
            $('#total_appointment').html(json.totalAppointment)
            $('#total_caregiver').html(json.totalCaregiver)
            $('#total_patient').html(json.totalPatient)
            $('#total_agencies').html(json.totalAgencies)
            $('#total_hha_caregivers').html(json.totalHHACaregiver)
            $('#total_hha_patients').html(json.totalHHAPatient)
            $('#total_remote_clients').html(json.totalRemote)
            $('#total_visiting_aids').html(json.totalVisiting)
            $('.order-total-case-loader1').attr('style', 'display:none');
        }
    })
}

function generateColorPalette(length) {
    var predefinedColors = [
        '#A5D6A7', '#81D4FA', '#FFF176', '#FFAB91', '#FFCC80', '#B2EBF2', '#C5CAE9', '#E6EE9C'
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

function loadAgencyAppoitmentData() {
    $('.appointment-order-listing-loader1').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: _LOAD_AGENCY_DATA,
        success: function (response) {
            $('#popular_agency').html("")
            $('#popular_agency').html(response);
            $('.appointment-order-listing-loader1').attr('style', 'display:none');
        }
    })
}

function loadLocationAppoitmentData() {
    $('.appointment-order-listing-loader1').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: _LOAD_LOCATIONS_DATA,
        success: function (response) {
            $('#popular_location').html("")
            $('#popular_location').html(response);
            $('.appointment-order-listing-loader1').attr('style', 'display:none');
        }
    })
}

function loadUserAppoitmentData() {
    $('.appointment-order-listing-loader1').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: _LOAD_USER_DATA,
        success: function (response) {
            $('#popular_user').html("")
            $('#popular_user').html(response);
            $('.appointment-order-listing-loader1').attr('style', 'display:none');
        }
    })
}

function showHide(){
    $('#patient_month').addClass('d-none');
    $('#patient_week').addClass('d-none');
    var patient_type = $('#patient_type').val();
    if(patient_type == 'monthly'){
        $('#patient_month').removeClass('d-none');
    }
    if(patient_type == 'weekly'){
        $('#patient_month').removeClass('d-none');
        $('#patient_week').removeClass('d-none');
    }
}

var chartResponse;
function drawMonthlyCompareChart() {
    year = $('#year').val();
    $('.location-monthly-order-listing-loader1').attr('style', 'display:flex');
    $('#monthly_comparision_view_chart').html('');
    $('#monthly-comparision-no-data').css('display', 'none');
    $.ajax({
        url: MONTHLY_COMPARISION_CHART_DATA,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#monthly-comparision-no-data').css('display', 'block');
                $('.monthly-comparision-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                datas.addColumn('string', 'Year');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                datas.addColumn({ type: 'number', role: 'annotation' });
                data.forEach(function (item, val) {
                    chartData.push([item.year, item.count,colors[val],item.count]);
                });
                datas.addRows(chartData);
                var options = {
                    legend: { position: 'none'},
                    bar: {
                        groupWidth: '30%'  // Adjust bar thickness for clarity
                    },
                    hAxis: {
                        title: '',
                        slantedText: true, // Enable slanted text for labels
                        slantedTextAngle: 45, // Rotate the labels by 45 degrees
                        textStyle: {
                            fontSize: 14,  // Smaller text size for the legend
                        }
                    },
                    height: 250,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('monthly_comparision_view_chart'));
                chart.draw(datas, options);
                $('.monthly-comparision-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

$(function () {
    $('.js-example-basic-multiple').select2();
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#user_range_date').daterangepicker({
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
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
    }, function (chosen_date, end_date) {

        $('#user_range_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
        drawUserChart();
        
    })
});

$(function () {
    $('.js-example-basic-multiple').select2();
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#agency_range_date').daterangepicker({
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
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
    }, function (chosen_date, end_date) {

        $('#agency_range_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
        drawAgencyChart()
        
    })
});

$(function () {
    $('.js-example-basic-multiple').select2();
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#location_range_date').daterangepicker({
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
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
    }, function (chosen_date, end_date) {

        $('#location_range_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
        drawLocationChart()
        
    })
});