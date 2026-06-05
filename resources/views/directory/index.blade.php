@include('include/header')
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<style>
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
            <h5 class="mb-0 font-weight-bold">Directory List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span class=""></span></a>
                </div>
            </div>
        </div>
      <hr>

        <div class="col-12 grid-margin-top">
            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('success') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get('error') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
        </div>
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
                                                    <label>Full Name</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="full_name" placeholder="Enter Full Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Email</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="email" placeholder="Enter Email">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Phone</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="phone" placeholder="Enter Phone">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Department</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="department_id" placeholder="Enter Department">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Ext</label>
                                                    <input type="text" autocomplete="off" class="form-control" id="ext" placeholder="Enter Ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                             
                            </form>
                            
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search" class="btn search-btn1 searchAppoinment" id="search-data" value="Search" onclick="ajaxList(1)">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                                                                
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
                            <th>#</th>
                                <th nowrap>Full Name</th>
                                <th nowrap>Email</th>
                                <th nowrap>Phone</th>
                                <th nowrap>Department</th>
                                <th nowrap>Ext</th>
                                
                            </thead>
                            <tbody class="loading-shimmer">
                                <tr>
                                    <td colspan="13"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <span id="list_directory_id">
                    
                </span>



            </div>
        </div>
                        
                    
    </div> 
</div>
<div style="width: 100%;height: 217px;background-color: #f4f4f4;"></div>
    
    <!-- Insurance Start -->
    
    <!-- Insurance End -->

    @include('include/footer')


    
    <script>
        var _AJAX_LIST ="{{ url('ajax-directory-list')}}";
  
    </script>
<script src="{{ asset('assets/modulejs/directory.js')}}?time={{ env('timestamp')}}"></script>