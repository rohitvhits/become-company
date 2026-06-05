<style>

</style>
<div class="">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th>No</th>
                <th>Title</th>
                <th>Image</th>
                <th> Start Date </th>
                <th> End Date </th>
                <th> Status </th>
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
                <td>{{ $row->title}}</td>
                @if(empty($row->image))
                    @php $row->image = 'default.png'; @endphp
                @endif
                @if(env('FILE_UPLOAD_PERMISSION') != 'development')
                    @php 
                        $imageUrl = url('/event-image-show-aws') . '/' . $row->id . '?type=event'; 
                    @endphp
                @else
                    @php 
                        $imageUrl = url('event-image/') . '/' . $row->image; 
                    @endphp
                @endif
                <td><div id="logo-container"><img id="agency-logo" src="{{ $imageUrl }}" style="height: 50px;width: 50px;border-radius: 5px;" alt="Logo"></div> 
                </td>
                <td>{{ date('m/d/Y',strtotime($row->start_date))}}</td>
                <td>{{ date('m/d/Y',strtotime($row->end_date))}}</td>
                <td id="row_{{ $row->id}}">
                    @if($row->status == 1)
                        @php $status = 'Active' @endphp
                        @php $class = 'success' @endphp
                    @else
                        @php $status = 'Deactive' @endphp
                        @php $class = 'danger' @endphp

                    @endif
                    <span class="badge badge-{{$class}}">{{ $status }}</span>
                </td>
                <td>{{ date('m-d-Y',strtotime($row->created_at))}} <br> {{ $row->first_name}}  {{ $row->last_name}}</td>
                <td> 
                    @can('event-edit')
                        <a href="javascript:void(0)" onclick="getEditEvent('{{$row->id}}')"><i class="fa fa-edit"></i></a>
                    @endcan
                    @can('event-delete')
                        &nbsp;<a href="javascript:void(0)" onclick="deleteEvent('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                    @endcan
                    &nbsp;
                    <label class="toggle-switch toggle-switch-success">
                            <input type="checkbox" data-id="{{ $row->id}}" name="is_disabled" value="1" id="is_disabled_{{$row->id}}" onChange="changeStatus('{{$row->id}}','{{$row->start_date}}','{{$row->end_date}}')"  @if($row->status ==1) checked @endif>
                        <span class="toggle-slider round"></span>
                    </label>
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