<style>

</style>
<div class="">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th>No</th>
                <th style="width:25%">Disable Date</th>
                <th>Disable Time</th>
                <th> Created Date/ Created By </th>
                <th> Action </th>
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
                <td>
                    {{ $row->disable_dates }}
                </td>
                <td>
                    @if($row->time !="")
                    {{ date('h:i A',strtotime($row->time)) }}
                    @endif
                  
                </td>
                <td>{{ date('m-d-Y',strtotime($row->created_at))}} <br> {{ $row->first_name}}  {{ $row->last_name}}</td>
                <td>
                    @can('disable-date-edit')
                        <a href="javascript:void(0)" onclick="getEditDisableDate('{{$row->id}}')"><i class="fa fa-edit"></i></a>
                    @endcan
                    @can('disable-date-delete')
                        <a href="javascript:void(0)" onclick="deleteDisableDate('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                    @endcan
                </td>
            </tr>
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="6">
                    <span style="text-align:center">Data not found</span>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>