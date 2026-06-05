@include('include/header')
@include('include/sidebar')
<style>
    .error {
        color: Red;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }

    .hhaLoader {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgb(255 255 255 / 50%);
        z-index: 99;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .main-panel {
        position: relative;
    }

    .hide {
        display: none;
    }

    .hha-btn-wrapper {
        display: flex;
        align-items: center;
    }
    .h6{
        font-size: 18px !important;
    }
    .shimmer-loader {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite linear;
        border-radius: 4px;
        height: 22px;
            width: 50%;
    }

    @keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
    }
    .card-header {
        padding : 9px 18px !important;
    }
</style>
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">AI Diagnosis</h5>
        </div>
        <div class="row">   
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample" name="predict" method="post" id="predict">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Symptoms<span class="error ml-1">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-11">
                                                    <div>
                                                        <textarea type="text" name="symptoms" id="symptoms" rows="4" value="" class="form-control" placeholder="e.g. Persistent chest pain, shortness of breath, fatigue, sweating"></textarea>
                                                        <span id="symptoms_error" class="error mt-2"><?php echo $errors->add_agency->first('symptoms'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">History<span class="error ml-1">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-11">
                                                    <div>
                                                        <textarea type="text" name="history" id="history" value="" rows="4" class="form-control" placeholder="e.g. Smoker, diabetic, history of high blood pressure or previous cardiac issues"></textarea>
                                                        <span id="history_error" class="error mt-2"><?php echo $errors->add_agency->first('history'); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="diagnosis();" class="btn btn-primary mr-2" id="insertButton"><i class="fa fa-magic mr-2"></i> Diagnosis</button>
                            <button type="button" onclick="refresh();" class="btn btn-success mr-2" id=""><i class="fa fa-refresh mr-2"></i>Refresh</button>
                        </form>
                        <div id="shimmer-loaders" class="mt-5" style="display:none">
                            <div class="card shadow-sm border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold">Diagnosis:</h6>
                                    <ul id="" class="list-group list-group-flush mb-3">
                                        <li class="shimmer-loader mb-1"></li>
                                    </ul>

                                    <h6 class="fw-bold text-primary">Recommended Medications:</h6>
                                    <ul id="" class="list-group list-group-flush mb-3">
                                        <li class="shimmer-loader mb-1"></li>
                                        <li class="shimmer-loader mb-1"></li>
                                        <li class="shimmer-loader mb-1"></li>
                                        <li class="shimmer-loader mb-1"></li>
                                    </ul>

                                    <h6 class="fw-bold text-danger">Red Flags / Urgent Actions:</h6>
                                    <ul id="" class="list-group list-group-flush">
                                        <li class="shimmer-loader mb-1"></li>
                                        <li class="shimmer-loader mb-1"></li>
                                        <li class="shimmer-loader mb-1"></li>
                                        <li class="shimmer-loader mb-1"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="result" class="mt-5" style="display:none;">
                            <div class="card shadow-sm border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold">Diagnosis:</h6>
                                    <ul id="diagnosis" class="list-group list-group-flush mb-3"></ul>

                                    <h6 class="fw-bold text-primary">Recommended Medications:</h6>
                                    <ul id="medications" class="list-group list-group-flush mb-3"></ul>

                                    <h6 class="fw-bold text-danger">Red Flags / Urgent Actions:</h6>
                                    <ul id="red_flags" class="list-group list-group-flush"></ul>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- /Main Content -->
    <script>
        var DIAGNOSIS = "{{url('patient/diagnosis-predict')}}";
    </script>
    <!-- /Page Content -->
    <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
    <script src="{{ asset('assets/js/select2.js')}}"></script>
    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <link href="{{ asset('css/jquery-ui.css')}}">
    <script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('assets/modulejs/diagnosis.js')}}"></script>
    <!-- End Date Picker -->
    @include('include/footer')