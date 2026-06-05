@include('include/header')

@include('include/sidebar')

<div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
    
     <div class="col-12 grid-margin">  
                  @if(Session::has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ Session::get('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          @endif
          @if(Session::has('error') )
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>{{ Session::get('error') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          @endif
        </div>
    

   <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Edit Rate</h4>
           <!--  <p class="card-description">
              Horizontal form layout
            </p> -->
           <form class="forms-sample" action='<?php echo URL::to('/rate-update/') ?>/<?php echo $rateDetail->id ?>' name="adduser" method="post" onsubmit="return validation();" >
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <?php if($user->user_type_fk==3){ ?>
                 <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Agency Name</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="agency" name="agency">
                   <!--  <option value="">Select Agency</option> -->
                 <!--  <?php foreach ($agencyList as $rwAgency) { ?>
                    <option value="<?php echo $rwAgency->id ?>"<?php echo($rwAgency->id==$rateDetail->agency_fk)? 'selected':''  ?> ><?php echo $rwAgency->agency_name; ?></option>
                    <?php } ?>  -->
                    <option value="<?php echo $rateDetail->agency_fk?>" ><?php    if(isset($agencyListArray[$rateDetail->agency_fk])) { echo $agencyListArray[$rateDetail->agency_fk]; } ?></option> 
                    </select>
                    <span id="agency_error" class="error mt-2 text-danger" for="agency"><?php echo $errors->edit_rate->first('agency'); ?></span>
                  </div>
                </div>
                 <?php }else{?>
                            <input type="hidden" class="form-control" name="agency" value="<?php echo $user->agency_fk; ?>">
                          <?php }?>

                 <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Item</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="item" name="item">
                    <option value="">Select Item</option>
                  <?php foreach ($item as $rwItem) { ?>
                    <option value="<?php echo $rwItem->id ?>"<?php echo($rwItem->id==$rateDetail->item)? 'selected':''  ?>><?php echo $rwItem->name; ?></option>
                    <?php } ?>
                    </select>
                    <span id="item_error" class="error mt-2 text-danger" for="item"><?php echo $errors->edit_rate->first('item'); ?></span>
                  </div>
                </div>
              <div class="form-group row">
                <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Rate</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" placeholder="Enter Rate"  onkeypress="return isNumber(event)" id="rate" name="rate" value="<?php echo $rateDetail->rate; ?>">
                   <span class="error mt-2 text-danger" id="rate_error" for="rate"><?php echo $errors->edit_rate->first('rate'); ?></span>
                </div>
              </div>
              <div class="form-group row">
                <label for="start_date" class="col-sm-3 col-form-label">Start Date</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" placeholder="MM/DD/YYYY" autocomplete="off" id="start_date" name="start_date" value="<?php echo date("m/d/Y",strtotime($rateDetail->start_date)); ?>">
                  <span id="start_date_error" class="error mt-2 text-danger" for="start_date"><?php echo $errors->edit_rate->first('start_date'); ?></span>
                </div>
              </div>
               <div class="form-group row">
                <label for="start_date" class="col-sm-3 col-form-label">End Date</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" placeholder="MM/DD/YYYY" autocomplete="off" id="end_date" name="end_date" value="<?php echo date("m/d/Y",strtotime($rateDetail->end_date)); ?>">
                  <span id="end_date_error" class="error mt-2 text-danger" for="end_date"><?php echo $errors->edit_rate->first('end_date'); ?></span>
                   <span id="end_date_error_mess" class="error mt-2 text-danger" for="end_date"></span>
                </div>
              </div>
			   <div class="form-group row">
				<label class="col-sm-3 col-form-label">Type</label>
				<div class="col-sm-4">
					<div class="form-check">
					  <label class="form-check-label">
						<input type="radio" class="form-check-input" id="house_visit"  name="type" value="singleItem" <?php if($rateDetail->type=='singleItem') { echo "checked='checked'";} ?>>Single Item</label>
					</div>
				</div>
			  <div class="col-sm-5">
				<div class="form-check">
				  <label class="form-check-label">
					<input type="radio" class="form-check-input" id="house_visit"  name="type" value="monthlyItem" <?php if($rateDetail->type=='monthlyItem') { echo "checked='checked'";} ?>> Monthly Item</label>
				</div>
			  </div>
				<span class="error mt-2 text-danger"  id="house_visit_error"><?php echo $errors->add_record->first('type'); ?></span>
			</div>
			<div class="form-group row">
				<label class="col-sm-3 col-form-label">Can used with Monthly items</label>
				<div class="col-sm-4">
					<div class="form-check">
					  <label class="form-check-label">
						<input type="checkbox" class="form-check-input"  name="cused" value="Y" <?php if($rateDetail->cused=='Y') { echo "checked='checked'";} ?>>Yes</label>
					</div>
				</div>
			  <span class="error mt-2 text-danger"  id="house_visit_error"></span>
			</div>
              <button type="submit" class="btn btn-primary mr-2">Update</button>
             <!--  <button class="btn btn-light">Cancel</button> -->
            </form>
          </div>
        </div>
      </div>

<!-- /Main Content -->
<!-- /Page Content -->
<script>
  $('#type').on('change', function() {
    if (this.value == "individual") {
      $('#outletId').show();
    } else {
      $('#outletId').hide();
    }
  });

  function validation() {

    var temp = 0;

   
    var agency = $('#agency').val();
    var item = $('#item').val();
    var rate = $('#rate').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();


    
    if (agency == "") {
      $('#agency_error').html("Required");
      temp++;
    } else {
      $("#agency_error").html("");
    }
     if (item == "") {
      $('#item_error').html("Required");
      temp++;
    } else {
      $("#item_error").html("");
    }
     if (rate == "") {
      $('#rate_error').html("Required");
      temp++;
    } else {
      $("#rate_error").html("");
    } 
    if (start_date == "") {
      $('#start_date_error').html("Required");
      temp++;
    } else {
      $("#start_date_error").html("");
    }
    if (end_date == "") {
      $('#end_date_error').html("Required");
      temp++;
    } else {
      $("#end_date_error").html("");
    }
  

      if (temp == 0) {

        return true;

      } else {

        return false;

      }

    }
              function isNumber(evt) {
        
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (( charCode != 46 ||  $(this).val().indexOf('.') != -1)  && (charCode < 48 || charCode > 57 )) {
            
            return false;
        }
        return true;
    }

</script>
<!-- Date Picker -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>
  $("#start_date, #end_date").datepicker();
  $("#end_date").change(function () {
    var startDate = document.getElementById("start_date").value;
    var endDate = document.getElementById("end_date").value;
 
    if ((Date.parse(endDate) <= Date.parse(startDate))) {
       // alert("End date should be greater than Start date");
         $("#end_date_error_mess").html('End date should be greater than Start date');
        document.getElementById("end_date").value = "";
        $("#end_date_error").html("");
    }
});

</script>


  <!-- End Date Picker -->
@include('include/footer')