$(function () {
    $('.js-example-basic-multiple').select2();
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#case_range_date').daterangepicker({
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

        $('#case_range_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
        loadPatientCount();
        drawStatusDataChart();
        loadNotesData();
        drawServiceDataChart();
        loadTodayAppoitmentData();
        drawAgencyDataChart();
        loadPieChartNew();
        loadUpcommingAppoitmentData();
        loadLocationChart();
    })
});


function loadPatientCount() {
    var case_range_date = $('#case_range_date').val();
    $('.order-total-case-loader1').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: _TOTAL_PATIENT,
        data: {
            'case_range_date': case_range_date,
        },
        success: function (res) {
            var json = res.data;
            $('#total_patient').html(json.totalPatient)
            $('#total_caregiver').html(json.totalCaregiver)
            $('#total_agencies').html(json.totalAgencies)
            $('#total_hha_caregiver').html(json.totalHHACaregiver)
            $('#total_hha_patient').html(json.totalHHAPatientCount)

            $('#total_employee').html(json.totalEmployeeCount)
            $('#total_client').html(json.totalClientCount)
            $('#total_remote_client').html(json.totalRemoteCount)
            $('#total_visiting_aids').html(json.totalVisitingAidsCount)

            $('#total_pending').html('<a href="' + _PATIENT_LIST_SEARCH + '?status[]=Pending&created_date='+case_range_date+'" target="_blank">' + json.totalPendingCount + '</a>')
            updateProgressBar('total_pending_progress', json.totalPendingCount, json.totalCasesCount)
            $('#total_completed').html('<a href="' + _PATIENT_LIST_SEARCH + '?status[]=Completed&created_date='+case_range_date+'" target="_blank">' + json.totalCompletedCount + '</a>')
            updateProgressBar('total_completed_progress', json.totalCompletedCount, json.totalCasesCount)
            $('#total_booked').html('<a href="' + _PATIENT_LIST_SEARCH + '?status[]=Scheduled&created_date='+case_range_date+'" target="_blank">' + json.totalBookedCount + '</a>')
            updateProgressBar('total_booked_progress', json.totalBookedCount, json.totalCasesCount)
            $('#total_processing').html('<a href="' + _PATIENT_LIST_SEARCH + '?status[]=MarkAsProcessing&created_date='+case_range_date+'" target="_blank">' + json.totalProcessingCount + '</a>')
            updateProgressBar('total_processing_progress', json.totalProcessingCount, json.totalCasesCount)
            $('#total_cases').html('<a href="' + _PATIENT_LIST_SEARCH + '?created_date='+case_range_date+'" target="_blank">' + json.totalCasesCount + '</a>')
            $('.order-total-case-loader1').attr('style', 'display:none');
        }
    })
}

function loadTodayAppoitmentData(page) {
    $('.appoitment-listing-loader1').attr('style', 'display:flex');
    var case_range_date = $('#case_range_date').val();
    $.ajax({
        type: "GET",
        url: _TODAY_APPOITMENT,
        data: {
            'page': page,
            'type': 'today',
            'case_range_date': case_range_date
        },
        success: function (response) {
            $('#today_appoinment').html("")
            $('#today_appoinment').html(response);
            $('.appoitment-listing-loader1').attr('style', 'display:none');
        }
    })
}

loadTodayAppoitmentData(1);
loadPatientCount();

function drawAgencyDataChart() {
    agency_id = $('#location_agency_id').val() ?? '';
    agency_type_id = $('#agency_type_id').val() ?? '';
    case_range_date = $('#case_range_date').val();
    $('.loaction-order-listing-loader1').attr('style', 'display:flex');
    $.ajax({
        url: _GRAPH_TOTAL_CAREGIVER_PATIENT + '?agency_id=' + agency_id + '&agency_type_id=' + agency_type_id + '&case_range_date=' + case_range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            $('.loaction-order-listing-loader1').attr('style', 'display:none');
            if (datalist.length == 0) {
                $('#agency-no-data').css('display', 'block');
            } else {
                $('#agency-no-data').css('display', 'none');
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Location');
                data.addColumn('number', 'Caregiver');
                data.addColumn('number', 'Patient');

                var chart_data = [];
                for (var i = 0; i < datalist.data.length; i++) {

                    if (datalist.data[i].caregiver_count != 0 || datalist.data[i].patient_count != 0) {

                        chart_data.push([datalist.data[i].city, datalist.data[i].caregiver_count, datalist.data[i].patient_count]);
                    }
                }

                data.addRows(chart_data);
                var colors = ['#7571f9', '#0099CC', '#33CC33'];

                var options = {
                    title: '',
                    isStacked: true,
                    colors: colors,
                    chartArea: { width: '60%' },
                    bar: { groupWidth: '55%' },
                    hAxis: {
                        title: '',

                    },
                    vAxis: {
                        title: ''
                    }
                };

                var chart = new google.visualization.BarChart(document.getElementById('stock_meterial_chart'));
                chart.draw(data, options);
                google.visualization.events.addListener(chart, 'select', selectHandler);

                function selectHandler(e) {
                    var selection = chart.getSelection();

                    var c = chart.getSelection()[0].row;
                    if (selection.length > 0) {
                        var selectedItem = selection[0];

                        var city = data.getValue(selectedItem.row, 0);
                        var role = selectedItem.column === 1 ? 'Caregiver' : 'Patient';
                        var caregiverUrl = _PATIENT_LIST_SEARCH + '?type=' + role + '&locationId[]=' + datalist.data[c].id + '&agency_fk[]=' + agency_id + '&created_date=' + datalist.data[c].case_range_date
                        window.open(caregiverUrl, '_blank');
                    }

                }
            }
        }

    });
}

