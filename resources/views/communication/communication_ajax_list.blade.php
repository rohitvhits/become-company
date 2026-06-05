<style>

</style>
<div class="">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th nowrap>No</th>
                <th nowrap>Title</th>
                <th nowrap>Image</th>
                <th nowrap> Start Date </th>
                <th nowrap> End Date </th>
                <th nowrap> Status </th>
                <th nowrap> Message </th>
                <th nowrap> Created Date/ Created By </th>
                <th nowrap> Action </th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            <span id="{{$row->id}}" style="display:none">
                {!! $row->content !!}
            </span>
            <tr>

                <td nowrap>{{ $i++}}</td>
                <td nowrap>{{ $row->title}}</td>
                @if(empty($row->image))
                    @php $row->image = 'default.png'; @endphp
                @endif
                @if(env('FILE_UPLOAD_PERMISSION') != 'development')
                    @php 
                        $imageUrl = url('/announcements-image-show-aws') . '/' . $row->id . '?type=event'; 
                    @endphp
                @else
                    @php 
                        $imageUrl = url('announcements-image/') . '/' . $row->image; 
                    @endphp
                @endif
                <td nowrap><div id="logo-container"><img id="agency-logo" src="{{ $imageUrl }}" style="height: 50px;width: 50px;border-radius: 5px;" alt="Logo"></div> 
                </td>
                <td nowrap>{{ date('m/d/Y',strtotime($row->start_date))}}</td>
                <td nowrap>{{ date('m/d/Y',strtotime($row->end_date))}}</td>
                
                <td  nowrap id="row_{{ $row->id}}">
                    @if($row->status == 1)
                        @php $status = 'Active' @endphp
                        @php $class = 'success' @endphp
                    @else
                        @php $status = 'Deactive' @endphp
                        @php $class = 'danger' @endphp

                    @endif
                    <span class="badge badge-{{$class}}">{{ $status }}</span>
                </td>
                <td>
                <a href="javascript:void(0)" onclick="commonMessage('{{ $row->id}}')">{!! $row->contents !!}</a>
                
                </td>
                <td nowrap>{{ date('m-d-Y',strtotime($row->created_at))}} <br> {{ $row->first_name}}  {{ $row->last_name}}</td>
                <td nowrap> 
                    @can('announcements-edit')
                        <a href="javascript:void(0)" onclick="getEditEvent('{{$row->id}}')"><i class="fa fa-edit"></i></a>
                    @endcan
                    @can('announcements-delete')
                        &nbsp;<a href="javascript:void(0)" onclick="deleteEvent('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                    @endcan
                    @can('announcements-send-mail')
                        &nbsp;<a href="javascript:void(0)" onclick="sendMail('{{$row->id}}')"><i class="fa fa-envelope"></i></a>
                    @endcan
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