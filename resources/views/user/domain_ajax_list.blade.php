<table id="" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Domain</th>
            <th>Created Date</th>
            <th>Action</th>

        </tr>
    </thead>
    <tbody>
        @php
            $i = $page * 50 - 49;
        @endphp
        @if (!empty($query[0]))
            @foreach ($query as $val)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td id="domain{{ $val->id}}">{{ $val->domain }}</td>
                    <td>{{ date('m/d/Y h:i A',strtotime($val->created_at)) }}</td>
                    <td>
                        <a href="javascript:void(0)" data-id="{{ $val->id}}" class="edit-detail"><i class="fa fa-edit"></i></a>
                        <a href="javascript:void(0)" data-id="{{ $val->id}}" class="delete-detail"><i class="fa fa-trash"></i></a>
                    </td>
                
                </tr>
            @endforeach

        @endif
        @if (empty($query[0]))
                <tr>
                    <td>No record available</td>
                </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>