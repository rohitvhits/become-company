@include('include/header')

<style>
    .add-field {
        display: flex;
        justify-content: end;
    }

    .add-field a {
        height: 36px;
        border-radius: 50px;
        line-height: 17px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .media-preview-item {
        max-width: 80px;
        max-height: 80px;
        border-radius: 5px;
        margin: 5px;
        object-fit: cover;
    }

    /* Media preview in modals */
    .media-preview {
        max-width: 100px;
        max-height: 100px;
        border-radius: 5px;
        margin: 5px;
        object-fit: cover;
    }

    #media-preview img,
    #media-preview video,
    #edit-media-preview img,
    #edit-media-preview video {
        max-width: 100px;
        max-height: 100px;
        border-radius: 5px;
        margin: 5px;
        object-fit: cover;
    }

    /* Fix CKEditor dropdowns in Bootstrap modal */
    .ck.ck-balloon-panel {
        z-index: 10055 !important;
    }
    .ck.ck-dropdown__panel {
        z-index: 10055 !important;
    }
    .ck-body-wrapper {
        z-index: 10055 !important;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Announcement Master</h5>
            <div class="page-rightbtns">
                <div class="add-field">
                    @can('announcement-master-create')
                    <a class="btn btn-primary btn-rounded btn-fw btn-sm" href="javascript:void(0)"
                        onclick="openCreateModal()">
                        <i class="mdi mdi-plus"></i> Add Announcement
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="col-12 grid-margin-top">
            @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if (Session::has('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('warning') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if (Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <div id="resp"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('announcement_master/_partial/create_modal')
@include('announcement_master/_partial/edit_modal')
@include('announcement_master/_partial/view_modal')
@include('announcement_master/_partial/ckeditor_js')

@include('include/footer')

<script src="{{ asset('assets/modulejs/announcement_master/announcement_master.js')}}?time={{ time()}}"></script>
<script>
    var _AJAX_LIST = "{{ url('announcement-master-ajax-list')}}";
    var _STORE_URL = "{{ url('announcement-master')}}";
    var _UPDATE_URL = "{{ url('announcement-master')}}";
    var _DELETE_URL = "{{ url('announcement-master')}}";
    var _PUBLISH_URL = "{{ url('announcement-master-publish')}}";
    var _SHOW_URL = "{{ url('announcement-master')}}";
    var _DELETE_MEDIA_URL = "{{ url('announcement-master-media')}}";
    var _MEDIA_SHOW_URL = "{{ url('announcement-media-show')}}";
    var _CSRF_TOKEN = '{{ csrf_token()}}';
    var ISAWS = '{{ env('FILE_UPLOAD_PERMISSION') != 'development' ? '1' : '0' }}';
    var BASEURL = "{{ asset('') }}";
</script>