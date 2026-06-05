<table class="table table-bordered">
    <thead>
        <th>#</th>
        <th>Agency Name</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Status</th>
    </thead>
    <tbody>
        @php 
            $cnt =1
        @endphp

        @if(!empty($list[0]))
            @foreach($list as $val)
                <tr>
                    <td>{{ $cnt}}</td>
                    <td>{{ $val->agencyDetails->agency_name}}</td>
                    <td>{{ $val->start_date}}</td>
                    <td>{{ $val->end_date}}</td>
                    <td>{{ $val->status}}</td>
                </tr>
            @endforeach
        @endif

        @if(empty($list[0]))
            <tr>
                <td colspan="5">
                    No record available
                </td>
            </tr>

        @endif


    </tbody>
</table>