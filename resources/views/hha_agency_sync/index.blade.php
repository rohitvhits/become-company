@include('include/header')
@include('include/sidebar')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">HHA Agencies SYNC List</h5>
            <div class="page-rightbtns">

            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card search-card1" >
                    <div class="card-body">
                        <span class="table_response_id"></span>
                    </div>

                </div>
            </div>
        </div>
        
    </div>
    
</div>
<script>
var _AJAX_LIST = "{{ url('hha-sync-agency-ajax') }}";
    </script>
<script src="{{ asset('assets/modulejs/hhaAgencySync/hhaAgencySync.js')}}"></script>
@include('include/footer')
