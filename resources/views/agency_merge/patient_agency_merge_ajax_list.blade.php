@if(isset($validation_error))
    <div class="alert alert-warning text-center p-4">
        <i class="fa fa-exclamation-triangle"></i>
        <strong>{{ $validation_error }}</strong>
    </div>
@else
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="checkbox-cell">
                <input type="checkbox" id="selectAllCheckbox">
            </th>
            <th>No</th>
            <th>Id</th>
            <th>Patient Code</th>
            <th nowrap>Patient Name</th>
            <th nowrap>Agency</th>
            <th>Mobile</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Type</th>
            <th nowrap>Created Date</th>
            <th nowrap>Created By</th>
        </tr>
    </thead>
    <tbody>
        @if($patients->count() > 0)
            @foreach($patients as $index => $patient)
                <tr>
                    <td class="checkbox-cell">
                        <input type="checkbox" class="patient-checkbox" value="{{ $patient->id }}" data-agency-id="{{ $patient->agency_id }}">
                    </td>
                    <td>{{ ($patients->currentPage() - 1) * $patients->perPage() + $index + 1 }}</td>
                    <td><a href="{{url('patient/view/')}}/{{$patient->id}}" target="_blank">{{ $patient->id }}</a></td>
                    <td>{{ $patient->patient_code }}</td>
                    <td>{{ ucwords($patient->first_name . ' ' . ($patient->middle_name ? $patient->middle_name . ' ' : '') . $patient->last_name) }}</td>
                    <td>{{ $patient->agency_name }}</td>
                    <td>{{ $patient->mobile }}</td>
                    <td>{{ $patient->phone }}</td>
                    <td>
                        @if($patient->status == 'Completed')
                            <span class="badge badge-success">{{ $patient->status }}</span>
                        @elseif($patient->status == 'Pending')
                            <span class="badge badge-warning">{{ $patient->status }}</span>
                        @elseif($patient->status == 'Booked')
                            <span class="badge badge-primary">{{ $patient->status }}</span>
                        @elseif($patient->status == 'Cancel')
                            <span class="badge badge-danger">{{ $patient->status }}</span>
                        @else
                            <span class="badge badge-info">{{ $patient->status }}</span>
                        @endif
                    </td>
                    <td>{{ $patient->type }}</td>
                    <td>{{ $patient->created_date ? date('m/d/Y', strtotime($patient->created_date)) : '' }}</td>
                    <td>{{ $patient->creator_first_name . ' ' . $patient->creator_last_name }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12" class="text-center">No records found</td>
            </tr>
        @endif
    </tbody>
</table>

@if($patients->count() > 0)
    <div class="mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of {{ $patients->total() }} records
            </div>
            <div>
                {{ $patients->links() }}
            </div>
        </div>
    </div>
@endif

<script>
    // Reinitialize checkboxes after AJAX load
    PatientAgencyMerge.reinitializeCheckboxes();
</script>
@endif
