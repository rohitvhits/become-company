@include('include/header')

<link rel="stylesheet" href="{{ asset('assets/vendors/fancybox/fancybox.css') }}"/>

<style>
    /* ── Page Header ── */
    .ann-page-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    .ann-page-header h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
    }
    .ann-unread-badge {
        background: #e53e3e;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
    }

    /* ── Card wrapper ── */
    .ann-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(0,0,0,.08);
    }
    .ann-card .card-body { padding: 0; }

    /* ── Table ── */
    #announcement-listing thead th {
        background: #f7f8fc;
        color: #5a6278;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 2px solid #e2e8f0;
        padding: 13px 14px;
        white-space: nowrap;
    }
    #announcement-listing tbody tr {
        transition: background .15s;
    }
    #announcement-listing tbody tr:hover {
        background: #f0f4ff !important;
    }
    #announcement-listing tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 13.5px;
        color: #2d3748;
        border-color: #edf2f7;
    }
    #announcement-listing tbody tr.ann-unread {
        background: #fffbeb;
        font-weight: 600;
    }
    #announcement-listing tbody tr.ann-unread td:first-child {
        border-left: 3px solid #f6ad55;
    }

    /* ── Title link ── */
    .ann-title-link {
        color: #3a7bd5;
        text-decoration: none;
        font-weight: 600;
        font-size: 13.5px;
        cursor: pointer;
    }
    .ann-title-link:hover { text-decoration: underline; color: #2563eb; }

    /* ── Description preview ── */
    .ann-desc-preview {
        color: #718096;
        font-size: 12.5px;
        margin-top: 2px;
    }

    /* ── Badges ── */
    .badge-new {
        background: #fed7d7;
        color: #c53030;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 20px;
        letter-spacing: .3px;
    }
    .badge-read-status {
        background: #e2e8f0;
        color: #718096;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 9px;
        border-radius: 20px;
    }
    .badge-media {
        background: #ebf8ff;
        color: #2b6cb0;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 9px;
        border-radius: 20px;
        cursor: pointer;
    }
    .badge-media:hover { background: #bee3f8; }

    /* ── Action buttons ── */
    .ann-action-btn {
        background: none;
        border: none;
        padding: 4px 7px;
        border-radius: 6px;
        cursor: pointer;
        transition: background .15s;
        font-size: 14px;
    }
    .ann-action-btn:hover { background: #e2e8f0; }
    .ann-btn-view { color: #3a7bd5; }
    .ann-btn-check { color: #38a169; }
    .ann-btn-done { color: #a0aec0; cursor: default; }

    /* ── Empty state ── */
    .ann-empty {
        padding: 50px 20px;
        text-align: center;
        color: #a0aec0;
    }
    .ann-empty i { font-size: 36px; margin-bottom: 10px; display: block; }
    .ann-empty p { margin: 0; font-size: 14px; }

    /* ── Modal ── */
    #viewAnnouncementUserModal .modal-header {
        background: linear-gradient(135deg, #334155, #1e293b);
        color: #fff;
        border-radius: 8px 8px 0 0;
        padding: 16px 20px;
    }
    #viewAnnouncementUserModal .modal-header .close {
        color: #fff;
        opacity: .8;
        font-size: 22px;
    }
    #viewAnnouncementUserModal .modal-header .close:hover { opacity: 1; }
    #viewAnnouncementUserModal .modal-title { font-weight: 700; font-size: 16px; }
    #viewAnnouncementUserModal .modal-content {
        border: none;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0,0,0,.15);
    }
    #viewAnnouncementUserModal .section-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #718096;
        margin-bottom: 6px;
    }
    #viewAnnouncementUserModal .section-box {
        background: #f7f8fc;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13.5px;
        color: #2d3748;
        line-height: 1.6;
        border: 1px solid #e2e8f0;
    }
    #viewAnnouncementUserModal .steps-box {
        background: #f0fff4;
        border: 1px solid #c6f6d5;
        border-left: 4px solid #38a169;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13.5px;
        color: #22543d;
        line-height: 1.6;
    }
    /* ── Media grid in modal ── */
    .ann-media-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 4px;
    }
    .ann-media-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: border-color .15s, transform .15s;
        width: 100px;
        height: 80px;
        display: block;
        text-decoration: none;
    }
    .ann-media-item:hover { border-color: #3a7bd5; transform: scale(1.03); }
    .ann-media-item img,
    .ann-media-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .ann-media-item .play-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,.35);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
    }
    /* ── Meta info in modal ── */
    .ann-modal-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 12px;
        color: #a0aec0;
        margin-top: 6px;
    }
    .ann-modal-meta span { display: flex; align-items: center; gap: 4px; }
