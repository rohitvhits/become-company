$(document).ready(function() {
  var dataJson; 
  loadChart();
  });
  var chartResponse;
  function loadChart(){
   
    var agencyId = $('#agencyId').val();
    $.ajax({
        url: _CHATURL, 
        type: 'GET',
        data:{
          agency_id:agencyId,
          'record_type':$('#record_type').val(),
          'location_id':$('#location_id').val(),
          'created_date':$('#created_date').val(),
          'last_updated_date':$('#last_updated_date').val()
        },
        dataType: 'json',
        success: function(data) { 
          dataJson = data;
                // If no records, show a message instead of a chart
                if (!data || data.length === 0) {
                  $('.total-class').text(0);
                  if (chartResponse) {
                      chartResponse.destroy(); // Remove any existing chart
                  }
                  let ctx = document.getElementById("pieChart").getContext("2d");
                  ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height); // Clear chart area
                  ctx.font = "16px Arial";
                  ctx.textAlign = "center";
                  ctx.fillText("No data available", ctx.canvas.width / 2, ctx.canvas.height / 2);
                  return;
              }
              data.forEach(countData => {
                var statusID= countData.status_id;
                $(`#total_${statusID}`).text(countData.count);
               });
         // Generate random colors for each record
         let backgroundColors = [];
         let borderColors = [];
                // Soft / pastel color palette generator
                function getPastelColor() {
                  let r = Math.floor((Math.random() * 127) + 100); // 100–227 range for softness
                  let g = Math.floor((Math.random() * 127) + 100);
                  let b = Math.floor((Math.random() * 127) + 100);
                  return `rgba(${r}, ${g}, ${b}, 0.5)`; // semi-transparent
                }
         data.forEach(() => {
          let pastel = getPastelColor();
          let solid = pastel.replace('0.5', '1'); // border fully opaque
          backgroundColors.push(pastel);
          borderColors.push(solid);
         });
            var doughnutPieData = {
                datasets: [{
                    data: data.map(item => item.count),
                    backgroundColor: backgroundColors,
                    borderColor: borderColors
                }],
                labels: data.map(item => item.status_name),
            };
            if (chartResponse) {
              chartResponse.destroy();
          }
            chartResponse =  doughnutPieOptions(doughnutPieData, doughnutPieOptions);
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
  }
  function doughnutPieOptions(data, options) {
    var ctx = document.getElementById('pieChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options
    });
    
    // Add a click event listener to the chart to handle redirection
    ctx.canvas.addEventListener('click', function (evt) {
      var agencyId = $('#agencyId').val();
        var activeElements = chart.getElementsAtEventForMode(evt, 'point', chart.options);
        if (activeElements.length > 0) {
            var status = data.labels[activeElements[0]._index];
            var url = "/patient?status=" + status+'&agency_fk='+agencyId+'&type='+$('#record_type').val()+'&locationId='+$('#location_id').val();
            window.open(url, '_blank');
            // window.location.href = url; 
        }
    });
  
    return chart;
  }
    var areaData = {
      labels: ["2013", "2014", "2015", "2016", "2017"],
      datasets: [{
        label: '# of Votes',
        data: [12, 19, 3, 5, 2, 3],
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
          'rgba(255,99,132,1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1,
        fill: true, // 3: no fill
      }]
    };
  
    var areaOptions = {
      plugins: {
        filler: {
          propagate: true
        }
      }
    }
  
    var multiAreaData = {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
          label: 'Facebook',
          data: [8, 11, 13, 15, 12, 13, 16, 15, 13, 19, 11, 14],
          borderColor: ['rgba(255, 99, 132, 0.5)'],
          backgroundColor: ['rgba(255, 99, 132, 0.5)'],
          borderWidth: 1,
          fill: true
        },
        {
          label: 'Twitter',
          data: [7, 17, 12, 16, 14, 18, 16, 12, 15, 11, 13, 9],
          borderColor: ['rgba(54, 162, 235, 0.5)'],
          backgroundColor: ['rgba(54, 162, 235, 0.5)'],
          borderWidth: 1,
          fill: true
        },
        {
          label: 'Linkedin',
          data: [6, 14, 16, 20, 12, 18, 15, 12, 17, 19, 15, 11],
          borderColor: ['rgba(255, 206, 86, 0.5)'],
          backgroundColor: ['rgba(255, 206, 86, 0.5)'],
          borderWidth: 1,
          fill: true
        }
      ]
    };
  
    var multiAreaOptions = {
      plugins: {
        filler: {
          propagate: true
        }
      },
      elements: {
        point: {
          radius: 0
        }
      },
      scales: {
        xAxes: [{
          gridLines: {
            display: false
          }
        }],
        yAxes: [{
          gridLines: {
            display: false
          }
        }]
      }
    }
  
    var scatterChartData = {
      datasets: [{
          label: 'First Dataset',
          data: [{
              x: -10,
              y: 0
            },
            {
              x: 0,
              y: 3
            },
            {
              x: -25,
              y: 5
            },
            {
              x: 40,
              y: 5
            }
          ],
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)'
          ],
          borderColor: [
            'rgba(255,99,132,1)'
          ],
          borderWidth: 1
        },
        {
          label: 'Second Dataset',
          data: [{
              x: 10,
              y: 5
            },
            {
              x: 20,
              y: -30
            },
            {
              x: -25,
              y: 15
            },
            {
              x: -10,
              y: 5
            }
          ],
          backgroundColor: [
            'rgba(54, 162, 235, 0.2)',
          ],
          borderColor: [
            'rgba(54, 162, 235, 1)',
          ],
          borderWidth: 1
        }
      ]
    }
  
    var scatterChartOptions = {
      scales: {
        xAxes: [{
          type: 'linear',
         position: 'bottom' 
        }]
      }
    }
    // Get context with jQuery - using jQuery's .get() method.
    if ($("#barChart").length) {
      var barChartCanvas = $("#barChart").get(0).getContext("2d");
      // This will get the first returned node in the jQuery collection.
      var barChart = new Chart(barChartCanvas, {
        type: 'bar',
        data: data,
        options: options
    });
  }
var resetFilterBtn = document.getElementById('resetFilterBtn');
  resetFilterBtn.addEventListener('click', function() {
    // Reset the select2 elements
    $('#agencyId').val(null).trigger('change');
     $('#record_type').val('Patient').trigger('change'); 
    $('#location_id').val('');
    $('#created_date').val('').trigger("change");
    $('#last_updated_date').val('').trigger("change");
  });

  $(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#created_date').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select Date': [start, end],
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
            loadChart();
    })

    $('#created_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });

      $('#last_updated_date').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Select Date': [start, end],
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
        $('#last_updated_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
            loadChart();
    })

    $('#last_updated_date').on('apply.daterangepicker', function(ev, picker) {
        // Detect "Select Date"
        if (picker.chosenLabel === 'Select Date') {
            $(this).val('');
        } else {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }
    });
});

$('#exportBtn').on('click', function() {
   
      // Pick only status_name & count
      let filteredData = dataJson.map(item => ({
        "Status": item.status_name,
        "Count": item.count
    }));

    // Create worksheet from filtered data
      let ws = XLSX.utils.json_to_sheet(filteredData);
    // Create a new workbook
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Detailed Refusals");
   
    // Export Excel file
    XLSX.writeFile(wb, "detailed_refusals.xlsx");
});