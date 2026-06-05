@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/global.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/my_dashboard.css') }}" type="text/css" />
<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0 font-weight-bold">My Dashboard</h5>
                </div>
                <div class="col-md-4 d-flex justify-content-end">
                    
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="row">
						<div class="col-md-6 mb-2">
							<div class="dash-side-box">
								<div class="box info-box card common-card-box p-0">
									<div class="title justify-content-end">
										<h5 class="margin-cls-12">Agency Activity Feed</h5>
										<ul class="nav nav-tabs" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" id="appoinment-today-tab" data-toggle="tab" href="#today-appoinment" role="tab" aria-controls="today-appoinment" aria-selected="false" onclick="getActivityFeedData();">Appointment</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" id="upcomming-appoinment" data-toggle="tab" href="#appoinment-upcomming" role="tab" aria-controls="appoinment-upcomming" aria-selected="true" onclick="getActivityFeedUserData();">User</a>
											</li>
										</ul>
									</div>
									<div class="tab-content">
										<div class="tab-pane fade active show" id="today-appoinment" role="tabpanel"
											aria-labelledby="appoinment-today-tab">
											<div class="agency-data-loader ml-3" style="display:flex">
												<div class="col-md-12">
													<div class="row btm-brder">
														<div class="row col-md-12">
															<div class="mb-1 col-md-6">
																<h6 class="mb-1">Status</h6>
															</div>
															<div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">
															</div>
															<div class="shimmer-loader mb-1 col-md-12">
															</div>
															<div class="shimmer-loader mb-1 col-md-12">
															</div>
															<div class="shimmer-loader mb-1 col-md-12" style="display:flex">
																<div class="col-md-6">
																</div>
																<div class="col-md-6" style="display:flex;justify-content: end">
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row basic-detail-row activity_div"
												style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;" id="agency_activity_feed">
											</div>
										</div>

										<div class="tab-pane fade" id="appoinment-upcomming" role="tabpanel"
											aria-labelledby="upcomming-appoinment">
											<div class="agency-data-loader ml-3" style="display:flex">
												<div class="col-md-12">
													<div class="row btm-brder">
														<div class="row col-md-12">
															<div class="mb-1 col-md-6">
																<h6 class="mb-1">Status</h6>
															</div>
															<div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">
															</div>
															<div class="shimmer-loader mb-1 col-md-12">
															</div>
															<div class="shimmer-loader mb-1 col-md-12">
															</div>
															<div class="shimmer-loader mb-1 col-md-12" style="display:flex">
																<div class="col-md-6">
																</div>
																<div class="col-md-6" style="display:flex;justify-content: end">
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row basic-detail-row activity_div"
												style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;" id="agency_user_activity_feed">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="dash-side-box">
								<div class="box info-box card common-card-box p-0">
									<div class="title">
										<h5 class="margin-cls-12">Status Not Updated (Last 7 Days)</h5>
									</div>
									<div class="status-data-loader ml-3" style="display:flex">
										<div class="col-md-12">
											<div class="row btm-brder">
												<div class="row col-md-12">
													<div class="mb-1 col-md-6">
														<h6 class="mb-1">Status</h6>
													</div>
													<div class="shimmer-loader mb-1 col-md-6" style="display: flex;justify-content: flex-end;">
													</div>
													<div class="shimmer-loader mb-1 col-md-12">
													</div>
													<div class="shimmer-loader mb-1 col-md-12">
													</div>
													<div class="shimmer-loader mb-1 col-md-12" style="display:flex">
														<div class="col-md-6">
														</div>
														<div class="col-md-6" style="display:flex;justify-content: end">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row basic-detail-row status_div"
										style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;" id="status_not_updated">
									</div>
								</div>
							</div>
						</div>
            		</div>
                </div>
            </div>
        </div>
    </div>
@include('include/footer')
<script>
	var ACTIVITY_FEED_DATA = "{{ url('get-activity-feed-data') }}";
	var ACTIVITY_FEED_USER_DATA = "{{ url('get-activity-feed-user-data') }}";
	var LAST_STATUS_DATA = "{{ url('get-last-status-not-updated-data') }}";
	var PATIENT_URL = "{{ url('patient/view/') }}";
	var USER_URL = "{{ url('user-view/') }}";
</script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('/assets/modulejs/my_dashboard.js')}}?time={{ env('timestamp') }}"></script>