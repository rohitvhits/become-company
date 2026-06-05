<div class="table-responsive tableData appointment-list-table">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>

                <th>ID</th>
                <th>Agency Name</th>
                <th>Total SYNC Remaining Caregiver</th>

            </tr>
        </thead>
        <tbody>
            @if(count($response) >0)
                @php
                    $i = ($page * 50)-49;
                @endphp
                @foreach($response as $val)
                    <tr>
                        <td>
                            {{$i++}}
                        </td>
                        <td>
                            {{ $val->agencyDetails->agency_name}}
                        </td>
                        <td>
                            {{ $val->total}}
                        </td>
                    </tr>
                @endforeach
            @endif

            @if(count($response) ==0)
                <tr>
                    <td colspan="3">No record available</td>
                </tr>
            @endif
            
        </tbody>
    </table>

    <div class="pull-right pegination-margin">
    {{ $response->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>