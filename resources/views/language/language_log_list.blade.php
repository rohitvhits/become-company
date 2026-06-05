@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="<?php echo URL::to('/'); ?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
<link href="<?php echo URL::to('/'); ?>/assetsd/css/vertical-layout-light/jquery.timepicker.css" rel="stylesheet" type="text/css">
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<style>
    .mini-card .form-control {
        height: 20px;
        padding: 2px;
    }

    dl {
        margin-top: 0;
        margin-bottom: 20px;
    }

    ul,
    ol,
    dl {
        padding-left: 0px !important;
    }

    .dl-horizontal dt {
        float: left;
        width: 72px;
        clear: left;
        text-align: right;
        /* overflow: hidden; */
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dl-horizontal dt {
        float: left;
        width: 85px;
        clear: left;
        text-align: right;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #otherupdated_id {
        width: 750px;
    }

    #other_id {
        width: 750px;
    }

    h6.fm_1 {
        /* text-align: end;*/
        font-size: 14px;
    }

    dt {
        font-weight: 700;
    }

    .dl-horizontal dd {
        margin-left: 90px;
        margin-bottom: 0px;
    }

    .ml-3,
    .rtl .settings-panel .sidebar-bg-options .rounded-circle,
    .rtl .settings-panel .sidebar-bg-options .color-tiles .tiles,
    .rtl .settings-panel .color-tiles .sidebar-bg-options .tiles,
    .mx-3 {
        margin-left: 1rem !important;
        width: 100%;
    }

    #hr2 .dl-horizontal dd {
        margin-left: 110px;
    }

    #hr2 .dl-horizontal dt {
        width: 101px;
    }

    .profile-feed-item.abc {
        padding: 0;
        border: none;
    }

    .profile-feed-item.border {
        border: none;
    }

    .htv {
        height: 50%;
    }

    .removeSpace {
        margin-top: 0px !important;
        margin-bottom: 0px !important
    }

    #loadersId {
        float: left
    }

    .tab-content {
        padding: 0.5rem;
    }

    .alert-warning {
        color: #856404;
        background-color: #fff3cd;
        border-color: #ffeeba;
    }

    .error {
        color: red;
    }

    #Commsas::first-letter {
        text-transform: uppercase;
    }

    #next_apid {
        padding-left: 38px;
    }

    #dates_id {
        margin-bottom: 0px !important;
        margin-left: -10px;
    }

    #month_id {
        margin-bottom: 0px !important;
        margin-left: -10px;
    }

    .test_id {
        display: none;
    }

    .deleted {
        background: #ddd5d5;
        color: #f70707;
        opacity: 0.5;
    }

    .deleted>td>a {
        color: #f70707;
    }
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>
<!--main-container-part-->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Language Logs</h5>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12" id="logList" style="display:flex;justify-content:center;">
                                <img src="{{asset('/ajax-loader.gif')}}" alt="loader" id="loadertag" style="display: none; ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>  
    @include('include/footer')
    <script src="<?= URL::to('assets/js/jquery.min.js') ?>"></script>
    <script>
        $(document).on('click', '.log-pegination .pagination a', function(event) {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });
        $(document).ready(function() {
            $('#loadertag').show();
            getData(1);
        });

        function getData(page) {
            var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

            $.ajax({
                method: 'GET',
                url: "{{ url('/language/log/ajax') }}"+"?page=" + page,
                data: {
                    'id': "{{$id}}",
                    '_token': "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $('#loadertag').show();
                },
                success: function success(response) {

                    $('#loadertag').hide();
                    $('#logList').html("");
                    $('#logList').html(response);
                },
                error: function error(_error) {
                    console.error(_error);
                    toastr.error('Something happened. Try again');
                }
            });
        }
    </script>
    