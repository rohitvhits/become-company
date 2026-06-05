@include('include/header')
@include('include/sidebar')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Validation Results - Payment Log Import</h5>
        </div>

        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Records</h6>
                        <h3>{{ $summary['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Valid Records</h6>
                        <h3>{{ $summary['valid'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>Invalid Records</h6>
                        <h3>{{ $summary['invalid'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Success Rate</h6>
                        <h3>{{ $summary['total'] > 0 ? round(($summary['valid'] / $summary['total']) * 100, 2) : 0 }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invalid Records -->
        @if(count($invalidData) > 0)
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Invalid Records ({{ count($invalidData) }})</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Row #</th>
                                <th>Name</th>
                                <th>DOB</th>
                                <th>Portal ID</th>
                                <th>Vendor Name</th>
                                <th>Errors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invalidData as $invalid)
                                <tr>
                                    <td>{{ $invalid['row'] }}</td>
                                    <td>{{ $invalid['data']['name'] ?? 'N/A' }}</td>
                                    <td>{{ $invalid['data']['dob'] ?? 'N/A' }}</td>
                                    <td>{{ $invalid['data']['patient_id'] ?? 'N/A' }}</td>
                                    <td>{{ $invalid['data']['vendor_name'] ?? 'N/A' }}</td>
                                    <td>
                                        <ul class="mb-0">
                                            @foreach($invalid['errors'] as $error)
                                                <li class="text-danger">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Valid Records Preview -->
        @if(count($validData) > 0)
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Valid Records Preview (First 10)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>DOB</th>
                                <th>Portal ID</th>
                                <th>Vendor Name</th>
                                <th>Service Type</th>
                                <th>Cash</th>
                                <th>Card</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($validData, 0, 10) as $valid)
                                <tr>
                                    <td>{{ $valid['name'] }}</td>
                                    <td>{{ $valid['dob'] }}</td>
                                    <td>{{ $valid['patient_id'] }}</td>
                                    <td>{{ $valid['vendor_name'] }}</td>
                                    <td>{{ $valid['service_type'] ?? 'N/A' }}</td>
                                    <td>{{ $valid['cash']??0.00 }}</td>
                                    <td>{{ $valid['card']??0.00 }}</td>
                                    <td>{{ $valid['location'] ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <a href="{{ route('payment_log_import.mapping', $importLog->id) }}" class="btn btn-primary btn-lg mb-2 mb-md-0">
                            <i class="mdi mdi-arrow-left"></i> Back to Mapping
                        </a>
                        <a href="{{ route('payment_log_import.index') }}" class="btn btn-light btn-lg mb-2 mb-md-0">
                            <i class="mdi mdi-view-list"></i> Back to List
                        </a>
                    </div>
                    <div>
                        @if($summary['valid'] > 0)
                            <form action="{{ route('payment_log_import.confirm', $importLog->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg mb-2 mb-md-0"
                                        onclick="return confirm('Are you sure you want to import {{ $summary['valid'] }} valid records?')">
                                    <i class="mdi mdi-check-all"></i> Import {{ $summary['valid'] }} Valid Records
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('include/footer')
