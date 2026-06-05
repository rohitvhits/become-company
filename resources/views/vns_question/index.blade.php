@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
    .actions {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">VNS Question List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    @can('add-vns-question')
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal-add-vns-question" id="link-add-question" data-whatever="@mdo" onclick="resetAddQuestion()" class="btn btn-primary cust-right-btn"><i
                            class="mdi mdi-plus"></i>Add VNS Question</a>
                    @endcan
                   

                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="question_name">Question Name</label>
                                                    <input type="text" name="question_name" class="form-control" id="question_name" placeholder="Enter Question Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="template_type">Template Type</label>
                                                    <select name="template_type" id="template_type" class="form-control">
                                                        <option value="">Select Template Type</option>
                                                        @if(isset($templates) && count($templates) > 0)
                                                            @foreach($templates as $template)
                                                                <option value="{{ $template->id }}">{{ $template->template_name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search" onclick="loadAjaxList()">
                                        
                                        <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                                            class="mdi mdi-reload"></i>
                                        Reset</a>
                                        @can('export-vns-question')
                                        <a href="javascript:void(0)" class="btn btn-warning cust-right-btn" onclick="exportCSV()"><i
                                            class="mdi mdi-download"></i>
                                        Export CSV</a>
@endcan
                                        
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                   <th>No</th>
                                    <th>Question Name</th>
                                    <th>Template Type</th>
                                    <th>Created Date / Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <span id="response_requested_id">

                </span>



            </div>
        </div>

    </div>
<div style="color:red" id="blank_div" class="mt-5">

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</div>
</div>
@include('vns_question._partial.create_question_modal')
@include('vns_question._partial.edit_question_modal')

@include('include/footer')

<script>
   var _LOAD_DATA_URL = "{{ url('vns-question/data/list')}}";
   var _CSRF_TOKEN ="{{ csrf_token()}}";
   var _DELETE_VNS_QUESTION="{{ url('vns-question')}}";
   var _SAVE_QUESTION="{{ url('vns-question/save')}}";
   var _EDIT_QUESTION="{{ url('vns-question/edit')}}";
   var _UPDATE_QUESTION="{{ url('vns-question/update')}}";
   var _EXPORT_CSV="{{ url('vns-question/export-csv')}}";
</script>

<script src="{{ asset('assets/modulejs/vns_question/vns_question.js')}}?time={{ env('timestamp') }}"></script>
