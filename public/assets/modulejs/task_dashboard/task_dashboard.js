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
        loadTaskCount();
        loadTaskListData();
        drawPriorityDataChart();
        drawPatientTaskChart();
        drawAssigneeTaskChart();
    })
});

loadTaskCount();
loadTaskListData();
function loadTaskCount() {
    $('.order-total-case-loader1').attr('style', 'display:flex');
    var range_date = $('#range_date').val();
    $.ajax({
        type: "GET",
        url: _TOTAL_TASK,
        data: {
            'range_date': range_date,
        },
        success: function (res) {
            var json = res.data;
            $('#total_progress').show();
            if(json.totalTask == 0){
                $('#total_progress').hide();
            }
            $('#total_task').html(json.totalTask)
            $('#total_pending').html(json.totalPending)
            $('#total_urgent').html(json.totalUrgent)
            $('#total_outstanding').html(json.totalOutstanding)
            $('#total_completed').html(json.totalCompleted)

            $('#total_pending').html('<a href="' + _TASK_LIST_SEARCH + '?status=Pending&created_task_date='+range_date+'" target="_blank">' + json.totalPending + '</a>')
            updateProgressBar('total_pending_progress', json.totalPending, json.totalTask)
            $('#total_completed').html('<a href="' + _TASK_LIST_SEARCH + '?status=Completed&created_task_date='+range_date+'" target="_blank">' + json.totalCompleted + '</a>')
            updateProgressBar('total_completed_progress', json.totalCompleted, json.totalTask)
            $('#total_urgent').html('<a href="' + _TASK_LIST_SEARCH + '?status=Urgent&created_task_date='+range_date+'" target="_blank">' + json.totalUrgent + '</a>')
            updateProgressBar('total_urgent_progress', json.totalUrgent, json.totalTask)
            $('#total_outstanding').html('<a href="' + _TASK_LIST_SEARCH + '?status=Outstanding&created_task_date='+range_date+'" target="_blank">' + json.totalOutstanding + '</a>')
            updateProgressBar('total_outstanding_progress', json.totalOutstanding, json.totalTask)
            $('.order-total-case-loader1').attr('style', 'display:none');
        }
    })
}

var chartResponse;
function drawPriorityDataChart() {
    range_date = $('#range_date').val();
    $('.priority-order-listing-loader1').attr('style', 'display:flex');
    $('#priority_donut_chart').html('');
    $('#priority-no-data').css('display', 'none');
    $.ajax({
        url: _TASK_PRIORITY_CHART_DATA + '?range_date=' + range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#priority-no-data').css('display', 'block');
                $('.priority-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [['Priority', 'Total']];
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                // Loop through the data and add it to the chartData array
                data.forEach(function (item) {
                    chartData.push([item.name, item.total]);
                });
                var data = google.visualization.arrayToDataTable(chartData);
                var options = {
                    colors: colors,
                    pieHole: 0.4,
                };
                var chartd = new google.visualization.PieChart(document.getElementById('priority_donut_chart'));
                chartd.draw(data, options);
                google.visualization.events.addListener(chartd, 'select', selectHandlers);

                function selectHandlers(e) {
                    var selection = chartd.getSelection();                    
                    var c = selection[0].row;
                    if (selection.length > 0) {
                        var Url = _TASK_LIST_SEARCH + '?priority=' + datalist.data[c].name + '&created_task_date=' + range_date
                        window.open(Url, '_blank');
                    }
                }
                $('.priority-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

function loadTaskListData(page) {
    $('.task-listing-loader1').attr('style', 'display:flex');
    var range_date = $('#range_date').val();
    $.ajax({
        type: "GET",
        url: _TASK_LIST_DATA,
        data: {
            'page': page,
            'range_date': range_date
        },
        success: function (response) {
            $('#task_list').html("")
            $('#task_list').html(response);
            $('.task-listing-loader1').attr('style', 'display:none');
        }
    })
}

var chartResponse;
function drawPatientTaskChart() {
    range_date = $('#range_date').val();
    $('.patient-order-listing-loader1').attr('style', 'display:flex');
    $('#patient_view_chart').html('');
    $('#patient-no-data').css('display', 'none');
    $.ajax({
        url: _PATIENT_WISE_TASK_DATA + '?range_date=' + range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#patient-no-data').css('display', 'block');
                $('.patient-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Patient');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                datalist.data.forEach(function (item, val) {
                    chartData.push([item.name, item.total, colors[val]]);
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
                    bar: { groupWidth: "200%" }, // Increase the width of bars
                    chartArea: {
                        left: 50,
                    },
                    vAxis: {
                        title: '',
                        gridlines: { count: 5 },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                    width: 800,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('patient_view_chart'));
                chart.draw(datas, options);
                $('.patient-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

var chartResponse;
function drawAssigneeTaskChart() {
    range_date = $('#range_date').val();
    $('.assignee-order-listing-loader1').attr('style', 'display:flex');
    $('#assignee_chart').html('');
    $('#assignee-no-data').css('display', 'none');
    $.ajax({
        url: _ASSIGNEE_WISE_TASK_DATA + '?range_date=' + range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#assignee-no-data').css('display', 'block');
                $('.assignee-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Patient');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                datalist.data.forEach(function (item, val) {
                    chartData.push([item.name, item.total, colors[val]]);
                });
                datas.addRows(chartData);
                var options = {
                    title: '',
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
                    chartArea: {
                        left: 50,
                    },
                    width: 1000,
                    height: 295
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('assignee_chart'));
                chart.draw(datas, options);
                google.visualization.events.addListener(chart, 'select', selectHandlers);

                function selectHandlers(e) {
                    var selection = chart.getSelection();                    
                    var c = selection[0].row;
                    if (selection.length > 0) {
                        var Url = _TASK_LIST_SEARCH + '?user_id=' + datalist.data[c].id
                        window.open(Url, '_blank');
                    }
                }
                $('.assignee-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
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
    loadTaskListData(page);
});



