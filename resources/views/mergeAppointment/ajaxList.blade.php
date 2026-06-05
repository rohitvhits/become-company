<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th>#</th>
            <th>Record Id</th>
            <th>Created Date</th>
            <th>Created By</th>
            <th>Action</th>
           
        </tr>
    </thead>
    <tbody>
        @php
            $i = 1;
            if($flag =='active'){
                $status = 1;
            }else{
                $status = 0;
            }
        @endphp
        @forelse($query as $val)
            <tr>
                <td>{{ $i++ }}</td>
                @if($flag =='active')
                <td><a href="{{ url('/deleted_appointment_show')}}/{{ $val->merge_patient_id}}" target="_blank">{{ $val->merge_patient_id }}</a></td>
                
                @else
                    @if($id == $val->main_patient_id)
                    <td><a href="{{ url('/deleted_appointment_show')}}/{{ $val->merge_patient_id}}" target="_blank">{{ $val->merge_patient_id }}</a><br><span class="badge badge-info">Merge</span></td>
                    @else
                    <td><a href="{{ url('/patient/view')}}/{{ $val->main_patient_id}}" target="_blank">{{ $val->main_patient_id }}</a><br><span class="badge badge-success">Active</span></td>
                    @endif
                
                @endif
                
                <td>{{ Common::convertMDYTime($val->created_date) }}</td>
                <td>{{ $val->first_name }} {{ $val->last_name }}</td>
                <td>
                    @can('unmerge-record')
                        <a class="unmerge_appointment_id1" onclick="newUnMergeAppointment('{{ $val->id}}','{{ $status }}')" title="Unmerge"><i class="fa fa-undo"></i></a>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    No records found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
