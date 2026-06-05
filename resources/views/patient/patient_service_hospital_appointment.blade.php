<!DOCTYPE html>
<html lang="en">

<head>

  <!-- Required meta tags -->

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>NY Best Medical Care PC : Appointment</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/fonts/materialdesignicons-webfont.eot">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/fonts/materialdesignicons-webfont.ttf">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/fonts/materialdesignicons-webfont.woff">
  <link href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/jquery-ui.css" rel="stylesheet">
  <!-- base:css -->

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/mdi/css/materialdesignicons.min.css">

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/css/vendor.bundle.base.css">

  <!-- endinject -->

  <!-- plugin css for this page -->

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/jqvmap/jqvmap.min.css">
  

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/flag-icon-css/css/flag-icon.min.css">

  <!-- End plugin css for this page -->

  <!-- inject:css -->

  <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/css/vertical-layout-light/style.css">

  <!-- endinject -->
  <script src="<?php echo URL::to('/'); ?>/assets/js/jquery.min.js"></script>

  <link rel="shortcut icon" href="<?php echo URL::to('/'); ?>/img/logo.png" />
  
  <style>
    .compact-view .form-control {
      padding: 0 !important;
      height: 24px;
    }

    .compact-view td {
      padding: 5px 10px;
    }
	.img_new{
		/* width: 200px; */
    width: 100%;
		/* margin-left:15%;
		margin-top: -19%; */
    height: 100%;
    object-fit: contain;
    
	}
  .hide{
    display: none;
  }
  .brand-logo{
    width: 100%;
    height: 200px;
   /*  margin-top: -6%; */
  }
  </style>



</head>



