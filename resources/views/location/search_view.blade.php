@include('include/header')
 @include('include/sidebar')
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwr1n4QpMo-f6fjd8THMLRyqrFk7iZcA8&libraries=places,geometry,drawing,marker&callback=initAutocomplete&v=weekly" async defer></script>
 <style type="text/css">
     #order-listing_length,
     #order-listing_paginate,
     #order-listing_info {
         display: none;
     }
     #order-listing_filter {
         text-align: right;
     }
     .select2-design+.select2.select2-container.select2-container--default {
         width: 100% !important;
     }
     td {
         table-layout: fixed;
         width: 20px;
         overflow: hidden;
         word-wrap: break-word;
     }
     .table-width1 tr th:first-child {
         width: 3%;
     }
     .table-width1 tr th:nth-child(5) {
         width: 10%;
     }
     .table-width1 tr th:nth-child(6) {
         width: 10%;
     }
     .table-width1 tr th:nth-child(7) {
         width: 10%;
     }
     .table-width1 {
         background-color: #fff;
     }
     .search-inner {
         display: flex;
         justify-content: space-between;
         padding-top: 10px;
         padding-right: 20px;
         padding-left: 20px;
     }
     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
     }
     .srch-icon {
         padding: 0 !important;
         width: 40px;
         height: 40px;
     }
 </style>
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Search Location</h5>
             <div class="page-rightbtns">
             </div>
         </div>
         <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-Campaign">
                            <label><b>Location Name</b> </label>
                            <input id="ship-address" class="form-control" name="ship-address" required autocomplete="off" onChange="searchData()" />
                            <span style="color:red" id="nerror"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img src="{{ asset('/ajax-loader.gif')}}" class="order-listing-loader1" alt="loader" id="loaderDashboardGraph" style="display:none;margin-top: 31px;">
                    </div>
                </div><br>
            </div>
         </div>
        &nbsp;
        <div class="card">
            <div class="card-body compact-view">
                <div class="row">
                    <div class="col-12">
                        <div class="wmd-view-topscroll">
                            <div class="scroll-div1">
                            </div>
                        </div>
                        <div class="wmd-view">
                            <div class="scroll-div2">
                            <div class="row" id="show_otheer_details">
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
     </div>
     <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

    <script>
        var _SEARCH_LOCATION_AJAX = "{{ url('/location-search-ajax-list')}}";
    </script>
    <script src="{{asset('assets/modulejs/location/location_search_list.js')}}?time={{ time()}}"></script>
     @include('include/footer')