<div class="table-responsive">
    <table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Appointment Count</th>
            <th>Full Name</th>
        </tr>
    </thead>
    <tbody>
        @if (count($appoinmentData) > 0)
            @foreach($appoinmentData as $row)
            <tr>
                <th scope="row">{{$row['users']['id']}}</th>
                <td>{{ number_format($row['count'])}}</td>
                <td>{{$row['users']['full_name']}}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
        @endif           
    </tbody>
</table>
</div>