<body class="sidebar-dark sidebar-fixed">



  <!--Header-part-->

  <div class="container-scroller">

    <!-- partial:partials/_navbar.html -->


    <script>
      $('a[data-notif-id]').click(function() {

        var notif_id = $(this).data('notifId');
        var targetHref = $(this).data('href');
        $.ajax({
          async: false,
          global: false,
          type: "GET",
          url: "<?php echo URL::to('/'); ?>/NotifMarkAsRead",
          data: {
            'notif_id': notif_id
          },
          succes: function(response) {
            if (response == 'success') {
              window.location.href = targetHref;
            } else {
              alert("Sorry, something went wrong. Please try again.");
              return false;
            }
          }

        })
      });
    </script>

    <!--close-Header-part-->





    <!--top-Header-menu-->



    <!--close-top-Header-menu-->
    <div class="container-fluid page-body-wrapper">

      <!-- partial -->
      <div class="main-panel" style="margin-left:-3px !important">
        <div class="content-wrapper">
		        <div class="brand-logo">
              
              <img class="img_new"src="{{ asset('/assets/ImportedPhoto.744644306.492802.jpeg')}}" alt="logo">
              
            </div>
			  <p class="text-muted mb-3 tx-12">Hey <?php 
					$fname = '';
					$lname = '';
					if(isset($query->first_name) && $query->first_name !=''){
						$fname = ucfirst($query->first_name);
					}
					if(isset($query->last_name) && $query->last_name !=''){
						$lname = ucfirst($query->last_name);
					}
					//echo $fname.' '.$lname;
					echo $fname;
						?>,</p>
				
			<?php 
				if(isset($query->status) && $query->status =='booked'){
		?>		
			<p class="text-muted mb-3 tx-12"> Your booking is scheduled  on  <?php echo date('m-d-Y',strtotime($query->appointment_date));?>. Below is appointment details :</p>		
			<div class="">
				<div class="form-group">
					<p class="text-muted mb-1 tx-12">Location :<?php echo $locationname;?></p>
					<p class="text-muted mb-1 tx-12">Date :<?php echo date('m-d-Y',strtotime($query->appointment_date));?></p>
					<p class="text-muted mb-1 tx-12">Time :<?php echo $appointmentTimes;?></p>
					
					
					
				</div>
			</div>
			<a href="javascript:void(0)" onclick="getReschedule()">Click here to reschedule.</a>
		<?php }else{ ?>		
		<p class="text-muted mb-3 tx-12">As you know, in order to continue employment with <?php echo $query->agency_name;?>, you must keep up to date with all medical requirements.<?php echo $query->agency_name;?> works together with NY Best Medical, a local medical provider. Please schedule your doctor appointment below.</p>		
		
		<?php } ?>

  
          <div class="row" >
			
            <div id="add_form" class="col-12 grid-margin" style="<?php if(isset($query->status) && $query->status =='booked'){ ?>display:none<?php } ?>">
              <div class="card">
                <div class="card-body">
                  
                  <form class="form-sample" action='<?php echo URL::to('/'); ?>/patient/appointment-save' name="adduser" method="post" onsubmit="return validation();">
                    <input type="hidden" name="_token" value="J5VVQ3C1J3I1nj4HqhcwQBSliEejM8u3C6T2x4M8">
                    <input type="hidden" name="key" value="<?php echo $key; ?>">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Location <span class="error mt-2 text-danger">*</span></label>
                          <div class="col-sm-12">
                            <div class="row">
                            <?php foreach ($location_list as $vals) { ?>
                              <div class="col-md-3">
                            <div class="form-check">
                                          <label class="form-check-label">
                                            <input onclick="getTimeSearch(<?php echo $vals->id; ?>,<?php echo $vals->walkin;?>)" type="radio" id="<?php echo $vals->id; ?>" class="form-check-input" name="location_id" id="optionsRadios1" data-id="<?php echo $vals->walkin;?>" value="<?php echo $vals->id; ?>">
                                            <?php echo $vals->address1. ', '.$vals->city.', '.$vals->state; ?>
                                          <i class="input-helper"></i></label>
                                        </div>
                              </div>
                            <?php } ?>
                           
                            </div>
                            <span id="agency_name_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
                      
                    </div>
                    <div class="row hide" id="imesSelectedId">
                      <div class="col-md-6  hide" id="datesSelectedId">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Date <span class="error mt-2 text-danger">*</span></label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control datepicker" autocomplete="off" placeholder="Choose date" id="middle_name_id" name="start_date" value="" onchange="getTimeSearch()">
                            <span id="middle_name_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Time<span class="error mt-2 text-danger">*</span></label>
                          <div class="col-sm-9">
                            <select name="time_id" class="form-control" id="time_id">
                              <option value="">Select Time</option>
                              
                            </select>
                            <span id="last_name_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
                    </div>

                <span id="selected_error_msg" class="text-danger"></span>
                    <button type="submit" class="btn btn-primary mr-2 hide" id="ssid">Save</button>
                  </form>
                </div>
              </div>
            </div>
			<div id="edit_form" class="col-12 grid-margin" style="display:none">
              <div class="card">
                <div class="card-body">
                  
                  <form class="form-sample" action='<?php echo URL::to('/'); ?>/patient/appointment-update' name="adduser" method="post" onsubmit="return validation1();">
                    <input type="hidden" name="_token" value="J5VVQ3C1J3I1nj4HqhcwQBSliEejM8u3C6T2x4M8">
                    <input type="hidden" name="key" value="<?php echo $key; ?>">
                    <input type="hidden" name="id" value="<?php echo $query->id; ?>">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group row">
                          <label class="col-sm-12 col-form-label">Location<span class="error mt-2 text-danger">*</span></label>
                          <div class="col-sm-12">
                          <div class="row">
						  <?php foreach ($location_list as $vals) { ?>
                
                  <div class="col-md-3">
                    <div class="form-check">
                            <label class="form-check-label">
                              <input onclick="getTimeSearchEdit(<?php echo $vals->id; ?>,<?php echo $vals->walkin; ?>)" type="radio" id="<?php echo $vals->id; ?>" class="form-check-input" name="location_id_edit" id="optionsRadios1" value="<?php echo $vals->id; ?>">
                              <?php echo $vals->address1. ', '.$vals->city.', '.$vals->state; ?>
                            <i class="input-helper"></i></label>
                          </div>
                  </div>
               
						  
						  <?php } ?>
              </div>
                            

                            <span id="agency_name_edit_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
                      
                    </div>
                    <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Date</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control datepicker" autocomplete="off" placeholder="Choose date" id="middle_name_id_edit" name="start_date_edit" value="" onchange="getTimeSearchEdit()">
                            <span id="middle_name_edit_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Time<span class="error mt-2 text-danger">*</span></label>
                          <div class="col-sm-9">
                            <select name="time_id_edit" class="form-control" id="time_id_edit">
                              <option value="">Select Time</option>
                              
                            </select>
                            <span id="last_name_edit_error" class="error mt-2 text-danger"></span>
                          </div>
                        </div>
                      </div>
                    </div>


                    <button type="submit" class="btn btn-primary mr-2">Update</button>
                  </form>
                </div>
              </div>
            </div>
			<div class="col-12 grid-margin" style="<?php 
				if(isset($query->status) && $query->status =='booked'){?>display:none<?php } ?>">
				<div class="" id="perfers_id">
					<a href="javascript:void(0)" id="close_account"><p class="text-muted mb-3 tx-12">I perfer not to schedule my appointment through NY Best, I will use my own provider.</p></a>
				</div>
			</div>
          </div>
        </div>
        <!-- /Main Content -->

        <!-- /Page Content -->

        <script>
          function validation() {

            var temp = 0;

            var location_id = $('input[name="location_id"]').is(":checked");
            var middle_name_id = $('#middle_name_id').val();

            var time_id = $('#time_id').val();

            $("#agency_name_error").html("");
            $("#middle_name_error").html("");

            $("#last_name_error").html("");

            if (location_id == false) {
              $('#agency_name_error').html("Required");
              temp++;
            }

            if (time_id == "") {
              $('#last_name_error').html("Required");
              temp++;
            }


            if (middle_name_id == "") {
              $('#middle_name_error').html("Required");
              temp++;
            }

            if (temp == 0) {

              return true;

            } else {

              return false;

            }

          }
		  function validation1() {

            var temp = 0;

            var location_id = $('input[name="location_id_edit"]').is(":checked");
            var middle_name_id = $('#middle_name_id_edit').val();

            var time_id = $('#time_id_edit').val();

            $("#agency_name_edit_error").html("");
            $("#middle_name_edit_error").html("");

            $("#last_name_edit_error").html("");

            if (location_id == false) {
              $('#agency_name_edit_error').html("Required");
              temp++;
            }

            if (time_id == "") {
              $('#last_name_edit_error').html("Required");
              temp++;
            }


            if (middle_name_id == "") {
              $('#middle_name_edit_error').html("Required");
              temp++;
            }

            if (temp == 0) {

              return true;

            } else {

              return false;

            }

          }
        </script>
        <!-- Date Picker -->
        <script src="<?php echo URL::to('/'); ?>/js/jquery.min.js"></script>
        <link href="<?php echo URL::to('/'); ?>/css/jquery-ui.css">
        <script src="<?php echo URL::to('/'); ?>/assets/js/jquery-ui.min.js"></script>
        <script>
          $("#bill_date").datepicker();

          $('.datepicker').datepicker({
            minDate: 1,
		    beforeShowDay: $.datepicker.noWeekends
        
          });
        </script>


        <!-- End Date Picker -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"> 2019 - {{date('Y')}} &copy; Nybest Medical.
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="<?php echo URL::to('/'); ?>/assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  
  <script src="<?php echo URL::to('/'); ?>/assets/js/jquery-ui.min.js"></script>
 
  <link href="<?php echo URL::to('/');?>/assets/libs/sweetalert/sweetalert.css" rel="stylesheet"/>
