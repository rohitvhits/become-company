@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/global.css') }}" type="text/css" />

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0 font-weight-bold">HHA Dashboard</h5>
                </div>
                
            </div>
            <hr />
            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="card common-card-box" style="height: 100%; border-radius:4px;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="dash-sm-card">
                                                <div class="row">
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-info shimmer">
                                                            <div class="inner">
                                                                <h3 id="arrived">{{ count($totalCaregivers)}}</h3>
                                                                <p>Total Caregiver</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-ticket"></i>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-warning shimmer">
                                                            <div class="inner">
                                                                <h3 id="processing">{{ count($totalPatient)}}<sup style="font-size: 20px"></sup></h3>
                                                                <p>Total Patient</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-ticket"></i>
                                                            </div>
                                                           
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-success shimmer">
                                                            <div class="inner">
                                                                <h3 id="check_in">{{ count($totalOtherCompliance)}}</h3>
                                                                <p>Total Other Compliane</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-ticket"></i>
                                                            </div>
                                                           
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
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
