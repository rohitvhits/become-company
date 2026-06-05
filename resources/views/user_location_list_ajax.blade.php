<table id="" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Location Name</th>
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
                <td>{{ isset($val->locationDetails->location_name) ? $val->locationDetails->location_name : '' }}</td>
                <td>{{ date('m/d/Y h:i A',strtotime($val->created_at)) }}</td>
                <td>{{ $val->userDetails->first_name ??"" }} {{ $val->userDetails->last_name ??"" }}</td>
                <td>{{ date('m/d/Y h:i A',strtotime($val->updated_at)) }}</td>
                <td>{{ $val->updatedUserDetails->first_name ??"" }} {{ $val->updatedUserDetails->last_name ??"" }}</td>
                <td>
                    <a href="javascript:void(0)" class="edit-user-location" onclick="editUserLocation('{{$val->id}}')" title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0)" class="delete-user-location" onclick="deleteUserLocation('{{$val->id}}','{{$val->locationDetails->id ?? ''}}','{{addslashes($val->locationDetails->location_name ?? '')}}')" title="Delete"><i class="fa fa-trash"></i></a>
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
<div class="pull-right pegination-margin user-location-pegination">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
