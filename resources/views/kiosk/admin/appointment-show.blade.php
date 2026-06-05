@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
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
</style>
<div class="main-panel main-page-box" style="margin-bottom:15%">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Patient Information</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                    <a href="{{ route('admin.appointments') }}"
                        class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Appointments
                    </a>
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Patient Information -->
                    <div class="admin-card">
                        <div class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                            <h3 class="font-bold text-gray-800">Patient Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Full Name</label>
                                    <p class="font-medium text-gray-800">{{ $appointment['first_name'] }} {{ $appointment['middle_name']
                            }} {{ $appointment['last_name'] }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Mobile</label>
                                    <p class="font-medium text-gray-800">{{ $appointment['mobile'] }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Phone</label>
                                    <p class="font-medium text-gray-800">{{ $appointment->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Email</label>
                                    <p class="font-medium text-gray-800">{{ $appointment->email ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Date of Birth</label>
                                    <p class="font-medium text-gray-800">{{ $appointment->dob ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Gender</label>
                                    <p class="font-medium text-gray-800">{{ $appointment->gender ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="admin-card">
                        <div class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                            <h3 class="font-bold text-gray-800">Address</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="text-xs text-gray-500 uppercase">Street Address</label>
                                    <p class="font-medium text-gray-800">{{ $appointment->address1 ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">City</label>
                                    <p class="font-medium text-gray-800">{{ $appointment->city ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">State</label>
                                    <p class="font-medium text-gray-800">{{ $appointment->state ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase">Zip Code</label>
                                    <p class="font-medium text-gray-800">{{ $appointment->zip_code ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="admin-card">
                        <div class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                            <h3 class="font-bold text-gray-800">Uploaded Documents</h3>
                        </div>
                        <div class="p-6">
                            @if($appointmentDocuments->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($appointmentDocuments as $document)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $document->original_name }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($document->file_size / 1024, 1) }} KB</p>
                                    </div>
                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p>No documents uploaded</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Token & Status -->
                    <div class="admin-card">
                        <div class="p-6 text-center">
                            <p class="text-sm text-gray-500 mb-1">Token Number</p>
                            <p class="text-3xl font-bold text-blue-600 mb-4">{{ $appointment->token_no ?? 'N/A' }}</p>
                            <span class="status-badge status-{{ $appointment->status }} text-sm px-4 py-1">
                                @if($appointment->status == 'checked_in')
                                Check-in
                                @else
                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Appointment Info -->
                    <div class="admin-card">
                        <div class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                            <h3 class="font-bold text-gray-800">Appointment Info</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Type</label>
                                <p class="font-medium text-gray-800">{{ $appointment->type }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Services</label>
                                <div class="mt-1">
                                    @if(is_array($appointment->service_id) && count($appointment->service_id) > 0)
                                    @foreach($appointment->service_id as $serviceId)
                                    <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded mb-1 mr-1">
                                        {{ $servicesMap[$serviceId] ?? 'Service '.$serviceId }}
                                    </span>
                                    @endforeach
                                    @else
                                    <p class="font-medium text-gray-800">-</p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Agency ID</label>
                                <p class="font-medium text-gray-800">{{ $appointment->agency_id ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Location</label>
                                <p class="font-medium text-gray-800">{{ $locationsMap[$appointment->location_id] ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Language</label>
                                <p class="font-medium text-gray-800">{{ $languagesMap[$appointment->language] ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Insurance</label>
                                <p class="font-medium text-gray-800">{{ $insurancesMap[$appointment->insurance_name] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="admin-card">
                        <div class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                            <h3 class="font-bold text-gray-800">Timestamps</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Created At</label>
                                <p class="font-medium text-gray-800">{{ $appointment->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Check-in At</label>
                                <p class="font-medium text-gray-800">{{ $appointment->checked_in_at ?
                        $appointment->checked_in_at->format('M j, Y g:i A') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@include('include/footer')
<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>