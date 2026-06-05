@include('include/header')
@include('include/sidebar')
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="<?php echo URL::to('/');?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <link href="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo URL::to('/');?>/assets/sweetalert.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	
</style>
 <div class="main-panel">
<?php
$follow_dates='';
?>
        <div class="content-wrapper">
          <div class="card">
          	  <div class="row list-name">
				   <div class="col-sm-5" > <h4 class="card-title">Task List ({{$query->total()}})</h4></div>
				   <div class="col-sm-7 pull-right" >
				   <!--<a href="javascript:void(0)" onclick="getArchive()" class="btn btn-info btn-fw btn-sm pull-right"><i class="mdi mdi-reload"></i>Patient Archive</a>-->
                     <a href="{{url('tasks/mytesk/export')}}?task_name={{ $task_name }}&status={{ $status }}&created_date={{ $created_date }}" class="btn btn-success pull-right btn-fw btn-sm"><i class="mdi mdi-file"> </i>Export</a>
                   <?php 
				   
				   ?>
                 
                 
                   
				   </div>
			</div>
    
            <div class="card-body compact-view">
              <div class="row">
                <div class="col-12">
					<div class="wmd-view-topscroll">
					    <div class="scroll-div1">
					    </div>
					</div>
					
              		    
		                    <table id="order-listing1" class="table table-bordered">
		                      <thead>
		                        <tr>
                                    <!--<th></th>-->
                                    <th style="width:100px;">Record</th>
                                    
                                    <th>Task Name</th>
                                    
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
		                      	<tr>
								<form method="get" action="" >
                                   
                                        <td><input type="submit" name="submit" class="btn btn-primary btn-sm btn-rounded btn-fw  pull-right" value="Search"></td>
                                        <td><input type="text"  class="form-control" name="task_name" value="{{$task_name}}" ></td>
                                       
                                        <td>
                                        <select name="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="Urgent" @if($status =="Urgent") selected='selected' @endif>Urgent</option>
                                                <option value="Outstanding" @if($status =="Outstanding") selected='selected' @endif>Outstanding</option>
                                                <option value="Pending" @if($status =="Pending") selected='selected' @endif>Pending</option>
                                                <option value="Completed" @if($status =="Completed") selected='selected' @endif>Completed</option>
                                            </select>

                                        </td>
                                        <td><input type="text" name="created_date" autocomplete="off"  class="form-control fdate_ids" value="{{ $created_date }}" style="width:100px;"></td>
                                    <td></td>
								</form>
								</tr>
								</thead>
								<tbody>
                                    @php 

                                    $i = 1 +(($query->currentPage()-1) * $query->perPage());

                                    @endphp
                                    @if(count($query) >0)
                                    
                                        @foreach($query as $val)
                                        <tr id="{{$val->id}}">
                                            <td>{{ $i++}}</td>
                                            <td>{{$val->task_name}}</td>
                                            
                                            <td id="status{{$val->id}}">
												
                                                @if(strtolower($val->task_status) =='pending')
                                                    <span class="badge badge-primary">Pending</span>
                                                @elseif(strtolower($val->task_status) =='urgent')
                                                <span class="badge badge-danger">Urgent</span>
                                                @elseif(strtolower($val->task_status) =='outstanding')
                                                <span class="badge badge-success">Outstanding</span>
                                                @elseif(strtolower($val->task_status) =='completed')
                                                <span class="badge badge-info">Completed</span>
                                                @endif
                                            </td>
                                            <td>{{date('m/d/Y h:i A',strtotime($val->created_date))}}</td>
                                            <td>{{$val->first_name}} {{$val->last_name}}</td>
                                            <td>
											<a href="{{url('/tasks/task-list')}}/{{$val->id}}"><i class="fa fa-eye"></i></a>
                                           <a href="javascript:void(0)" onclick="getDelete({{$val->id}})"><i class="fa fa-trash"></i></a>
                                               
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
									
                                    @if(count($query) ==0)
                                        <tr>
                                            <td colspan="6">No record available</td>
                                        </tr>
                                    @endif
								</tbody>
		                    </table>
                  		
                            <div class="pull-right pegination-margin">
						{{$query->appends(request()->input())->links("pagination::bootstrap-4")}}
						</div>             
					
                </div>
              </div>
           </div>
        </div>
    </div>	  

@include('include/footer')
<script src="<?php echo URL::to('/');?>/assets/vendors/select2/select2.min.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/js/select2.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/css/toastr/toastr.min.js"></script>
  <script src="<?php echo URL::to('/');?>/assets/sweetalert.min.js"></script>
	<script>  
		toastr.options.closeButton = true;
        toastr.options.tapToDismiss = false;
        toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "500",
        "timeOut": "3000",
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "tapToDismiss": false
    };
  /*vishal d patel code end chat message listing*/
</script>

 <script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo URL::to('/');?>/assets/js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL::to('/');?>/css/daterangepicker.css" />
<script type="text/javascript">
    $(function(){
    $(".wmd-view-topscroll").scroll(function(){
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function(){
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
});


function getDelete(id){
    swal({
            title: "Are you remove this task ?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes',
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: false
            },
            function(isConfirm){

            if (isConfirm){
                $.ajax({
                    type:"POST",
                    url:"{{url('tasks/task-list/')}}/"+id,
                    data:{
                        '_token':"{{csrf_token()}}",
                        '_method':"DELETE",
                        'id':id
                    },
                    success:function(res){
                        if(res ==1){
                            toastr.success('Task successfully deleted');
                            $('#'+id).remove();
							swal.close();
                        }else{
                            toastr.error('Sorry, something went wrong. Please try again.'); 
							swal.close();
                        }
                    }
                })
            } else { 
                swal.close(); 
            } 
        });

}


</script>
<script>
	
	$(function () {
		$('.fdate_ids').on('apply.daterangepicker', function(ev, picker) {
		  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
		});
		
        var start = moment().subtract(0, 'days');
        var end = moment();
        $('.fdate_ids').daterangepicker({
            startDate: start,
            endDate: end,
            autoUpdateInput: false,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function (chosen_date, end_date) {

            $('.fdate_ids').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
           // search();
        })
		
    });
</script>
