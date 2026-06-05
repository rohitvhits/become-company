@include('include/header')
@include('include/sidebar')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>

<div class="main-panel">
	<div class="content-wrapper">
		<div class="row grid-margin-top">  
			<div class="col-12">  
								
				<div class="card">
		
			
					<div class="card-body" >
						<div class="row" style="margin:-10px;">
							<div class="col-md-10 card-title" style="margin-top:5px;">  <h4 class="card-title">Agency Graph</h4></div>
							<div class="col-md-2">
								<div class="text-center">
								 <input type="text" id="datepicker_id" class="form-control datepicker_date" value="<?php echo $current_date;?>">
							</div>
							
						</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								 <div id="chart_all_div" style="height: 700px;"></div>
							</div>
						  </div>
						</div>
						
					</div>
					
				</div>
   
			</div>
<div class="row grid-margin-top">  
			<div class="col-12">  
								
				<div class="card">
		
			
					<div class="card-body" >
						<div class="row" style="margin:-10px;">
							<div class="col-md-10 card-title" style="margin-top:5px;">  <h4 class="card-title">EMC Graph</h4></div>
							
						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								 <div id="chart_all_div1" style="height: 700px;"></div>
							</div>
						  </div>
						</div>
						
					</div>
					
				</div>
   
			</div>			
		
		</div>
@include('include/footer')

 <script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/css/daterangepicker.css" />

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
google.charts.load('current', {packages: ['corechart', 'bar','line']});
  google.charts.setOnLoadCallback(drawMultCases);
  google.charts.setOnLoadCallback(drawMultEMC);
   var chart, data, options;
    $(document).ready(function(){
		
	});
 function drawMultCases() {
    
    var  created_date = $('#datepicker_id').val();

     var data = new google.visualization.DataTable();
      data.addColumn('string', '');
      data.addColumn('number', 'Completed');
	 data.addColumn('number', 'Filed');
	 data.addColumn('number', 'Closed');
     
    
		  var options = {
			chartArea: {width: '50%'},
        hAxis: {
          title: '',
          minValue: 0,
          textStyle: {
            bold: true,
            fontSize: 12,
            color: '#4d4d4d'
          },
          titleTextStyle: {
            bold: true,
            fontSize: 18,
            color: '#4d4d4d'
          }
        },
        vAxis: {
          title: 'Agency',
          textStyle: {
            fontSize: 14,
            bold: true,
            color: '#848484'
          },
          titleTextStyle: {
            fontSize: 14,
            bold: true,
            color: '#848484'
          }
        }
      };

		  var chart = new google.visualization.BarChart(document.getElementById('chart_all_div'));
			
		  $.ajax({
					url: "<?php echo URL::to('/');?>/graph-ajax?created_date="+created_date,
					dataType: "json",
					type: "GET",
					success: function (datalist) {
					 
						data.addRows(datalist.result);
						chart.draw(data, options);
						google.visualization.events.addListener(chart, 'select', selectHandler); 
						function selectHandler(e)     { 
						console.log(datalist);
							var c = chart.getSelection()[0].row;
							if(chart.getSelection()[0].column ==1){
								var patienr = 16;
							}else if(chart.getSelection()[0].column ==2){
										var patienr = 62;
							}else{
								var patienr = 14;
							}
							window.open("<?php echo URL::to('/');?>/record?agency_fk1="+datalist.Data[c].USERID+'&patient_status='+patienr+"&created_date="+created_date, '_blank');
								
						}
					

			  }
			  
			});
			
		  
	}
	
	function drawMultEMC() {
    
    var  created_date = $('#datepicker_id').val();

     var data = new google.visualization.DataTable();
      data.addColumn('string', '');
      data.addColumn('number', 'Completed');
     data.addColumn('number', 'Filed');
     data.addColumn('number', 'Closed');
    
    
		  var options = {
			chartArea: {width: '50%'},
        hAxis: {
          title: '',
          minValue: 0,
          textStyle: {
            bold: true,
            fontSize: 12,
            color: '#4d4d4d'
          },
          titleTextStyle: {
            bold: true,
            fontSize: 18,
            color: '#4d4d4d'
          }
        },
        vAxis: {
          title: 'EMC User',
          textStyle: {
            fontSize: 14,
            bold: true,
            color: '#848484'
          },
          titleTextStyle: {
            fontSize: 14,
            bold: true,
            color: '#848484'
          }
        }
      };

		  var chart = new google.visualization.BarChart(document.getElementById('chart_all_div1'));
			
		  $.ajax({
					url: "<?php echo URL::to('/');?>/graph-ajax-emc?created_date="+created_date,
					dataType: "json",
					type: "GET",
					success: function (datalist) {
					 
				data.addRows(datalist.result);
			   chart.draw(data, options);
					google.visualization.events.addListener(chart, 'select', selectHandler); 
						function selectHandler(e)     { 
						console.log(datalist);
							var c = chart.getSelection()[0].row;
							if(chart.getSelection()[0].column ==1){
								var patienr = 16;
							}else if(chart.getSelection()[0].column ==2){
										var patienr = 62;
							}else{
								var patienr = 14;
							}
							window.open("<?php echo URL::to('/');?>/record?emcuser="+datalist.Data[c].USERID+'&patient_status='+patienr+"&created_date="+created_date, '_blank');
								
						}
					
							

			  }
			});
		  
	}
	
	$(function () {
			var start = moment().subtract(0, 'days');
			var end = moment();
			$('.datepicker_date').daterangepicker({
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
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
					'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
					'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
					'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
				}
			}, function (chosen_date, end_date) {

				$('.datepicker_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
				drawMultCases();
				drawMultEMC();
			})
});
  </script>