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

    .table-check {
        padding-left: 10px;
    }

    td.row_td {
        padding: 0 5px 0px 5px;
        padding-left: 25px;
    }
</style>
<!--main-container-part-->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="dashboard-header d-flex flex-column grid-margin">
            <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom pb-3 mb-3">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 font-weight-bold">Role # {{$id}} - {{$role["name"]}} </h4>
                    <div class="d-md-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center">
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h4>Role Details</h4>
                                </div>
                                <div class="col-sm-9">
                                    <a class="pull-right btn btn-danger btn-rounded btn-sm d-none d-md-block ml-1 deletRole" href="javascript:void(0)" data-did="{{ $role->id }}"><i class="fa fa-trash"></i> Delete</a>

                                    <a href="{{ url('roles') }}/{{ $role->id }}/edit" class="btn btn-primary btn-sm btn-fw pull-right btn-rounded ml-1"><i class="mdi mdi-pencil"></i> Edit</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="profile-feed">
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- DataTales Example -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="card-body pl-0">
                                    <div class="form-group row">
                                        <label for="name" class="col-sm-1 card-title">Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-user col-sm-11" id="name" name="name" value="{{ $role->name }}" disabled maxlength="25" aria-describedby="nameHelp" placeholder="Enter Name">
                                    </div>

                                    <div class="card-body p-0">
                                        <h4 class="mb-2 card-title">Permission<span class="text-danger">*</span></h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-gray role_view" width="100%" cellspacing="0">
                                                <tbody>
                                                    @php $i=1; @endphp
                                                    @foreach ($permission as $key => $role)
                                                    @php 
                                                        $divideVal = count($role['value'])/4;
                                                        $checkCount = count($role['value'])-1;
                                                    @endphp
                                                    <tr>
                                                        <td rowspan="{{ceil($divideVal)}}" style="background: #dadcdf4d;"><strong>{{ ucwords(str_replace('-', ' ', $role['module_name'])) }}</strong>
                                                        </td>

                                                        @for ($i = 0; $i < 39; $i++) @if (!empty($role['value'][$i])) @if ($i==4 || $i==8) <tr>
                                                            @endif
                                                            <td class="row_td" style="white-space: nowrap;" colspan="{{$checkCount==$i ? (13-ceil($divideVal)):''}}">
                                                                <div class="form-check custom-check table-check">
                                                                    <!-- <input class="form-check-input checkinput" name="permission[]" type="checkbox" id="permission" value="{{ isset($role['value'][$i]->id) ? $role['value'][$i]->id : '' }}" @if (in_array($role['value'][$i]->id, $rolePermissions)) checked @endif>
                                                                    <label for="defaultCheck1">{{ isset($role['value'][$i]->name) ? ucwords(str_replace('-', ' ', $role['value'][$i]->name)) : '' }}
                                                                    </label> -->

                                                                    <label class="form-check-label">
                                                                        <input type="checkbox" class="form-check-input checkinput" name="permission[]" type="checkbox" id="permission" value="{{ isset($role['value'][$i]->id) ? $role['value'][$i]->id : '' }}" @if (in_array($role['value'][$i]->id, $rolePermissions)) checked @endif disabled>
                                                                        {{ isset($role['value'][$i]->name) ? ucwords(str_replace('-', ' ', $role['value'][$i]->name)) : '' }}
                                                                        <i class="input-helper"></i></label>
                                                                </div>
                                                            </td>
                                                            @endif
                                                            @if ($i == 3 || $i == 7 || $i == 11 || $i == 15 || $i == 19 || $i == 23 || $i ==27 || $i ==31 || $i ==35  || $i ==39)
                                                    </tr>
                                                    @endif
                                                    @endfor
                                                    </tr>
                                                    @php $i++; @endphp
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-title-main">
                <h5 class="mb-0 font-weight-bold">Role Logs</h5>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12" id="logList" style="display:flex;justify-content:center;">
                                    <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag" style="display: none; ">
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
                url: "{{ url('/role-ajax') }}" + "?page=" + page,
                data: {
                    'id': "{{ $id }}",
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
    <script>
        $(document).on("click", ".deletRole", function() {
            var id = $(this).attr('data-did');
            deleteRole(id);
        });

        function deleteRole(id) {
            var upUrl = "{{ route('roles.destroy', 'id') }}";
            var redirectUrl = "{{ url('roles') }}";
            Swal.fire({
                title: 'Are you sure?',
                text: "you want to delete this role?",
                type: "warning",
                showCancelButton: !0,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                confirmButtonClass: "btn btn-success mt-2",
                cancelButtonClass: "btn btn-danger ml-2 mt-2",
                buttonsStyling: !1
            }).then((result) => {
                var url = upUrl;
                url = url.replace('id', id);
                if (result.value) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        async: false,
                        url: url,
                        type: "DELETE",
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(r) {
                            window.location.href = redirectUrl;
                        }
                    });
                } else {
                    return false;
                }
            });
        }
    </script>