</style>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="ann-page-header">
            <h5><i class="fa fa-bullhorn mr-2" style="color:#3a7bd5;"></i>Announcements</h5>
            <span class="ann-unread-badge" id="ann-unread-count" style="display:none;"></span>
        </div>

        <div class="card ann-card mb-4">
            <div class="card-body">
                <div class="table-responsive" id="announcement-container">
                    <div class="ann-empty">
                        <i class="fa fa-spinner fa-spin"></i>
                        <p>Loading announcements...</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- View Announcement Modal --}}
<div class="modal fade" id="viewAnnouncementUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="ann_modal_title">Announcement</h5>
                    <div class="ann-modal-meta">
                        <span><i class="fa fa-calendar"></i> <span id="ann_modal_date"></span></span>
                        <span id="ann_modal_status_wrap"></span>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 12px 16px;">

                <div class="mb-2">
                    <div class="section-label">Message</div>
                    <div class="section-box" id="ann_modal_description"></div>
                </div>

                <div class="mb-2" id="ann_modal_steps_wrap" style="display:none;">
                    <div class="section-label">Summary</div>
                    <div class="steps-box" id="ann_modal_steps"></div>
                </div>

                <div id="ann_modal_media_wrap" style="display:none;">
                    <div class="section-label">Attachments</div>
                    <div class="ann-media-grid" id="ann_modal_media"></div>
                </div>

            </div>
            <div class="modal-footer" style="border-top: 1px solid #edf2f7; padding: 8px 16px;">
                <button type="button" id="ann_modal_mark_read_btn" class="btn btn-success btn-sm" style="display:none;">
                    <i class="fa fa-check mr-1"></i> Mark as Read
                </button>
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Video Player Modal --}}
<div class="modal fade" id="annVideoPlayerModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="background:#000; border:none; border-radius:10px; overflow:hidden;">
            <div class="modal-header" style="background:#1a1a2e; border:none; padding:10px 16px;">
                <h6 class="modal-title text-white" style="font-size:13px;">
                    <i class="fa fa-play-circle mr-1" style="color:#3a7bd5;"></i>
                    <span id="ann_video_title">Video</span>
                </h6>
                <button type="button" class="close text-white" id="ann_video_close_btn" style="opacity:.8; font-size:20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:0; background:#000;">
                <video id="ann_video_player"
                       controls
                       style="width:100%; max-height:70vh; display:block; background:#000;"
                       preload="metadata">
                    <source id="ann_video_source" src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="modal-footer" style="background:#1a1a2e; border:none; padding:8px 16px; justify-content:flex-end;">
                <button type="button" class="btn btn-sm btn-secondary" id="ann_video_close_btn2">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@include('include/footer')

<script src="{{ asset('assets/vendors/fancybox/fancybox.umd.js') }}"></script>
<script src="{{ asset('assets/modulejs/announcement_user/announcement_list.js')}}?time={{ time()}}"></script>
<script>
    var _AJAX_LIST    = "{{ url('announcement-list-ajax') }}";
    var _MARK_READ    = "{{ url('mark-announcement-as-read') }}";
    var _SHOW_URL     = "{{ url('announcement-master') }}";
    var _MEDIA_URL    = "{{ url('announcement-media-show') }}";
    var _CSRF_TOKEN   = '{{ csrf_token() }}';
    var _ANN_COUNT    = "{{ url('get-announcement-count') }}";
    var ISAWS         = '{{ env('FILE_UPLOAD_PERMISSION') != 'development' ? '1' : '0' }}';
</script>
