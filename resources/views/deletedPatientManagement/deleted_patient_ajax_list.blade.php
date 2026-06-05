<table class="table table-bordered table-width1">
    <thead>
        <th>#</th>
        <th>Patient ID</th>
        <th>Agency Name</th>
        <th>Patient Name</th>
        <th>Created Date/Created By</th>
        <th>Deleted Date/Deleted By</th>
        <th>Action</th>
    </thead>
    <tbody>
        @php
        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
        @endphp
        @forelse($query as $patient)
            <tr>
                <td>{{$i}}</td>
                <td>
                    <a href="{{ url('patient/view')}}/{{ $patient->patient_id}}" target="_blank">
                        {{ $patient->patient_id}}
                    </a>
                </td>
                <td>{{ $patient->agency_name}}</td>
                <td>{{ $patient->patient_name}}</td>
                <td>
                    {{ date('m/d/Y h:i A', strtotime($patient->created_date))}}</br>
                    {{ $patient->creator_first_name}} {{$patient->creator_last_name}}
                </td>
                <td>
                    {{ date('m/d/Y h:i A', strtotime($patient->deleted_date))}}</br>
                    {{ $patient->deleted_first_name}} {{$patient->deleted_last_name}}
                </td>
                <td>
                    <button type="button" class="btn btn-success btn-sm" onclick="reactivatePatient('{{ $patient->patient_id }}')">
                        <i class="mdi mdi-restore"></i> Reactivate
                    </button>
                </td>
            </tr>
            @php
            $i++;
            @endphp
        @empty
            <tr>
                <td colspan="7" class="text-center">No record available</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->links() }}
</div>
