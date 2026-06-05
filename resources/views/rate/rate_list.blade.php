		
 @include('include/header')
@include('include/sidebar')

 <div class="main-panel">
        <div class="content-wrapper">
          <div class="card">
          	  <div class="row list-name">
				   <div class="col-sm-6 card-title" > <h4 class="card-title">Rates List</h4></div>
				   <div class="col-sm-6" > 
          <!--   <a href="" class="btn btn-success btn-rounded btn-fw pull-right" id="test_rate" onclick="export_data()"><i class="mdi mdi-file-export"></i>Export</a>
				   	 <a href="<?php echo URL::to("/"); ?>/rates" class="btn btn-danger btn-rounded btn-fw pull-right" ><i class="mdi mdi-reload"></i> Reset</a>
				   	<a href="<?php echo URL::to('/add-rate') ?>" class="btn btn-primary btn-rounded btn-fw pull-right"><i class="mdi mdi-plus"> </i> Add Rate</a> -->
				   	</div>
				
 			</div>
            <div class="card-body">
             
              
              <div class="row">
	              		
                <div class="col-12">
                  <div class="table-responsive">
                  	<form method="post" action="<?php echo URL::to("/"); ?>/search-rate" onsubmit="return validation();">
                  		       <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <table id="order-listing" class="table">
                      <thead>
                        <tr>
									<th>#</th>
									<th>Agency Name</th>
									<th>Item</th>
									<th>Rate</th>
									<th>Start Date</th>
									<th>End Date</th>
									<th>Action</th>
								</tr>
                      </thead>
                     <tbody>
                    <!--  	<tr>
                     		<td></td>
                           <?php if($user->user_type_fk==3){ ?>
                     		<td>
                          <select name="agency_fk" id="agency_fk">
                     			<option value="">Select Agency</option>
                     			<?php 
                     			 foreach ($agencyList as $rwAgency) {?>
                     			<option value="<?php echo $rwAgency->id; ?>" <?php echo (($rwAgency->id) == $agency_fk) ? 'selected' : '' ;?>   ><?php echo $rwAgency->agency_name; ?></option> 
                     		<?php 	}  ?>
                     			
                     		</select></td>
                      <?php }else{?> <td></td>  <?php } ?>
                     		<td><select name="item" id="item">
                     			<option value="">Select Item</option>
                     			<?php 
                     			 foreach ($item as $rwItem) {?>
                     			<option value="<?php echo $rwItem->id; ?>" <?php echo (($rwItem->id) == $select_item) ? 'selected' : '' ;?> ><?php echo $rwItem->name; ?></option> 
                     		<?php 	} ?>
                     			
                     		</select></td>
                     		
                     		<td></td>
                     		<td><input type="text" autocomplete="off" value="<?php  echo ($start_date!='') ? date('m/d/Y',strtotime($start_date)) : ''; ?>" name="start_date" id="start_date"></td>
                     		<td><input type="text" autocomplete="off" value="<?php  echo ($end_date!='') ? date('m/d/Y',strtotime($end_date)) : ''; ?>" name="end_date" id="end_date"></td>
                     		<td><input type="submit" name="search" class="btn btn-primary btn-rounded btn-fw pull-right"  value="search">  </td>
                     	</tr> -->
								<?php 
								$i = 1;
								foreach ($rateData as $row) {  ?>
									<tr>
										<th scope="row"><?= $i++ ?></th>
										<td><?= $row->agency_name ?></td>
										<td><?= $row->item_name ?></td>
										<td>$<?= $row->rate ?></td>
										<td><?= date("m-d-Y",strtotime($row->start_date)) ?></td>
										<td><?= date("m-d-Y",strtotime($row->end_date)) ?></td>
										


										<td><a href="<?php echo URL::asset("/"); ?>rate-edit/<?= $row->id?>" data-toggle="tooltip" title="{{ trans('sentence.Edit')}}"><i class="mdi mdi-eyedropper"></i></a> <a href="<?php echo URL::asset("/"); ?>rate-delete/<?= $row->id ?>" data-toggle="tooltip" title="{{ trans('sentence.Delete')}}" onclick="return confirm('Are you sure remove this record?')"><i class="mdi mdi-delete"></i></a></td>
									</tr>
								<?php } ?>
							</tbody>
                    </table>
                </form>
                     <div class="pull-right pegination-margin">
						            {{$rateData->links("pagination::bootstrap-4")}}
						          </div>
                   
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


<!-- <script>
	function search(){
	alert('hello');
	var agency_fk = $('#agency_fk').val();
	var item = $('#item').val();
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	
	$.ajax({
            url: '<?php echo URL::to("/"); ?>/search-rate/?agency_fk=' + agency_fk + '&item=' + item + '&start_date=' + start_date +'&end_date=' + end_date,
            type: "get",
            datatype: "html",
        })

}
</script> -->

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
        alert("End date should be greater than Start date");
       //  $("#end_date_error_mess").html('End date should be greater than Start date');
        document.getElementById("end_date").value = "";
     
    }
});

</script>
<script>

  function validation() {
   
    var agency_fk = $('#agency_fk').val();
    var item = $('#item').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    if(agency_fk =='' && item=='' && start_date=='' &&  end_date=='' ){
    	alert('please select any one');
    	 return false;
    }else{
    	return true;
    }
}
function export_data() {

var agency_fk = $('#agency_fk').val();
  var item = $('#item').val();
  var start_date = $('#start_date').val();
  var end_date = $('#end_date').val();
  var temp1 = '<?php echo URL::to("/")?>/rate-export?agency_fk='+agency_fk+'&item='+item+'&start_date='+start_date+'&end_date='+end_date;
  //  var temp = temp1.replace("http://", "https://");
    $('#test_rate').attr("style", '');
    $('#test_rate').attr("href", temp1);
  }

</script>
  <!-- End Date Picker -->
@include('include/footer')