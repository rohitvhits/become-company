  @include('include/header')
    @include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/fullcalendar/fullcalendar.min.css">
    <!-- partial -->
      <!-- partial:../../partials/_settings-panel.html -->
   
      <!-- partial -->
      <!-- partial:../../partials/_sidebar.html -->
      
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
           
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Calendar.
				  <?php if(in_array($user->user_type_fk,array(3,4))){?>
				   <div class="pull-right">
					<select name="emclist" class="form-control" id="emc_id">
						<option value="">All</option>
						<?php if(!empty($userList)){ 
							foreach($userList as $kyy){
							?>
						<option value="<?php echo $kyy->id;?>" <?php if($emd_rep_id ==$kyy->id){ echo "selected='seleced'";}?>><?php echo ucfirst($kyy->first_name. ' '.$kyy->last_name);?></option>
						<?php } } ?>
					<select>
				  </div>
				  <?php } ?>
				  </h4>
				 
                  <div id="calendar" class="full-calendar"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
       

  @include('include/footer')
 <script src="<?php echo URL::to('/');?>/assets/vendors/moment/moment.min.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/vendors/fullcalendar/fullcalendar.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- Custom js for this page-->
  <script>
$(document).ready(function(){
var emc_id = $('#emc_id').val();
$.ajax({
	url: "<?php echo URL::to('/');?>/dashboard-calander",
	type: 'get', // Send post data
	data:{id:emc_id},
	async: false,
	success: function (s) {
		json_events = s;
	}
});
var calnedr = $('#calendar').fullCalendar({
        header: {
          left: 'prev,next today',
          center: 'title',
         right: 'month,basicWeek,agendaDay,listWeek'
        },
      defaultView: 'agendaWeek',
		navLinks: true, // can click d,ay/week names to navigate views
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        events:JSON.parse(json_events)
		
      })

});	  
</script>
<script>
	  $('#emc_id').change(function(e){
		var id = $('#emc_id').val();
	  window.location.href="<?php echo URL::to('/');?>/dashboard/calander?emclist="+id;
	  });
</script>