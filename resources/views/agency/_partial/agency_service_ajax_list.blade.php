<style>

</style>
<div class="">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="25%"> Services </th>
                <th width="25%"> Type </th>
                <th width="25%"> Created Date/ Created By </th>
                <th width="15%"> Action </th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            <tr>
                <td>{{ $i++}}</td>
                <td>{{$row->serviceDetails->name}}</td>
                <td>{{$row->type}}</td>
                <td>{{ date('m-d-Y',strtotime($row->created_date))}} <br> {{ $row->users->first_name}}  {{ $row->users->last_name}}</td>
                <td> 
                    @if(Auth()->user()->id == 482)
                        @can('rate-card-edit')
                            <a href="javascript:void(0)" onclick="editAgencyTeleService('{{$row->id}}')"><i class="fa fa-edit"></i></a>
                        @endcan
                        @can('rate-card-delete')
                            <a href="javascript:void(0)" onclick="deleteAgencyTeleService('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                        @endcan
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