<style>

</style>
<div class="">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th></th>
                <th>Title</th>
                <th>Image</th>
                {{-- <th> Is Default </th> --}}
                <th>Created Date/ Created By</th>
                <th>Action</th>
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
                <td>{{ $row->title}}</td>
                @if(empty($row->image))
                @php $row->image = 'default.png'; @endphp
                @endif
                <td><div id="logo-container"><img id="agency-logo" src="{{ url('stmp/') }}/{{ $row->id}}" style="height: 50px;width: 50px;border-radius: 5px;" alt="Logo"></div> 
                </td>
                {{-- <td>
                    <label class="toggle-switch toggle-switch-success">
                        <input type="checkbox" name="is_default" data-id="{{$row->id}}" class="stampEnableDisabled"
                            {{ $row->is_default == '1' ? 'checked' : '' }}>
                        <span class="toggle-slider round"></span>
                    </label>
                </td> --}}
                <td>{{ date('m-d-Y',strtotime($row->created_at))}} <br> {{ $row->first_name}}  {{ $row->last_name}}</td>
                <td> 
                    @can('stamp-edit')
                        <a href="javascript:void(0)" onclick="getEditApproveStamp('{{$row->id}}')"><i class="fa fa-edit"></i></a>
                    @endcan
                    @can('stamp-delete')
                        <a href="javascript:void(0)" onclick="deleteApproveStamp('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                    @endcan
                </td>
            </tr>
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="5">
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