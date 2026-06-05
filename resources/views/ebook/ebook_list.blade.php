@include('include/header')
@include('include/sidebar')
 
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
 </style> 
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
 <div class="main-panel">
     <div class="content-wrapper">
         <div class="page-title-main">
             <h5 class="mb-0 font-weight-bold">Ebook List</h5>
             <div class="page-rightbtns">
                 
                 <div>
                     @can('ebook-add')
                     <a href="javascript:void(0)" onclick="getEbook()" class="btn btn-primary btn-rounded btn-fw btn-sm ml-1"><i class="mdi mdi-plus"></i>Add Ebook</a>
                     @endcan
                 </div>
               
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
@include('ebook/_partial/ckeditor_js')
@include('ebook/_partial/ebook_view_modal')

@include('ebook/_partial/ebook_add_modal')
@include('ebook/_partial/ebook_edit_modal')

@include('include/footer')

<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>

<script> 
    var ISAWS = '0'; 
</script>

@if (env('FILE_UPLOAD_PERMISSION')  != 'development')
    <script> var ISAWS = '1'; </script>
@endif
    <script>
    var _EBOOK_LIST = "{{ url('ebook-list') }}";
    var _EBOOK = '{{ url("/ebook") }}'; 
    var _EBOOK_BY_ID = '{{ url("/ebook-by-id") }}'; 
    var BASEURL = "{{ asset('ebook-video') }}/";
    var _CSRF_TOKEN ='{{ csrf_token()}}';
    var _EBOOK_AWS ='{{ url("ebook-show-aws")}}';
    </script>
<script src="{{ asset('assets/modulejs/ebook.js')}}?time={{ time()}}"></script>



