@include('include/header')
@include('include/sidebar')
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/jquery.min.js"></script>

  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<div class="main-panel">
	<div class="content-wrapper">
		<div class="row grid-margin-top">  
			<div class="col-12">  
								
				<div class="card">
		
			
					<div class="card-body" >
						<div class="row" style="margin:-10px;">
							<div class="col-md-8 card-title" style="margin-top:5px;">  <h4 class="card-title">Graph</h4></div>
							<div class="col-md-2">
								<div class="text-center">
								<select name="status_list" id="status_list" class="form-control select2" onchange="getChange()">
									<option value="">Select Status</option>
									<?php if(count($status_list) >0){
										foreach($status_list as $ld){
										?>
									<option value="<?php echo $ld->id;?>" <?php if($status == $ld->id){ echo "selected='selected'";}?>><?php echo $ld->name;?></option>
									<?php } } ?>
								</select>
							</div>
							
						</div>
						<div class="col-md-2">
								<div class="text-center">
								<select name="emcuser_list" id="emcuser_list" class="form-control select2" onchange="getChange()">
									<option value="">Select EMC User</option>
									<?php if(count($emcuser_list) >0){
										foreach($emcuser_list as $lds){
										?>
									<option value="<?php echo $lds->id;?>" <?php if($status == $lds->id){ echo "selected='selected'";}?>><?php echo $lds->name;?></option>
									<?php } } ?>
								</select>
							</div>
							
						</div>
						
						
						</div>
						<hr>
						<div class="row">
							<div class="col-12">
								 <div id="chart_all_div" style="height: 300px;"></div>
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
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/css/daterangepicker.css" />

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
$('.select2').select2();
google.charts.load('current', {packages: ['corechart', 'bar','line']});
  google.charts.setOnLoadCallback(drawMultCases);
   var chart, data, options;
    
 function drawMultCases() {
    
    var  status = $('#status_list').val();
    var  emcuser_list = $('#emcuser_list').val();

     var data = new google.visualization.DataTable();
      data.addColumn('string', 'UserName');
      data.addColumn('number', 'Total');

    
		  var options = {
	 
			hAxis: {
			  title: 'User name'
			 
			},
			vAxis: {
			  title: 'Total Case Count'
			},
			 legend: { position: "none" } 
		  };

		  var chart = new google.visualization.ColumnChart(document.getElementById('chart_all_div'));
			
		  $.ajax({
					url: "<?php echo URL::to('/');?>/emc-graph-ajax?status="+status+'&emcuser='+emcuser_list,
					dataType: "json",
					type: "GET",
					success: function (datalist) {
					 
						data.addRows(datalist.result);
					   chart.draw(data, options);
						google.visualization.events.addListener(chart, 'select', selectHandler); 
						function selectHandler(e)     { 
						console.log(datalist);
							var c = chart.getSelection()[0].row;
							console.log(c);
							if(emcuser_list !=''){
								emcs = emcuser_list;
							}else{
								emcs = datalist.Data[c].id;
							}
							var url = "<?php echo URL::to('/');?>/record?emcuser="+emcs+'&patient_status='+status;
							window.open(url, '_blank');
							
								
						}
					

					}
				});
		  
	}
	function getChange(){
		drawMultCases();
	}
  </script>