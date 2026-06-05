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
            <h5 class="mb-0 font-weight-bold">Services List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                @can('service-master-add')
                         <a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal-add-services" id="link-add-services" data-whatever="@mdo" onclick="resetAddService()" class="btn btn-primary cust-right-btn"><i
                                 class="mdi mdi-plus"></i>Add Service</a>
                     @endcan

                    

                     <a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                             class="mdi mdi-reload"></i>
                         Reset</a>

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
                                                    <label for="service_name">Service Name</label>
                                                    <input type="text" name="service_name" class="form-control" id="service_name" placeholder="Enter Service Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="type">Type</label>
                                                    <select name="type" id="type" class="form-control">
                                                        <option value="">Select Type </option>
                                                        <option value="Caregiver">Caregiver</option>
                                                        <option value="Patient">Patient</option>
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
                                    <th>Type</th>
                                    <th>Service Name</th>
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
@include('service_master._partial.create_service_modal')
@include('service_master._partial.edit_service_modal')
@include('include/footer')

<script>
   var _LOAD_DATA_URL = "{{ url('service-master/ajax-list')}}";
   var _CSRF_TOKEN ="{{ csrf_token()}}";
   var _SAVE_SERVICES="{{ url('service-master/save')}}";
   var _EDIT_SERVICES="{{ url('service-master/edit')}}";
   var _UPDATE_SERVICES="{{ url('service-master/update')}}";
   var _DELETE_SERVICES="{{ url('service-master/delete')}}";
   var _ENABLED_SERVICE="{{ url('service-master/enabled-service')}}";
   var _ENABLE_NYBEST_USER="{{ url('service-master/enabled-nybest-user')}}";
</script>

<script src="{{ asset('assets/modulejs/service_master/service_master.js')}}?time={{ time()}}"></script>