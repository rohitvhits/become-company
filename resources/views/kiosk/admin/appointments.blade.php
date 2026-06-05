@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
    rel="stylesheet" />
<style>
.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.agency-filter-toggle-wrapper {
    display: inline-flex;
    align-items: center;
    margin-left: 8px;
    gap: 6px;
}

.agency-toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 4px;
    border: 2px solid #ddd;
    background: #fff;
    cursor: pointer;
    transition: all 0.25s ease;
    padding: 0;
    vertical-align: middle;
    position: relative;
    flex-shrink: 0;
}

.agency-toggle-btn i {
    font-size: 18px;
    line-height: 1;
    pointer-events: none;
    display: block;
}

/* Blue/Grey - Professional & Clear (Current Active) */
.agency-toggle-btn[data-mode="include"] {
    background-color: #cfe2ff !important;
    border-color: #0d6efd !important;
    color: #084298 !important;
}

.agency-toggle-btn[data-mode="include"]:hover {
    background-color: #b6d4fe !important;
    transform: scale(1.05);
}

.agency-toggle-btn[data-mode="exclude"] {
    background-color: #e9ecef !important;
    border-color: #6c757d !important;
    color: #495057 !important;
}

.agency-toggle-btn[data-mode="exclude"]:hover {
    background-color: #dee2e6 !important;
    transform: scale(1.05);
}
.agency-toggle-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

.agency-toggle-btn:active {
    transform: scale(0.95);
}

.agency-toggle-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: color 0.25s ease;
    user-select: none;
    white-space: nowrap;
}

/* Match label colors with button colors */
.agency-toggle-label.mode-include {
    color: #0d6efd;
}

.agency-toggle-label.mode-exclude {
    color: #6c757d;
}

<style>
        * {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-container {
            min-height: 100vh;
            background: #eee;
            /* background: #f8fafc url('https://html.laralink.com/prohealth/assets/img/home_1/department_bg.svg') no-repeat center center; */
            background-size: cover;
            background-attachment: fixed;
        }

        .header-nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.2s ease;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .header-nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .header-nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
        }

        .main-content {
            min-height: calc(100vh - 70px);
        }

        .admin-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.1);
        }

        .admin-btn {
            min-height: 44px;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            cursor: pointer;
            border: none;
        }

        .admin-btn-primary {
            background: linear-gradient(135deg, #7895d5 0%, #3557c5 100%);
            color: white;
        }

        .admin-btn-primary:hover {
            background: linear-gradient(135deg, #7895d5 0%, #3557c5 100%);
        }

        .admin-btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .admin-btn-secondary:hover {
            background: #e2e8f0;
        }

        .admin-input {
            min-height: 44px;
            font-size: 0.875rem;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            padding: 0 1rem;
            width: 100%;
            transition: all 0.2s ease;
            background: white;
        }

        .admin-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            background: #f8fafc;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .admin-table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .admin-table tr:hover td {
            background: #f8fafc;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-checked_in {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</style>
<div class="main-panel main-page-box" style="margin-bottom:15%">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Kiosk Admin</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i
                            class="mdi mdi-filter-outline"></i>Filter <span></span></a>
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                                    <input type="text" name="search" id="search_data" value="{{ request('search') }}" class="form-control"
                                                        placeholder="Token, Name, Mobile...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                    <select name="status" id="status" class="form-control select2-design cal-padding-0 w-100">
                                                        <option value="">All Status</option>
                                                        <option value="checked_in" {{ request('status')=='checked_in' ? 'selected' : '' }}>Check-in</option>
                                                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                                    <select name="location" id="location" class="form-control">
                                                        <option value="">All Locations</option>
                                                        @foreach($locations as $key => $location)
                                                        <option value="{{ $key }}">
                                                            {{ $location ?? '' }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                                    <input type="text" name="created_date" readonly value="" class="datepickernn form-control" id="created_date" placeHolder="Created Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search" onclick="loadAppointment(1)">

                                        <a href="javascript:void(0)" class="btn btn-light btn-rounded btn-fw btn-sm" onclick="refresh()"><i class="mdi mdi-reload"></i> Clear</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id hideClass" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Token No</th>
                                    <th>Patient Name</th>
                                    <th>Mobile</th>
                                    <th>Location</th>
                                    <th>Services</th>
                                    <th>Docs</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody class="shimmer-loader">
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <span id="ajax_response_id"></span>
            </div>
        </div>
    </div>
</div>
@include('include/footer')
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
<script>
    var _KIOSK_AJAX = "{{ url('kiosk/admin/appointments-ajax-list')}}";
</script>
<script type="text/javascript" src="{{ asset('/assets/modulejs/kiosk/appointment.js')}}"></script>