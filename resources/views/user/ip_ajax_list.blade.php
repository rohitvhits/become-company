<table id="" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>IP Address</th>
            <th>Type</th>
            <th>Created Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($query))
        @php $i=1; @endphp
        @foreach ($query as $val)
        <tr>
            <td>{{$i}}</td>
            <td>{{ $val->ip_address }}</td>
            <td>{{ucfirst($val->type)}}</td>
            <td>{{ date('m/d/Y h:i A',strtotime($val->created_at)) }}</td>
            <td> <a href="javascript:void(0)" data-id="{{ $val->id}}" class="edit-ip-address" title="Edit"><i class="fa fa-edit"></i></a>
                <a href="javascript:void(0)" data-id="{{ $val->id}}" class="delete-ip-address" title="Delete"><i class="fa fa-trash"></i>
            </td>
        </tr>
        @php $i++; @endphp
        @endforeach
        @else
        <tr>
            <td>No record available</td>
        </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>