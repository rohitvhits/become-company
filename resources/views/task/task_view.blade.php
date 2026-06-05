@include('include/header')
@include('include/sidebar')
<link href="{{ asset('assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/sweetalert.min.css')}}" rel="stylesheet" type="text/css" />
<style>
	.content-wrapper {
		margin-bottom: -100px;
	}

	.select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: normal !important;
		line-height: inherit !important;
	}

	#commentList {
		overflow: auto;
		width: auto;
		max-height: 500px;
		height: 100% !important;
	}
.chat{
	/* padding: 5px 20px 5px 10px */
}
.min-height109 {
    min-height: 109px;
}
	.chat .item>img {
		width: 40px;
		height: 40px;
		border: 2px solid transparent;
		border-radius: 50%;
	}

	.chat .item>.message {
		margin-left: 55px;
		margin-top: -40px;
	}

	.chat .item>.message>.name {
		display: block;
		font-weight: 600;
	}

	a {
		color: #3c8dbc;
	}
</style>
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css')}}">
<!--main-container-part-->

<div class="main-panel">
	<div class="content-wrapper">
		<div class="dashboard-header d-flex flex-column grid-margin">
			<div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
				<div class="d-flex align-items-center">
					<h4 class="mb-0 font-weight-bold">Task View </h4>
				</div>
				<div class="d-md-flex align-items-center justify-content-between flex-wrap">
					<div class="d-flex align-items-center"></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-6 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="row">

							<div class="col-md-12" style="margin-bottom: 15px;">
								<h6 class="card-title">Task View
									@can('task-delete')
									@if(auth()->user()->id == $task_details->created_by)
									<a href="javascript:void(0)" onclick="getDelete('{{request()->segment(2)}}')" class="pull-right btn btn-danger btn-rounded  btn-sm  d-none d-md-block" title="Delete"><i class="fa fa-trash"></i>
										Delete</a>
									@endif
									@endcan

									@if(auth()->user()->id == $task_details->created_by)
									@can('task-edit')
									<a href="{{url('/')}}/tasks/task-list/{{request()->segment(2)}}/edit" class="pull-right btn btn-secondary btn-rounded  btn-sm  d-none d-md-block" title="Edit" style="margin-right: 10px;"><i class="fa fa-edit"></i> Edit</a>
									@endcan
									@endif

									@if(auth()->user()->id == $task_details->assign_id)
									<a href="javascript:void(0)" class="pull-right btn btn-danger btn-rounded  btn-sm mr-2 " id="clock_out" onclick="clockIn('{{request()->segment(2)}}','clock_out')" title="Clock Out" style="margin-right: 10px;"><i class="fa fa-clock-o"></i> Clock Out</a>
									<a href="javascript:void(0)" class="pull-right btn btn-success btn-rounded  btn-sm mr-2 " id="clock_in" onclick="clockIn('{{request()->segment(2)}}','clock_in')" title="Clock In" style="margin-right: 10px;"><i class="fa fa-clock-o"></i> Clock In</a>
									@endif
								</h6>
							</div>

						</div>

						<div class="row">
							<div class="col-lg-12">
								<div class="profile-feed">
									<div class="d-flex align-items-start profile-feed-item">
										<table class="table">
											<tbody>
												<tr>
													<th>Task Name</th>
													<td>
														@if($task_details->task_name!='')
														{{$task_details->task_name}}
														@endif
													</td>
												</tr>
												<tr>
													<th>Assign User</th>
													<td><span id="assign_user_name">{{ $task_details->assignFname }} {{ $task_details->assignLnamae }}</span></td>
												</tr>
												<tr>
													<th>Assign To</th>
													<td>
														<div class="row">
															<div class="col-md-10">
																<select name="assign_to_user" class="form-control js-example-basic-single" id="assign_to_user">
																	<option value="">Select Assign To</option>
																	@if(!empty($user_list[0]))
																	@foreach($user_list as $va)
																	@if(auth()->user()->id != $va->id)
																	<option value="{{$va->id}}" <?php if ($task_details->assign_id == $va->id) { ?> selected='selected' <?php } ?>>{{$va->name}} (@if($va->agency_fk !="") Agency User @else NyBest User @endif)</option>
																	@endif
																	@endforeach
																	@endif

																</select>
																<span id="assign_to_error" class="text-danger"></span>
															</div>

															<div class="col-md-2">

																<button type="button" onclick="assignUser()" style="bottom:3px;" class="btn btn-primary" id="updateAssignUserBtn">Update</button>

															</div>
														</div>

													</td>
												</tr>
												<tr>
													<th>Status</th>
													<td id="checkStatus">
														<div class="row">
															<div class="col-md-2" id="statusDisplay">
																@php
																$class = "";
																$status = "";

																if ($task_details->task_status =="Pending") {
																$class = "badge badge-primary";
																$status = "Pending";
																} elseif ($task_details->task_status =="Urgent") {
																$class = "badge badge-danger";
																$status = "Urgent";
																} elseif ($task_details->task_status =="Outstanding") {
																$class = "badge badge-info";
																$status = "Outstanding";
																} elseif ($task_details->task_status =="Completed") {
																$class = "badge badge-success";
																$status = "Completed";
																}
																@endphp

																<span id="task_status_id" class="{{$class}}">{{$status}}</span>

															</div>
															<div class="col-md-8">
																<select name="task_status" id="task_status" class="form-control" id="updateStatusDropdown">
																	<option value="Pending" {{ $task_details->task_status == 'Pending' ? 'selected' : '' }}>Pending</option>
																	<option value="Urgent" {{ $task_details->task_status == 'Urgent' ? 'selected' : '' }}>Urgent</option>
																	<option value="Outstanding" {{ $task_details->task_status == 'Outstanding' ? 'selected' : '' }}>Outstanding</option>
																	<option value="Completed" {{ $task_details->task_status == 'Completed' ? 'selected' : '' }}>Completed</option>
																</select>
															</div>
															<div class="col-md-2">
																<button type="button" style="bottom:3px;" class="btn btn-primary" onclick="getChangeStatus()" id="updateStatusBtn">Update</button>
															</div>
														</div>
														<div class="input-group">
															<div>



															</div>
															&nbsp;&nbsp;&nbsp;

															&nbsp;&nbsp;&nbsp;
															<div class="input-group-append">

															</div>
														</div>
													</td>
												</tr>

												<tr>
													<th>Created Date</th>
													<td>
														@if($task_details->created_date!='')
														{{ date('m/d/Y h:i A',strtotime($task_details->created_date)) }}
														@endif
													</td>
												</tr>
												<tr>
													<th>Created By</th>
													<td>{{ $task_details->first_name }} {{ $task_details->last_name }}</td>
												</tr>
												{{-- <tr>
													<th>Description</th>
													<td>
														@if($task_details->task_description!='')
														{{ $task_details->task_description }}
												@endif
												</td>
												</tr>
												<tr>
													<th>Notes</th>
													<td>
														@if($task_details->notes!='')
														{{ $task_details->notes }}
														@endif
													</td>
												</tr> --}}
												<tr>
													<th>Start Date</th>
													<td>
														@if($task_details->start_date!='')
														{{ date('m/d/Y',strtotime($task_details->start_date)) }}
														@endif
													</td>
												</tr>
												<tr>
													<th>Due Date</th>
													<td>
														@if($task_details->due_date!='')
														{{ date('m/d/Y',strtotime($task_details->due_date)) }}
														@endif
													</td>
												</tr>
												<tr>
													<th>Total Task Hour</th>
													<td>
														{{-- @if($task_details->task_hour!='') --}}
														<span id="task_hour_id">{{$task_details->task_hour}}</span>
														{{-- @endif --}}
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6 grid-margin stretch-card">
				<div class="card">

					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<h6 class="card-title">Comment Section
								</h6>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="profile-feed chat scrollchat min-height109" id="commentList">
								
								</div>
							</div>
						</div>
						
					</div>
					<div class="card-footer">
						<div >
							<div class="row col-md-12 form-group">
								<textarea class="form-control" name="task_comment" placeholder="Type message..." id="task_comment" rows="5" cols="50"></textarea>
								<span id="comment_error" style="color:red"></span>
							</div>
							<button type="submit" onclick="commentSave()" class="btn btn-primary">Save Comment</button>

						</div>
					</div>


				</div>
			</div>
		</div>



		{{-- //new div add 19-12-2023 --}}
		<div class="row">
			<div class="col-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="task-log-tab" data-toggle="tab" onclick="getLogList(1);" href="#task-log-1" role="tab" aria-controls="task-log-1" aria-selected="false">Task Log List</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="task-time-tab" data-toggle="tab" onclick="getTimeLogList(1);" href="#task-time-1" role="tab" aria-controls="task-time-1" aria-selected="false">Task Time Log</a>
							</li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane fade active show" id="task-log-1" role="tabpanel" aria-labelledby="task-log-tab">
								<div class="row">
									<div class="col-sm-6 card-title">
										<h4 class="card-title">Task Logs</h4>
									</div>
									<div class="col-sm-6">

									</div>
								</div>
								<div class="table-responsive">

									<div class="col-12" id="logList" style="display:flex;justify-content:center;">
										<img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag" style="display: none; ">
									</div>
								</div>
							</div>

							<div class="tab-pane fade " id="task-time-1" role="tabpanel" aria-labelledby="task-time-tab">
								<div class="row">
									<div class="col-sm-6 card-title">
										<h4 class="card-title">Task Time Logs</h4>
									</div>
									<div class="col-sm-6">

									</div>
								</div>
								<div class="table-responsive">

									<div class="col-12" id="task-time-log-list" style="display:flex;justify-content:center;">
										<img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag1" style="display: none; ">
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>


		</div>

		{{-- end div --}}

	</div>
	<div class="content-wrapper">

	</div>

</div>



@include('include/footer')
<script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/css/toastr/toastr.min.js')}}"></script>
<script src="{{ asset('assets/sweetalert.min.js')}}"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
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
<script>
	$(document).ready(function() {
		commentList()
		$('.js-example-basic-single').select2();
	});

	function getChangeStatus() {

		var status = $('#task_status').val();
		$.ajax({
			type: "GET",
			url: "{{url('tasks/task-change-status')}}",
			data: {
				id: '{{ $task_details->id}}',
				status: status
			},
			success: function(res) {
				console.log(res.data.status);
				var record_id = $('#edit_id').val();
				if (res.status == 1) {
					toastr.success(res.error_msg);
					var response = '';
					if (status == 'Pending') {
						response = '<span class="badge badge-primary">Pending</span>';
					}
					if (status == 'Urgent') {
						response = '<span class="badge badge-danger">Urgent</span>';
					}
					if (status == 'Outstanding') {
						response = '<span class="badge badge-success">Outstanding</span>';
					}
					if (status == 'Completed') {
						response = '<span class="badge badge-info">Completed</span>';
					}
					$('#statusDisplay').html("");
					$('#statusDisplay').html(response);

					getLogList(1);

				} else {
					toastr.error(res.error_msg);
				}
			},
			error: function error(_error) {

				toastr.error(_error.responseJSON.message);
			}
		})

	}

	function getDelete(id) {
		swal({
				title: "Are you delete this task ?",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'Yes',
				cancelButtonText: "No",
				closeOnConfirm: false,
				closeOnCancel: false
			},
			function(isConfirm) {

				if (isConfirm) {
					$.ajax({
						type: "POST",
						url: "{{url('tasks/task-list/')}}/" + id,
						data: {
							'_token': "{{csrf_token()}}",
							'_method': "DELETE",
							'id': id
						},
						success: function(res) {
							if (res == 1) {
								toastr.success('Task successfully deleted');
								window.location.href = "{{url('tasks/task-list')}}";
								swal.close();
							} else {
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

	function getModal(id) {
		$('.error').html("");
		$('#task_description').val("");
		$('#edit_id').val(id);
	}

	$(document).on('click', '.log-pegination .pagination a', function(event) {
		$('li').removeClass('active');
		$(this).parent('li').addClass('active');
		event.preventDefault();
		var myurl = $(this).attr('href');
		var page = $(this).attr('href').split('page=')[1];
		getLogList(page);
	});

	$(document).ready(function() {
		var clockIn = "{{$task_details->clock_in}}"
		var clockOut = "{{$task_details->clock_out}}"
		if (clockIn != '' && clockOut == '') {
			$('#clock_in').attr('style', 'display:none;');
			$('#clock_out').attr('style', 'display:block;');
		}

		if (clockIn == '' || clockOut != '') {
			$('#clock_in').attr('style', 'display:block;');
			$('#clock_out').attr('style', 'display:none;');
		}

		$('#loadertag').show();
		getLogList(1);
	});

	function getLogList(page) {
		var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
		$.ajax({
			method: 'GET',
			url: "{{ url('tasks/patient/task-log-list') }}" + "?page=" + page,
			data: {
				'id': '{{request()->segment(2) }}',
				'_token': "{{ csrf_token() }}"
			},
			beforeSend: function() {
				$('#loadertag').show();
			},
			success: function success(response) {

				$('#loadertag').hide();
				$('#logList').html("");
				$('#logList').html(response);
			},
			error: function error(_error) {
				console.error(_error);
				toastr.error('Something happened. Try again');
			}
		});
	}

	$(document).on('click', '.time-log-pegination .pagination a', function(event) {
		$('li').removeClass('active');
		$(this).parent('li').addClass('active');
		event.preventDefault();
		var myurl = $(this).attr('href');
		var page = $(this).attr('href').split('page=')[1];
		getTimeLogList(page);
	});

	function getTimeLogList(page) {
		var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
		$.ajax({
			method: 'GET',
			url: "{{ url('tasks/patient/task-time-log-list') }}" + "?page=" + page,
			data: {
				'id': '{{request()->segment(2) }}',
				'_token': "{{ csrf_token() }}"
			},
			beforeSend: function() {
				$('#loadertag1').show();
			},
			success: function success(response) {

				$('#loadertag1').hide();
				$('#task-time-log-list').html("");
				$('#task-time-log-list').html(response);
			},
			error: function error(_error) {
				toastr.error('Something happened. Try again');
			}
		});
	}

	function clockIn(id, type) {
		$.ajax({
			method: 'GET',
			url: "{{ url('tasks/patient/task-clock-in-out') }}",
			data: {
				'id': id,
				'type': type,
				'auth_id': "{{auth()->user()->id}}",
				'_token': "{{ csrf_token() }}"
			},

			success: function success(response) {
				if (response.data.type == "clock_in") {

					$('#clock_in').attr('style', 'display:none;');
					$('#clock_out').attr('style', 'display:block;');
				} else {
					$('#task_hour_id').html("");
					$('#task_hour_id').html(response.data.task_hour);
					$('#clock_in').attr('style', 'display:block;');
					$('#clock_out').attr('style', 'display:none;');
				}
				getLogList(1);

				toastr.success(response.error_msg);

			},
			error: function error(_error) {
				toastr.error(_error.responseJSON.message);
				// location.reload();
			}
		});

	}

	function assignUser() {
		var assignUserId = $('#assign_to_user').val();
		$('#assign_to_error').html("");

		if (assignUserId == '') {
			$('#assign_to_error').html("Please select Assign To");
			return false;
		}
		$.ajax({
			method: 'GET',
			url: "{{ url('tasks/task-assign-to-user') }}",
			data: {
				'task_id': '{{ $task_details->id}}',
				'assignUserId': assignUserId,
				'selectedText': $('#assign_to_user').select2('data')[0].text
			},

			success: function success(response) {
				$('#assign_user_name').html("")
				$('#assign_user_name').html(response.data)
				getLogList(1);

				toastr.success(response.error_msg);

			},
			error: function error(_error) {
				toastr.error(_error.responseJSON.message);
			}
		});

	}

	function commentSave() {
		var comment = $('#task_comment').val();

		$('#comment_error').html("");
		if (comment.trim() == '') {
			$('#comment_error').html("Please enter Message");
			return false;
		} else {
			$.ajax({
				method: 'post',
				url: "{{ url('tasks/task-comment-save') }}",
				data: {
					'task_id': '{{ $task_details->id}}',
					'comment': comment,
					'_token': "{{ csrf_token() }}"
				},

				success: function success(response) {

					
					var htmlResponse = `<div class="align-items-start profile-feed-item"><div class=" ml-1">
						<p class="clearfix">
                          <span class="float-left">
						  <strong>${response.data.user_details.first_name}  ${response.data.user_details.last_name}</strong>
                          </span>
                          <span class="float-right text-muted">
						  <i class="fa fa-clock-o"></i>&nbsp;&nbsp;${moment(response.data.created_date).format('MM/DD/YYYY HH:mm A')}
                          </span>
                        </p>
						<p class="clearfix"></p>	
						<p style="white-space:pre-line;margin-top:-10px">
						${response.data.comment}
						</p>
					</div></div>`
					
					$('#task_comment').val("");
					$('#commentList').append(htmlResponse);
					$('.chat').animate({
							scrollTop: $('.chat')[0].scrollHeight}, 2000);
				},
				error: function error(_error) {
					toastr.error(_error.responseJSON.message);
				}
			});
		}


	}

	function commentList() {
		$.ajax({
			method: 'GET',
			url: "{{ url('tasks/task-comment-list') }}",
			data: {
				'task_id': "{{ $task_details->id}}",
			},

			success: function success(res) {
				var data = res.data;
				var response = '';
				$('#commentList').html('');
				if (data.length != 0) {
					var cnt = 1;
					$.each(data, function(i, v) {
						
						response += `<div class="align-items-start profile-feed-item"><div class=" ml-1">
						<p class="clearfix">
                          <span class="float-left">
						  <strong>${v.user_details.first_name}  ${v.user_details.last_name}</strong>
                          </span>
                          <span class="float-right text-muted">
						  <i class="fa fa-clock-o"></i>&nbsp;&nbsp;${moment(v.created_date).format('MM/DD/YYYY HH:mm A')}
                          </span>
                        </p>
						<p class="clearfix"></p>	
						<p style="white-space:pre-line;margin-top:-10px">
						${v.comment}
						</p>
					</div></div>`
					})
				}
				$('#commentList').append(response);
				$('.chat').animate({
						scrollTop: $('.chat')[0].scrollHeight}, 2000);

			},

		});
	}
</script>