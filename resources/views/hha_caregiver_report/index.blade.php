@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link href="{{ asset('assets/css/global.css')}}" rel="stylesheet" type="text/css" />
<style>
   

    .search-inner {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        padding-right: 20px;
        padding-left: 20px;
    }

    .search-main1 {
        border-top: 1px solid #eeeeee;
        margin-left: -20px;
        margin-right: -20px;
    }

    .search-btn1,
    .search-btn1:hover,
    .search-btn1:active,
    .search-btn1:focus {
        background: #007bff !important;
        border: #007bff !important;
        border-radius: 20px;
        height: 36px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .loading-shimmer {
    animation: shimmer 2s infinite linear;
    background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
    background-size: 1000px 100%;
}

th {
 text-align: left; 
}

@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}


.circle {
  height: 70px;
  width: 70px;
  border-radius: 35px;
}

.box {
  height: 70px;
  width: 70px;  
}

.line {
  height: 20px;
  width: 300px;
}
.hide{
    display: none;
}
.appointment-list-table .table.table-bordered thead tr th, .appointment-list-table .jsgrid .table-bordered.jsgrid-table thead tr th{
    text-align: left !important;
}
</style>
<div class="main-panel main-page-box">
    @php
    $auth = auth()->user();
    @endphp
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Last 7 Days Supporting the Remaining Caregiver</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>




                </div>

            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0">
                            <form method="get" id="formsubmit" class="form-patient-list-box">
                                @csrf

                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency Name</label>
                                                    <select name="agency_fk[]" id="agency_fk"
                                                        class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100"
                                                        multiple="multiple"data-placeholder="Select Agency Name">
                                                        <?php foreach ($agencyList as $rwAgency) { ?>
                                                        <option value="<?php echo $rwAgency->id; ?>">
                                                            <?php echo $rwAgency->agency_name; ?></option>
                                                        <?php } ?>
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
                                                value="Search" onclick="ajaxList()">
                                           
                                            <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm clear"><i class="mdi mdi-reload"></i> Clear</a>
                                        </div>


                                    </div>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="card common-card-box">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="order-listing1" class="table table-bordered table-width1 shimmer_id">
                            <thead>
                                <tr>

                                    <th>ID</th>
                                    <th>Agency Name</th>
                                    <th>Total SYNC Remaining Caregiver</th>

                                </tr>
                            </thead>
                            <tbody id="response_shimmer_id">


                            </tbody>
                        </table>
                        <span id="main_response_id"></span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>


    @include('include/footer')

    <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
    <script src="{{ asset('assets/js/select2.js')}}"></script>
 

    <script>
        $(document).ready(function() {
            $("#filter-btn").click(function() {
                $("#search-filter-btn").slideToggle(600);
            });
        });

        function ajaxList(page=1){
            $('.shimmer_id').removeClass('hide')
            $('#response_shimmer_id').html("");
            $('#main_response_id').html("");
            $('#response_shimmer_id').html('<tr><td colspan="3" class="line loading-shimmer"></td></tr>')
            
            $.ajax({
                type:"GET",
                url:"{{ url('ajax-sync-remaining-caregiver')}}",
                data:{
                    'agency_id':$('#agency_fk').val(),
                    'page':page
                },
                success:function(res){
                    $('.shimmer_id').addClass('hide')
                    $('#main_response_id').html(res);
                }
            })
        }
        ajaxList();

        $('body').on('click', '.pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            ajaxList(page);
        });

        $('.clear').click(function(){
            $('#agency_fk').val("").trigger('change');
            ajaxList(1);
        })
    </script>