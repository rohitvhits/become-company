$(document).ready(function() {
  loadChart();
});
var chartResponse;
function loadChart(){
 
  var agencyId = $('#agencyId').val();
  $.ajax({
      url: _CHATURL, 
      type: 'GET',
      data:{
        agencyId:agencyId,
        'record_type':$('#record_type').val(),
        'location_id':$('#location_id').val()
      },
      dataType: 'json',
      success: function(data) { 

          var doughnutPieData = {
              datasets: [{
                  data: data.map(item => item.count),
                  backgroundColor: [
                    'rgb(64,155,234, 0.5)',
                    'rgba(1, 53, 128, 0.5)',
                    'rgba(164, 115, 120,0.5)',
                    'rgba(59,176,1, 0.5)',
                    'rgba(139,0,0, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(255, 205, 86, 0.5)',
                    'rgba(121,135,161, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(121,135,161, 0.5)'
                  ],
                  borderColor: [
                    'rgb(64,155,234, 0.5)',
                    'rgba(1, 53, 128, 0.5)',
                    'rgba(164, 115, 120, 0.5)',
                    'rgba(59,176,1, 0.5)',
                    'rgba(139,0,0, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(255, 205, 86, 0.5)',
                    'rgba(121,135,161, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(121,135,161, 0.5)'
                        
                  ],
              }],
              labels: data.map(item => item.status),
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