$('#location_agency_id').change(function (e) {
    drawAgencyDataChart();
})

var chartResponse;
function drawServiceDataChart() {

    type_id = $('#type_id').val();
    location_id = $('#location_id').val();
    agency_id = $('#agency_id').val();
    service_id = $('#service_id1').val();
    case_range_date = $('#case_range_date').val();
    $('.service-order-listing-loader1').attr('style', 'display:flex');
    $('#pieChartNew').html('');
    $('#pieChartNew1').html('');
    $('#service-no-data').css('display', 'none');
    $.ajax({
        url: _GRAPH_TOTAL_SERVICE + '?type=' + type_id + '&location_id=' + location_id + '&agency_id=' + agency_id + '&service_id=' + service_id + '&case_range_date=' + case_range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#service-no-data').css('display', 'block');
            } else {
                var chartData = [['Services', 'Total']];
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                // Loop through the data and add it to the chartData array
                data.forEach(function (item) {
                    chartData.push([item.name, item.total]);
                });
                var data = google.visualization.arrayToDataTable(chartData);

                var options = {

                    is3D: true,
                    colors: colors
                };

                var chartd = new google.visualization.PieChart(document.getElementById('pieChartNew'));
                chartd.draw(data, options);
                google.visualization.events.addListener(chartd, 'select', selectHandlers);

                function selectHandlers(e) {
                    var selection = chartd.getSelection();

                    // var c = chart.getSelection()[0].row;
                    var c = selection[0].row;
                    if (selection.length > 0) {
                        var selectedItem = selection[0];
                        var agency = getUrl('agency_fk', agency_id);
                        var Url = _PATIENT_LIST_SEARCH + '?service_id[]=' + datalist.data[c].id + '&type=' + type_id + '&locationId[]=' + location_id + agency + '&created_date=' + case_range_date
                        window.open(Url, '_blank');
                    }

                }

                // Started service coloumn chart

                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Services');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                datalist.data.forEach(function (item, val) {
                    chartData.push([item.name, item.total, colors[val]]);
                });
                datas.addRows(chartData);
                var options = {
                    title: '',
                    // isStacked:true,
                    is3D: true,
                    chartArea: { width: '70%' },
                    hAxis: {
                        title: '',
                        slantedText: true, // Enable slanted text for labels
                        slantedTextAngle: 90, // Rotate the labels by 45 degrees
                        textStyle: {
                            fontSize: 14,  // Smaller text size for the legend
                        }
                    },
                    legend: { position: "none" },
                    bar: { groupWidth: "200%" }, // Increase the width of bars
                    vAxis: {
                        title: '',
                        gridlines: { count: 5 },
                        viewWindow: {
                            min: 0,
                            max: 20000 // Set the vertical axis to range from 0 to 1,000.
                        },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                    chartArea: {
                        left: 100,
                    },
                    height: 500,
                    width: 3000,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('pieChartNew1'));

                chart.draw(datas, options);

                google.visualization.events.addListener(chart, 'select', selectHandler);

                function selectHandler(e) {
                    var selection = chart.getSelection();

                    var c = chart.getSelection()[0].row;
                    if (selection.length > 0) {
                        var agency = getUrl('agency_fk', agency_id);
                        var Url = _PATIENT_LIST_SEARCH + '?service_id[]=' + datalist.data[c].id + '&type=' + type_id + '&locationId[]=' + location_id + agency + '&created_date=' + case_range_date
                        window.open(Url, '_blank');
                    }

                }

            }
        }
    });
    $('.service-order-listing-loader1').attr('style', 'display:none');
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
function doughnutPieOptions(data, options) {

    if (chartResponse) {
        chartResponse.destroy();
    }
    options.is3D = true;
    var ctx = document.getElementById('pieChartNew').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options
    });

    return chart;
}

function loadPieChartNew() {
    drawServiceDataChart();
}

