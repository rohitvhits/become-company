@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }} ">
<link rel="stylesheet" href="{{ asset('assets/css/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css') }}">
<style>
.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

/* Badge pulse animation */
.badge-pulse {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.3);
    }
    100% {
        transform: scale(1);
    }
}

/* Highlight animations for table rows */
.highlight-success {
    background-color: #d4edda !important;
    transition: background-color 1.5s ease-out;
}

.highlight-danger {
    background-color: #f8d7da !important;
    transition: background-color 0.3s ease-in;
}

/* Row fade in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.token-input-input-token input{
    width: 422px !important;
}
</style>
<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Zip Code</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                </div>
            </div>
        </div>
        <hr/>
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
                                                    <label>Zip Code</label>
                                                    <input type="text" name="zipcode" class="form-control" placeholder="Search zipcode..." value="" id="zipcode">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>County</label>
                                                    <select name="county" id="county" class="form-control" placeholder="Select County">
                                                        <option value="">Select County</option>
                                                        @foreach($countyList as $cou)
                                                            <option value="{{$cou->county}}">{{$cou->county}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-row-gap mt-3">
                                    <div class="col-md-9">
                                        <div class="appointment-btn-box" style="justify-content:left !important">
                                            <input type="button" name="search"
                                                class="btn search-btn1 searchAppoinment" id="search-data"
                                                value="Search" onclick="loadZipCode(1)">
                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh();"><i class="mdi mdi-reload"></i> Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Department Table -->
                        <div class="table-responsive" id="zipcode-wise-data-loader">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>County</th>
                                        <th>Zip Code</th>
                                        <th>Enable SMS</th>
                                    </tr>
                                </thead>
                                <tbody class="shimmer-loader">
                                    <tr>
                                        <td colspan="4"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span id="zipcode-list"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('include/footer')
<script>
    var ZIPCODE_AJAX = "{{ url('setting/zipcode-master/ajax-list') }}";
    var ZIPCODE_STATUS = "{{ url('setting/zipcode-master/status-update') }}";
    var _CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script type="text/javascript" src="{{ asset('assets/modulejs/zipcode/zipcode.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>