@include('include/header')
@include('include/sidebar')
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
 <link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/modulejs/css/ratecard/rate_card.css?time={{ env('timestamp')}}">
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Rate Card List</h5>
             <div class="page-rightbtns">
                 
                 <div>
                     @can('rate-card-add')
                     <a href="javascript:void(0)" onclick="getRateCard()" class="btn btn-primary btn-rounded btn-fw btn-sm ml-1"><i class="mdi mdi-plus"></i>Add Rate Card</a>
                     @endcan
                 </div>
               
             </div>
         </div>
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
                            <div class="rate-card-wise-data-loader" style="display:flex">
                                <table id="" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Services</th>
                                            <th>Amount</th>
                                            <th>Created Date/ Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="line loading-shimmer" colspan="5"></td></tr>
                                    </tbody>
                                </table>
                            </div>
                                <span id="resp"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
@include('rateCard/_partial/rate_card_add_modal')
@include('rateCard/_partial/rate_card_edit_modal')

@include('include/footer')

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script>
    var _RATECARD_LIST = "{{ url('rate-card-list') }}";
    var _RATECARD = '{{ url("/rate-card") }}'; 
    var _RATECARD_BY_ID = '{{ url("/rate-card-by-id") }}'; 
    var _CSRF_TOKEN ='{{ csrf_token()}}';
</script>
<script src="{{ asset('assets/modulejs/rate_card/rate_card.js')}}?time={{ time()}}"></script>