function loadServices() {
    existingId = $('#type_id').val();
    $.ajax({
        async: false,
        global: false,
        type: "GET",
        url: _AJAX_SERVICES,
        data: {
            "id": existingId,
        },
        success: function (res) {

            if (res != '') {
                htmlsresp = res;
            } else {
                htmlsresp += '<option value="">No record available</option>';
            }
            $('#service_id1').html("");
            $('#service_id1').html(htmlsresp);
        }
    })
}

function loadLocationChart() {
    $('.agency-order-listing-loader1').attr('style', 'display:flex');
    var location_id = $('#loc_id').val();
    var location_type_id = $('#location_type_id').val();
    var agency_id = $('#location_ageancy_id').val();
    var case_range_date = $('#case_range_date').val();
    $.ajax({
        url: _GRAPH_TOTAL_LOCATION + '?location_id=' + location_id + '&agency_id=' + agency_id + '&location_type_id=' + location_type_id + '&case_range_date=' + case_range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            // Initialize a Google DataTable
            var data = new google.visualization.DataTable();

            data.addColumn('string', 'Location');
            data.addColumn('number', 'Caregiver');
            data.addColumn('number', 'Patient');
            var chart_data = [];
            // Populate chart_data array with values from datalist
            for (var i = 0; i < datalist.data.length; i++) {
                chart_data.push([datalist.data[i].agency_name, datalist.data[i].patient_caregiver_count, datalist.data[i].patient_total_patient_count]);
            }

            // Add rows to the data table
            data.addRows(chart_data);
            var options = {
                title: '',
                colors: ['#7571f9', '#0099CC'],
                chartArea: { width: '60%' },
                // Enable stacking to control column spacing (even if you're not stacking)
                isStacked: true,
                hAxis: {
                    title: '',
                    slantedText: true, // Enable slanted text for labels
                    slantedTextAngle: 90, // Rotate the labels by 45 degrees
                    // textPosition: 'none',
                    textStyle: {
                        fontSize: 12,  // Smaller text size for the legend
                    }

                },
                bar: { groupWidth: "100%" }, // Increase the width of bars
                vAxis: {
                    title: '',
                    viewWindow: {
                        min: 0,
                        // max: 20000 // Set the vertical axis to range from 0 to 1,000.
                    },
                    format: 'short',  // Format the numbers with "K" (e.g., 1K).
                },
                chartArea: {
                    left: 100, // Add padding to the left
                    right: 50, // Add padding to the right
                    bottom: 150, // Add space at the bottom for the rotated labels
                    top: 50 // Top padding
                },
                legend: {
                    position: 'top', // This positions the legend above the chart
                    alignment: 'left',
                    textStyle: {
                        fontSize: 20,  // Smaller text size for the legend
                    }
                },
                height: 500,
                width: 3500
            };
            var chart = new google.visualization.ColumnChart(document.getElementById('barChartCanvas'));
            chart.draw(data, options);
            google.visualization.events.addListener(chart, 'select', selectHandler);

            function selectHandler(e) {
                var selection = chart.getSelection();

                var c = chart.getSelection()[0].row;
                if (selection.length > 0) {
                    var selectedItem = selection[0];

                    var city = data.getValue(selectedItem.row, 0);
                    var role = selectedItem.column === 1 ? 'Caregiver' : 'Patient';
                    if (agency_id == '') {
                        var agency = getUrl('agency_fk', datalist.data[c].id);
                    } else {
                        var agency = getUrl('agency_fk', agency_id);
                    }
                    // var Url = _PATIENT_LIST_SEARCH + '?type=' + role + '&agency_fk=' + datalist.data[c].id
                    var Url = _PATIENT_LIST_SEARCH + '?locationId[]=' + location_id + agency + '&type=' + role + '&created_date=' + case_range_date
                    window.open(Url, '_blank');
                }

            }
            $('.agency-order-listing-loader1').attr('style', 'display:none');
        }
    });
}

function loadUpcommingAppoitmentData(page = 1) {
    case_range_date = $('#case_range_date').val();
    $('.appoitment-listing-loader1').attr('style', 'display:flex');

    $.ajax({
        type: "GET",
        url: _UPCOMMING_APPOITMENT,
        data: {
            'page': page,
            'type': 'upcomming',
            'case_range_date': case_range_date
        },
        success: function (response) {
            $('#upcomming_appoinment').html("")
            $('#upcomming_appoinment').html(response);
            $('.appoitment-listing-loader1').attr('style', 'display:none');

        }
    })
}