<script src="<?php echo URL::to('/');?>/assets/libs/sweetalert/sweetalert.min.js"></script>

  <!-- End custom js for this page-->
</body>
<!-- Mirrored from www.urbanui.com/yoraui/template/demo/vertical-default-light/ by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 13 Dec 2019 05:50:45 GMT -->

</html>
<script type="text/javascript">
$('.date').datepicker();
  // This function is called from the pop-up menus to transfer to
  // a different page. Ignore if the value returned is a null string:
  function goPage(newURL) {
    // if url is empty, skip the menu dividers and reset the menu selection to default
    if (newURL != "") {
      // if url is "-", it is this page -- reset the menu:
      if (newURL == "-") {
        resetMenu();
      }
      // else, send page to designated URL            
      else {
        document.location.href = newURL;
      }
    }
  }
  // resets the menu selection upon entry to this page:
  function resetMenu() {
    document.gomenu.selector.selectedIndex = 2;
  }
  function getTimeSearch(){
			var location_id = $('input[name="location_id"]:checked').val();
			var walkIn = $('input[name="location_id"]:checked').attr('data-id');
    
			
			var date_id = $('#middle_name_id').val(); 
			if(walkIn ==0){
        $('#ssid').removeClass('hide');
        $('#imesSelectedId').removeClass('hide');
        $('#datesSelectedId').removeClass('hide');
        $('#selected_error_msg').html("")

      }else{
        $('#ssid').addClass('hide');
        $('#imesSelectedId').addClass('hide');
        $('#datesSelectedId').addClass('hide');
        $('#selected_error_msg').html("Please contact nybestmedical");
        return  false;
      }
			if(location_id !='' && date_id !=''){
				$.ajax({
					
					url: "<?php echo URL::to('/'); ?>/location-schedule-search1",
					type: "GET",
					data: {
						"location_id": location_id,
						'start_time':date_id
					},
					success: function(resp) {
						var json = JSON.parse(resp);
						var htmls = '';
						$('#time_id').html("");
						if (json.length !=0) {
							htmls = '<option value="">Select Appointment Time</option>';
							$.each(json,function(i,v){
                if(v.slots !=0){
                  htmls +='<option value="'+v.id+'">'+v.start_time+'-'+v.end_time+' ('+v.slots+')'+'</option>' 
                }
								
							});
							
						} else {
							htmls = '<option value="">No appointments available for selected date</option>'
						}
						
						$('#time_id').html(htmls);
					}

				})
				
			}
			
		}
		function getTimeSearchEdit(){
			var location_id = $('input[name="location_id_edit"]:checked').val();
			console.log(location_id);
			var date_id = $('#middle_name_id_edit').val();
			
			if(location_id !='' && date_id !=''){
				$.ajax({
					
					url: "<?php echo URL::to('/'); ?>/location-schedule-search1",
					type: "GET",
					data: {
						"location_id": location_id,
						'start_time':date_id
					},
					success: function(resp) {
						var json = JSON.parse(resp);
						var htmls = '';
						$('#time_id').html("");
						if (json.length !=0) {
							htmls = '<option value="">Select Appointment Time</option>';
							$.each(json,function(i,v){
                if(v.slots !=0){
                  htmls +='<option value="'+v.id+'">'+v.start_time+'-'+v.end_time+' ('+v.slots+')'+'</option>' 
                }
								
							});
							
						} else {
							htmls = '<option value="">No appointments available for selected date</option>'
						}
						
						$('#time_id_edit').html(htmls);
					}

				})
				
			}
			
		}
		
		
		$(document).ready(function() {
		  $("#close_account").on("click", function(e) {
			
			e.preventDefault();
			swal({
			  title: "Please confirm you will be using your own doctor to obtain all necessary Medical’s due.",
			 
			  type: "warning",
			  showConfirmButton: true,
			  showCancelButton: true,
			  confirmButtonText:"Yes",
			  cancelButtonText:"Cancel"
			},
			function(isConfirm){
					if(isConfirm ==true){
						$.ajax({
							async:false,
							global:false,
							url:"<?php echo URL::to('/');?>/patient/change-status",
							type:"GET",
							data:{'id':<?php echo $query->id;?>},
							success:function(res){
								if(res ==1){
									window.location.href="<?php echo URL::to('/');?>/thank-you?time=1232";
								}else{
									alert("Sorry");
								}
							}
						})
					}
				
			  })
		  });
		});

function getReschedule() {
  $('#edit_form').attr('style',"");

}
</script>