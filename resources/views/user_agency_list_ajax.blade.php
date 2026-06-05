<table id="" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Agency Name</th>
            <th>Created At</th>
            <th>Created By</th>
            <th>Updated At</th>
            <th>Updated By</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($query))
        
        @php
        $i = 1 + ($query->currentPage() - 1) * $query->perPage();
        @endphp
        @foreach ($query as $val)
        <tr>
            <td>{{$i++}}</td>
            <td>{{ isset($val->agencyDetails->agency_name) ? $val->agencyDetails->agency_name : '' }}</td>
            
            <td>{{ date('m/d/Y h:i A',strtotime($val->created_at)) }}</td>
            <td>{{ $val->userDetails->first_name ??"" }} {{ $val->userDetails->last_name ??"" }}</td>
            <td>{{ date('m/d/Y h:i A',strtotime($val->updated_at)) }}</td>
            <td>{{ $val->updatedUserDetails->first_name ??"" }} {{ $val->updatedUserDetails->last_name ??"" }}</td>
            <td> <a href="javascript:void(0)"  class="edit-user-agency" onclick="editUserAgency('{{$val->id}}')" title="Edit"><i class="fa fa-edit"></i></a>
                <a href="javascript:void(0)"  class="delete-user-agency" onclick="deleteUserAgency('{{$val->id}}')" title="Delete"><i class="fa fa-trash"></i>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td>No record available</td>
        </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin user-agency-pegination">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