var chartStatusResponse;
function drawStatusDataChart() {

    agency_id = $('#status_agency_id').val();
    case_range_date = $('#case_range_date').val();
    $('.order-status-loader1').attr('style', 'display:flex');
    $.ajax({
        url: _STATUS_WISE_GRAPH + '?agency_id=' + agency_id + '&case_range_date=' + case_range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var response = datalist.data;
            if (response.length == 0) {
                if (chartStatusResponse) {
                    chartStatusResponse.destroy();
                }
                $('#status-no-data').css('display', 'block');
            } else {
                $('#status-no-data').css('display', 'none');
                var chartData = [['Status', 'Total']];

                // Loop through the data and add it to the chartData array
                response.forEach(function (item) {
                    chartData.push([item.name.charAt(0).toUpperCase() + item.name.slice(1), parseInt(item.total)]);
                });
                var data = google.visualization.arrayToDataTable(chartData);
                var options = {
                    is3D: true,
                    colors: ['#A5D6A7', '#81D4FA', '#FFF176', '#FFAB91', '#FFCC80', '#B2EBF2', '#C5CAE9', '#E6EE9C', '#7571f9', '#daa400', '#6a329f', '#1b85b8', '#c3cb71', '#68c4af', '#FFCC80', '#B2EBF2', '#7571f9', '#0099CC'],
                    sliceVisibilityThreshold: 0
                };
                var chart = new google.visualization.PieChart(document.getElementById('statusChartNew'));
                chart.draw(data, options);

                google.visualization.events.addListener(chart, 'select', selectHandlers);

                function selectHandlers(e) {
                    var selection = chart.getSelection();

                    // var c = chart.getSelection()[0].row;
                    var c = selection[0].row;
                    if (selection.length > 0) {
                        var agency = getUrl('agency_fk', agency_id);
                        var Url = _PATIENT_LIST_SEARCH + '?status[]=' + datalist.data[c].statusText + agency + '&created_date=' + case_range_date
                        window.open(Url, '_blank');
                    }

                }
                $('.order-status-loader1').attr('style', 'display:none');
            }
        }
    });
}


function updateProgressBar(id, value, total) {
    // Get the input number
    const input = value;
    // Validate input (should be between 0 and 100)
    let progressValue = (value / total) * 100;
    // Ensure the percentage is between 0 and 100
    progressValue = Math.min(Math.max(progressValue, 0), 100);
    $('.' + id).show();
    // Update the progress bar
    if (progressValue > 0) {
        const progressBar = document.getElementById(id);
        progressBar.style.width = progressValue + '%';
        progressBar.setAttribute('aria-valuenow', progressValue);
        progressBar.setAttribute('title', Math.round(progressValue) + '%');
    } else {
        $('.' + id).hide();
    }
}

$(document).on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var nurl = $(this).attr('href').split('type=');

    var redirection = nurl[1].split('&');
    if (redirection[0] == 'today') {
        loadTodayAppoitmentData(page);
    }

    if (redirection[0] == 'upcomming') {
        loadUpcommingAppoitmentData(page);
    }
});

let notesPage = 1;
let isLoading = false;
loadNotesData();
$('#notes_section').on('scroll', function () {
    let div = $(this);
    // Check if the user has scrolled to the bottom of the div
    if (div.scrollTop() + div.innerHeight() <= div[0].scrollHeight && !isLoading) {
        notesPage++;
        isLoading = true;
        loadNotesData();
    }
});

function loadNotesData() {
    $('.notes-loader1').attr('style', 'display:flex');
    notes_type = $('#notes_type').val();
    var case_range_date = $('#case_range_date').val();
    $.ajax({
        type: "GET",
        url: _NOTES + '?notes_type=' + notes_type + '&case_range_date=' + case_range_date,
        data: {
            'page': notesPage
        },
        success: function (response) {
            var htmlResponse = '';
            var res = response.data.data;
            if (res.length == 0) {
                htmlResponse += 'Nothing to show.'
            } else {
                for (var i = 0; i < res.length; i++) {
                    var urls = _URL_NOTES + "/" + res[i].patient_id;

                    htmlResponse += `<div class="d-flex align-items-center py-2 border-bottom">
    
                      <div class="ml-1">
                        <h6 class="mb-1"><a href="${urls}">Record #${res[i].patient_id} ${res[i].patient.first_name + ' ' + res[i].patient.last_name}</a></h6>
                        <p style="white-space: pre-wrap;">${res[i].message}</p>
                        <p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i>${res[i].user_details.agency_details.agency_name} ${res[i].created_date}</p>
                        <div class="row">
                        <p class="text-muted mb-0 tx-12" style="margin-left:12px;">${res[i].user_details.first_name + ' ' + res[i].user_details.last_name}</p>
                        
                        </div>
                        
                      </div>
    
                      </div>`;
                }
            }

            if (notesPage == 1) {
                $('#notes_section').html(htmlResponse);
            } else {
                $('#notes_section').append(htmlResponse);
                isLoading = false;
            }
            $('.notes-loader1').attr('style', 'display:none');

        }
    })
}