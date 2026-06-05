@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/css/jquery-confirm.min.css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/modulejs/css/diagnosis.css?time={{ env('timestamp')}}">
<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.css">
<link rel="stylesheet"
    href="<?php echo URL::to('/'); ?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">       
<!--main-container-part-->
<style>
    .error {
        color: Red;
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
        padding: 9px 18px !important;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper px-3 pb-0">

        <div class="dashboard-header d-flex flex-column ">
            <div class="basic-detail-sec border-bottom  mb-3 card">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 mr-4 font-weight-bold"> AI Diagnosis
                        </h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12 grid-margin stretch-card mb-0">
                            <div class="card">
                                <div class="left-section-main info-tab-sec">
                                    <ul class="nav nav-tabs tabs-left sideways left-section-ul">
                                        <li class="active"><a href="#clinical-notes" data-toggle="tab"> <i class="mdi mdi-note mr-1"></i> Clinical Notes</a>
                                        </li>
                                        <li><a href="#personal-info-section" data-toggle="tab"> <i class="fa fa-info-circle mr-1"></i> Medical Assistant</a>
                                        </li>
                                        <li><a href="#health-tips-tab" data-toggle="tab"> <i class="mdi mdi-file-document mr-1"></i> Health Tips</a>
                                        </li>
                                        <li><a href="#lab-test-tab" data-toggle="tab"> <i class="mdi mdi-clipboard-text-outline mr-1"></i> Suggest Lab Test</a>
                                        {{-- <li><a href="#report-diagnosis-tab" data-toggle="tab"> <i class="mdi mdi-clipboard-text-outline mr-1"></i> Report Diagnosis</a> --}}
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content left-section-tab-content">
                                        <div class="tab-pane active" id="clinical-notes">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="title">
                                                                            <h5><i class="mdi mdi-note mr-1"></i>Clinical Notes</h5>
                                                                        </div>
                                                                        <form class="form-sample" name="clinical-notes-form" method="post" id="clinical-notes-form">
                                                                            @csrf
                                                                            <div class="row basic-detail-row">
                                                                                <div class="col-md-12">
                                                                                    <div class="row">
                                                                                        <div class="col-md-2">
                                                                                            <dt>Transcript<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-10 mb-2">
                                                                                            <textarea type="text" name="transcript" id="transcript" rows="10" value="" class="form-control" placeholder="Start typing or paste medical transcript... e.g., Doctor: How are you feeling? Patient: I've been having headaches and fatigue lately..."></textarea>
                                                                                            <span id="transcript_error" class="error error-html mt-2"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <hr/>
                                                                            <div class="page-rightbtns cust-page-rightbtns">
                                                                                <button type="button" onclick="diagnosisClinicalNots();" class="btn btn-primary mr-2 cust-right-btn" id="insertButton"><i class="mdi mdi-note-plus mr-2"></i> Generate Notes</button>
                                                                                <button type="button" onclick="refresh();" class="btn btn-secondary mr-2 cust-right-btn" id=""><i class="fa fa-refresh mr-2"></i>Refresh</button>
                                                                            </div>
                                                                        </form>
                                                                        <div id="shimmer-loaders" class="mt-5" style="display:none">
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-list mr-2"></i>Clinical Note Summary </h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div class="card-design">
                                                                                        <h3><span class="emoji"><i class="fa fa-file-text"></i></span>Subjective</h3>
                                                                                        <p class="shimmer-loader mb-1"></p>
                                                                                    </div>

                                                                                    <div class="card-design">
                                                                                        <h3><span class="emoji"><i class="fa fa-eye"></i></span>Objective</h3>
                                                                                        <p class="shimmer-loader mb-1"></p>
                                                                                    </div>

                                                                                    <div class="card-design">
                                                                                        <h3><span class="emoji"><i class="fa fa-search"></i></span>Assessment</h3>
                                                                                        <p class="shimmer-loader mb-1"></p>
                                                                                    </div>

                                                                                    <div class="card-design">
                                                                                        <h3><span class="emoji"><i class="fa fa-clipboard"></i></span>Plan</h3>
                                                                                        <p class="shimmer-loader mb-1"></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div id="clinical-notes-div" class="mt-5" style="display:none"> 
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-list mr-2"></i>Clinical Note Summary </h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div id="clinical-notes-result"> 
                                                                                    </div>
                                                                                    <div id="clinical-notes-no-message" class="" style="display:none">
                                                                                        <p>Looks like there’s nothing here yet. Try adding some history or medications!</p>
                                                                                        <small>Please check your spelling or try a different search term.</small>
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
                                        <div class="tab-pane" id="personal-info-section">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="title">
                                                                            <h5><i class="mdi mdi-information mr-1"></i>Medical Assistant </h5>
                                                                        </div>
                                                                        <form class="form-sample" name="predict" method="post" id="predict">
                                                                            @csrf
                                                                            <div class="row basic-detail-row">
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>Symptoms<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea type="text" name="symptoms" id="symptoms" rows="4" value="" class="form-control" placeholder="e.g. Persistent chest pain, shortness of breath, fatigue, sweating"></textarea>
                                                                                            <span id="symptoms_error" class="error error-html mt-2"><?php echo $errors->add_agency->first('symptoms'); ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>History<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea type="text" name="history" id="history" value="" rows="4" class="form-control" placeholder="e.g. Smoker, diabetic, history of high blood pressure or previous cardiac issues"></textarea>
                                                                                        <span id="history_error" class="error error-html mt-2"><?php echo $errors->add_agency->first('history'); ?></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <hr/>
                                                                            <div class="page-rightbtns cust-page-rightbtns">
                                                                                <button type="button" onclick="diagnosis();" class="btn btn-primary mr-2 cust-right-btn" id="insertButton"><i class="fa fa-magic mr-2"></i> Diagnosis</button>
                                                                                <button type="button" onclick="refresh();" class="btn btn-secondary mr-2 cust-right-btn" id=""><i class="fa fa-refresh mr-2"></i>Refresh</button>
                                                                            </div>
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
                                                                        <div id="show-result" class="mt-5" style="display:none"> 
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div id="result"> 
                                                                                        <h6 class="fw-bold">Diagnosis:</h6>
                                                                                        <ul id="diagnosis" class="list-group list-group-flush mb-3"></ul>

                                                                                        <h6 class="fw-bold text-primary">Recommended Medications:</h6>
                                                                                        <ul id="medications" class="list-group list-group-flush mb-3"></ul>

                                                                                        <h6 class="fw-bold text-danger">Red Flags / Urgent Actions:</h6>
                                                                                        <ul id="red_flags" class="list-group list-group-flush"></ul>
                                                                                    </div>
                                                                                    <div id="result-no-message" class="" style="display:none">
                                                                                        <p>Looks like there’s nothing here yet. Try adding some history or medications!</p>
                                                                                        <small>Please check your spelling or try a different search term.</small>
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
                                        <div class="tab-pane" id="health-tips-tab">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="title">
                                                                            <h5><i class="mdi mdi-file-document mr-1"></i>Health Tips</h5>
                                                                        </div>
                                                                        <form class="form-sample" name="predict-health" method="post" id="predict-health">
                                                                            @csrf
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="row basic-detail-row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>Lifestyle<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea type="text" name="lifestyle" id="health-lifestyle" rows="4" value="" class="form-control" placeholder="e.g. Sedentary, smokes occasionally, poor diet"></textarea>
                                                                                            <span id="health_lifestyle_error" class="error error-html mt-2"><?php echo $errors->add_agency->first('lifestyle'); ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row basic-detail-row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>Risk<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea type="text" name="risk" id="health-risk" value="" rows="4" class="form-control" placeholder="e.g. Family history of heart disease"></textarea>
                                                                                            <span id="health_risk_error" class="error error-html mt-2"><?php echo $errors->add_agency->first('risk'); ?></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row basic-detail-row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>History<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea type="text" name="history" id="health-history" value="" rows="4" class="form-control" placeholder="e.g. Smoker, diabetic, history of high blood pressure or previous cardiac issues"></textarea>
                                                                                            <span id="health_history_error" class="error error-html mt-2"><?php echo $errors->add_agency->first('history'); ?></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <hr/>
                                                                            <div class="page-rightbtns cust-page-rightbtns">
                                                                                <button type="button" onclick="diagnosisHealth();" class="btn btn-primary mr-2 cust-right-btn" id="healthinsertButton"><i class="fa fa-magic mr-2"></i> Diagnosis</button>
                                                                                <button type="button" onclick="refresh();" class="btn btn-secondary mr-2 cust-right-btn" id=""><i class="fa fa-refresh mr-2"></i>Refresh</button>
                                                                            </div>
                                                                        </form>
                                                                        <div id="healthy-shimmer-loaders" class="mt-5" style="display:none">
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <h6 class="fw-bold">Diagnosis:</h6>
                                                                                    <ul id="" class="list-group list-group-flush mb-3">
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                    </ul>

                                                                                    <h6 class="fw-bold text-primary">Tips:</h6>
                                                                                    <ul id="" class="list-group list-group-flush mb-3">
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                    </ul>

                                                                                    <h6 class="fw-bold text-info">Preventive Measures:</h6>
                                                                                    <ul id="" class="list-group list-group-flush mb-3">
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div id="health-result-div" class="mt-5" style="display:none;">
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div id="health-result" style="display:none">
                                                                                        <h6 class="fw-bold">Diagnosis:</h6>
                                                                                        <ul id="health-diagnosis" class="list-group list-group-flush mb-3"></ul>

                                                                                        <h6 class="fw-bold text-primary">Tips:</h6>
                                                                                        <ul id="health-tips" class="list-group list-group-flush mb-3"></ul>

                                                                                        <h6 class="fw-bold text-info">Preventive Measures:</h6>
                                                                                        <ul id="health-preventive-measures" class="list-group list-group-flush mb-3"></ul>
                                                                                    </div>
                                                                                    <div id="res-health-msg" style="display:none">
                                                                                        <p>Looks like there’s nothing here yet.</p>
                                                                                        <small>Try adding some proper data to <strong>Diagnosis</strong> to get meaningful results!</small>
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
                                        <div class="tab-pane" id="lab-test-tab">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="title">
                                                                            <h5><i class="mdi mdi-clipboard-text-outline mr-1"></i>Suggest Lab Test</h5>
                                                                        </div>
                                                                        <form class="form-sample" name="predict-lab-test" method="post" id="predict-lab-test">
                                                                        @csrf
                                                                            <div class="row basic-detail-row">
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>Symptoms<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea type="text" name="symptoms" id="lab-test-symptoms" rows="4" value="" class="form-control" placeholder="e.g. Persistent chest pain, shortness of breath, fatigue, sweating"></textarea>
                                                                                            <span id="lab-test-symptoms_error" class="error error-html mt-2"><?php echo $errors->add_agency->first('symptoms'); ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>History<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea type="text" name="history" id="lab-test-history" value="" rows="4" class="form-control" placeholder="e.g. Smoker, diabetic, history of high blood pressure or previous cardiac issues"></textarea>
                                                                                        <span id="lab-test-history_error" class="error error-html mt-2"><?php echo $errors->add_agency->first('history'); ?></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <hr/>
                                                                            <div class="page-rightbtns cust-page-rightbtns">
                                                                                <button type="button" onclick="diagnosisLabTest();" class="btn btn-primary mr-2 cust-right-btn" id="insertButton"><i class="fa fa-magic mr-2"></i> Diagnosis</button>
                                                                                <button type="button" onclick="refresh();" class="btn btn-secondary mr-2 cust-right-btn" id=""><i class="fa fa-refresh mr-2"></i>Refresh</button>
                                                                            </div>
                                                                        </form>
                                                                        <div id="test-shimmer-loaders" class="mt-5" style="display:none">
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <h6 class="fw-bold">Reasoning:</h6>
                                                                                    <ul id="" class="list-group list-group-flush mb-3">
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                    </ul>

                                                                                    <h6 class="fw-bold text-primary">Test:</h6>
                                                                                    <ul id="" class="list-group list-group-flush mb-3">
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div id="test-div" class="mt-5" style="display:none;">
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div class="" id="suggest_result">
                                                                                        <h6 class="fw-bold">Reasoning:</h6>
                                                                                        <ul id="reasoning" class="list-group list-group-flush mb-3"></ul>

                                                                                        <h6 class="fw-bold text-primary">Test:</h6>
                                                                                        <ul id="suggest-tests" class="list-group list-group-flush mb-3"></ul>
                                                                                    </div>
                                                                                    <div id="test-no-message" style="display:none">
                                                                                        <p>Looks like there’s nothing here yet.</p>
                                                                                        <small>Try adding some proper data to <strong>Diagnosis</strong> to get meaningful results!</small>
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
                                        {{-- <div class="tab-pane" id="report-diagnosis-tab">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="box info-box card basic-detail-div">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="title">
                                                                            <h5><i class="mdi mdi-clipboard-text-outline mr-1"></i>Report Diagnosis</h5>
                                                                        </div>
                                                                        <form class="form-sample" name="report-lab-test" method="post" id="report-lab-test">
                                                                        @csrf
                                                                            <div class="row basic-detail-row">
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>Symptoms<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea type="text" name="symptoms" id="report-test-symptoms" rows="4" value="" class="form-control" placeholder="e.g. Persistent chest pain, shortness of breath, fatigue, sweating"></textarea>
                                                                                            <span id="report-test-symptoms-error" class="error-html mt-2"><?php echo $errors->add_agency->first('symptoms'); ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3">
                                                                                            <dt>Upload Report PDF<span class="error ml-1">*</span></dt>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <input type="file" class="dropify" name="report" id="report" accept="application/pdf">
                                                                                        <span id="lab-test-history_error" class="error error-html mt-2"><?php echo $errors->add_agency->first('history'); ?></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <hr/>
                                                                            <div class="page-rightbtns cust-page-rightbtns">
                                                                                <button type="button" onclick="diagnosisReportTest();" class="btn btn-primary mr-2 cust-right-btn" id="insertButton"><i class="fa fa-magic mr-2"></i> Diagnosis</button>
                                                                                <button type="button" onclick="refresh();" class="btn btn-secondary mr-2 cust-right-btn" id=""><i class="fa fa-refresh mr-2"></i>Refresh</button>
                                                                            </div>
                                                                        </form>
                                                                        <div id="test-shimmer-loaders" class="mt-5" style="display:none">
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <h6 class="fw-bold">Reasoning:</h6>
                                                                                    <ul id="" class="list-group list-group-flush mb-3">
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                    </ul>

                                                                                    <h6 class="fw-bold text-primary">Test:</h6>
                                                                                    <ul id="" class="list-group list-group-flush mb-3">
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                        <li class="shimmer-loader mb-1"></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div id="report-show-result" class="mt-5" style="display:none;">
                                                                            <div class="card shadow-sm border-success">
                                                                                <div class="card-header bg-success text-white">
                                                                                    <h5 class="mb-0"><i class="fa fa-search mr-2"></i>Prediction Results</h5>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div class="" id="report-result">
                                                                                        <h6 class="fw-bold">Summary:</h6>
                                                                                        <ul id="report_summary" class="list-group list-group-flush mb-3"></ul>

                                                                                        <h6 class="fw-bold">Diagnosis:</h6>
                                                                                        <ul id="report_diagnosis" class="list-group list-group-flush mb-3"></ul>

                                                                                        <h6 class="fw-bold text-primary">Medications:</h6>
                                                                                        <ul id="report_medications" class="list-group list-group-flush mb-3"></ul>
                                                                                    </div>
                                                                                    <div id="report-result-no-message" style="display:none">
                                                                                        <p>Looks like there’s nothing here yet.</p>
                                                                                        <small>Try adding some proper data to <strong>Diagnosis</strong> to get meaningful results!</small>
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
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div style="width: 100%;height: 217px;background-color: #f4f4f4;"></div>
</div>
@include('include/footer')
<script>
    var DIAGNOSIS = "{{url('patient/diagnosis-predict')}}";
    var DIAGNOSIS_HEALTH = "{{url('patient/diagnosis-health-predict')}}";
    var DIAGNOSIS_LAB_TEST = "{{url('patient/diagnosis-test-predict')}}";
    var REPORT_DIAGNOSIS = "{{url('patient/diagnosis-report-predict')}}";
    var CLINICAL_NOTES = "{{url('patient/diagnosis-clinical-notes')}}";
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script src="{{ asset('js/jquery.min.js')}}"></script>
<link href="{{ asset('css/jquery-ui.css')}}">
<script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script src="{{ asset('assets/modulejs/diagnosis.js')}}"></script>
<script>
    $('.dropify').dropify();
</script>