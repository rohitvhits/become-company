<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th nowrap>Agency Name</th>
            <th nowrap>Total Caregiver Count</th>
            <th nowrap>Total Patient Count</th>
            <th nowrap>Total Count</th>
        </tr>
    </thead>
    
    <tbody>
        @if(count($query) > 0)
            @php $i = 1 + (($query->currentPage() - 1) * $query->perPage()); @endphp
            @foreach ($query as $row) 
                <tr>
                    <th><?= $i++ ?></th>
                    <td>{{ $row->agencyDetail->agency_name?? '' }}</td>
                    <td>{{$row->caregivers}}</td>
                    <td>{{$row->patients}}</td>
                    <td>{{ $row->caregivers + $row->patients }}</td>
                </tr>
            @endforeach
        @else
        <tr>
            <td colspan="5">
                <center><b>Data not found</b></center>
            </td>
        </tr>
        @endif
    </tbody>
</table>

<div class="pull-right pegination-margin">
    {{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>