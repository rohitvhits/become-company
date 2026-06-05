<div class="">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="20%">Agency Name</th>
                <th width="15%"> Type </th>
                <th width="25%"> Url </th>
                <th width="20%"> Created Date </th>
                <th width="10%"> Action </th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            <span id="{{$row->id}}" style="display:none">{{$row->data}}</span>
            <tr>
             
                <td>{{ $i++}}</td>
                <td>{{ $row->generateTokenDetails->agencyDetailsByToken['agency_name']??''}}</td>
                <td>{{ $row->type}}</td>
                <td>{{ $row->url}}</td>
                <td>{{ date('m/d/Y h:i A',strtotime($row->created_date))}}</td>
                <td>
                    @if(!empty($row->data) && count(json_decode($row->data,1)) > 0)
                    <a onclick="showSwal('View','{{$row->id}}')"><i class="fa fa-eye"></i></a>
                       
                    @endif
                </td>
            </tr>
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="8">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>