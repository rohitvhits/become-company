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
        loadEsignCount();
        drawStatusChart();
        drawTemplateDataChart();
        esignTodayData();
        drawReviewdEsignDataChart();
        drawCreatedEsignDataChart();
    })
});

$('#type_id').on('change',function(){
    loadEsignCount();
    drawStatusChart();
    drawTemplateDataChart();
    esignTodayData();
    drawReviewdEsignDataChart();
    drawCreatedEsignDataChart();
});
esignTodayData();
loadEsignCount();
function loadEsignCount() {
    var range_date = $('#range_date').val();
    var type_id = $('#type_id').val();
    $('.order-total-case-loader1').attr('style', 'display:flex');
    $.ajax({
        type: "GET",
        url: _TOTAL_ESIGN_DATA,
        data: {
            'range_date': range_date,
            'type' : type_id
        },
        success: function (res) {
            var json = res.data;
            $('#total_progress').show();
            if(json.totalEsign == 0){
                $('#total_progress').hide();
            }
            $('#total_esign').html(json.totalEsign)
            $('#total_pending').html(json.totalPendingCount)
            updateProgressBar('total_pending_progress', json.totalPendingCount, json.totalEsign)
            
            $('#total_completed').html(json.totalCompletedCount)
            updateProgressBar('total_completed_progress', json.totalCompletedCount, json.totalEsign)

            $('#total_approved').html(json.toalApprovedCount)
            updateProgressBar('total_approved_progress', json.toalApprovedCount, json.totalEsign)
            
            $('#total_rejected').html(json.totalRejectedCount)
            updateProgressBar('total_rejected_progress', json.totalRejectedCount, json.totalEsign)           
            $('.order-total-case-loader1').attr('style', 'display:none');
        }
    })
}

function esignTodayData(page) {
    $('.esign-listing-loader1').attr('style', 'display:flex');
    var type_id = $('#type_id').val();
    $.ajax({
        type: "GET",
        url: ESIGN_DATA,
        data: {
            'page': page,
            'type': 'today',
            'type_id' : type_id
        },
        success: function (response) {
            $('#esign_data').html("")
            $('#esign_data').html(response);
            $('.esign-listing-loader1').attr('style', 'display:none');
        }
    })
}

var chartResponse;
function drawStatusChart() {

    type_id = $('#type_id').val();
    agency_id = $('#agency_id').val();
    location_id = $('#location_id').val();
    range_date = $('#range_date').val();
    $('.service-order-listing-loader1').attr('style', 'display:flex');
    $('#pieChartNew').html('');
    $('#service-no-data').css('display', 'none');
    $.ajax({
        url: _GRAPH_TOTAL_STATUS + '?type=' + type_id + '&location_id=' + location_id + '&agency_id[]=' + agency_id + '&range_date=' + range_date,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#service-no-data').css('display', 'block');
                $('.service-order-listing-loader1').attr('style', 'display:none');
            } else {
                var chartData = [['Status', 'Total']];
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                // Loop through the data and add it to the chartData array
                data.forEach(function (item) {
                    chartData.push([item.name, item.count]);
                });
                var data = google.visualization.arrayToDataTable(chartData);

                var options = {
                    is3D: true,
                    colors: colors
                };

                var chartd = new google.visualization.PieChart(document.getElementById('pieChartNew'));
                chartd.draw(data, options);
                $('.service-order-listing-loader1').attr('style', 'display:none');
            }
        }
    });
}

