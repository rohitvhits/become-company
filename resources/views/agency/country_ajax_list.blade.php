<table id="" class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Country Name</th>
            <th>Created Date</th>
        </tr>
    </thead>
    <tbody>
        @if(count($query))
        @php $i=1; @endphp
        @foreach ($query as $val)
        <tr>
            <td>{{$i}}</td>
            <td>{{ $val->country_name }}</td>
            <td>{{ date('m/d/Y h:i A',strtotime($val->created_at)) }}</td>
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