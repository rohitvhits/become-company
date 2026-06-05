@include('include/header')
@include('include/sidebar')
<link href="https://cdn.rawgit.com/dubrox/Multiple-Dates-Picker-for-jQuery-UI/master/jquery-ui.multidatespicker.css">
<style>
span.select2.select2-container.select2-container--default {
    width: 100% !important;
}

.select2-container--default .select2-selection--multiple {
    border-radius: 0px !important;
    border: 1px solid #e3e7ed !important;
}
.error {
    color: red;
}

.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
#videoFrame {
    width: 455px;
    height: 315px;
    border: none;
}
.modal.fade.show {
    z-index: 1050 !important;
}

.ck-editor {
    width: 100%; /* Full width */
    margin: 0 auto; /* Center on the page */
}
.ck-editor__editable {
    min-height: 250px; /* Set a reasonable height */
    width: 100%; /* Expand to the container's width */
}

.modal-lg-plus {
    max-width: 900px; /* Set your custom width */
}

.ui-state-highlighted {
    background-color: #36a9f3 !important;
}

</style>
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Disable Date List</h5>
             <div class="page-rightbtns">
                 @if(Auth()->user()->id == 482)
                 <div>
                     @can('disable-date-add')
                     <a href="javascript:void(0)" onclick="getDisableDate()" class="btn btn-primary btn-rounded btn-fw btn-sm ml-1"><i class="mdi mdi-plus"></i>Add Disable Date</a>
                     @endcan
                 </div>
                 @endif
             </div>
         </div>

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
                                <span id="resp"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>

@include('disable_date/_partial/disable_date_add_modal')
@include('disable_date/_partial/disable_date_edit_modal')

@include('include/footer')
<script src="{{ asset('assets/modulejs/disable_date.js')}}?time={{ time()}}"></script>
<script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script>
    var DISABLE_DATE_LIST = "{{ url('disable-date-list') }}";
    var DISABLE_DATE_BY_ID = "{{ url('disable-date-by-id') }}";
    var DISABLE_DATE = '{{ url("/disable-date") }}';
    var _CSRF_TOKEN ='{{ csrf_token()}}';
    $(":input").inputmask();
</script>