function drawTemplateDataChart() {
    var range_date = $('#range_date').val();
    var type_id = $('#type_id').val();
    $('.templete-usage-loader1').attr('style', 'display:flex');
    $('#columnChart').html('');
    $('#template-no-data').css('display', 'none');
    $.ajax({
        url: _TEMPLATE_GRAPH + '?range_date=' + range_date+'&type='+type_id,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#template-no-data').css('display', 'block');
                $('.templete-usage-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Template');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                datalist.data.forEach(function (item, val) {
                    chartData.push([item.name, item.count, colors[val]]);
                });
                datas.addRows(chartData);
                var options = {
                    title: '',
                    isStacked:true,
                    is3D: true,
                    // chartArea: { width: '70%' },
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
                            max: 1000 // Set the vertical axis to range from 0 to 1,000.
                        },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                    chartArea: {
                        left: 100,
                    },
                    height: 300,
                    width: 600,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('columnChart'));

                chart.draw(datas, options);
                $('.templete-usage-loader1').attr('style', 'display:none');
            }
        }
    });
}

function drawCreatedEsignDataChart() {
    var range_date = $('#range_date').val();
    var type_id = $('#type_id').val();
    $('.created-esign-loader1').attr('style', 'display:flex');
    $('#createChart').html('');
    $('#created-no-data').css('display', 'none');
    $.ajax({
        url: _CREATED_ESIGN_GRAPH + '?range_date=' + range_date+'&type='+type_id,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#created-no-data').css('display', 'block');
                $('.created-esign-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Template');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                datalist.data.forEach(function (item, val) {
                    chartData.push([item.name, item.count, colors[val]]);
                });
                datas.addRows(chartData);
                var options = {
                    title: '',
                    isStacked:true,
                    is3D: true,
                    // chartArea: { width: '70%' },
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
                    vAxis: {
                        title: '',
                        gridlines: { count: 5 },
                        viewWindow: {
                            min: 0,
                            max: 1000 // Set the vertical axis to range from 0 to 1,000.
                        },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                    chartArea: {
                        left: 100,
                    },
                    height: 300,
                    width: 600,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('createChart'));

                chart.draw(datas, options);
                $('.created-esign-loader1').attr('style', 'display:none');
            }
        }
    });
}

function drawReviewdEsignDataChart() {
    var range_date = $('#range_date').val();
    var type_id = $('#type_id').val();
    $('.reviewed-esign-loader1').attr('style', 'display:flex');
    $('#reviewChart').html('');
    $('#review-no-data').css('display', 'none');
    $.ajax({
        url: _REVIEW_ESIGN_GRAPH + '?range_date=' + range_date+'&type='+type_id,
        dataType: "json",
        type: "GET",
        success: function (datalist) {
            var data = datalist.data;
            if (data.length == 0) {
                if (chartResponse) {
                    chartResponse.destroy();
                }
                $('#review-no-data').css('display', 'block');
                $('.reviewed-esign-loader1').attr('style', 'display:none');
            } else {
                var chartData = [];
                var colors = [];
                var colorpallate = (generateColorPalette(data.length));
                var colors = colors.concat(colorpallate);
                var datas = new google.visualization.DataTable();
                datas.addColumn('string', 'Template');
                datas.addColumn('number', '');
                datas.addColumn({ type: 'string', role: 'style' });
                datalist.data.forEach(function (item, val) {
                    chartData.push([item.name, item.count, colors[val]]);
                });
                datas.addRows(chartData);
                var options = {
                    title: '',
                    isStacked:true,
                    is3D: true,
                    // chartArea: { width: '70%' },
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
                    vAxis: {
                        title: '',
                        gridlines: { count: 5 },
                        viewWindow: {
                            min: 0,
                            max: 1000 // Set the vertical axis to range from 0 to 1,000.
                        },
                        format: 'short',  // Format the numbers with "K" (e.g., 1K).
                    },
                    chartArea: {
                        left: 100,
                    },
                    height: 300,
                    width: 600,
                };
                var chart = new google.visualization.ColumnChart(document.getElementById('reviewChart'));

                chart.draw(datas, options);
                $('.reviewed-esign-loader1').attr('style', 'display:none');
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
    var nurl = $(this).attr('href').split('type=');
    console.log(nurl);
    var redirection = nurl[1].split('&');
    if (redirection[0] == 'today') {
        console.log(nurl);
        esignTodayData(page);
    }
});

