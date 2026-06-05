<style>

</style>
<div class="table-responsive">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th nowrap>No</th>
                <th nowrap>Name</th>
                <th nowrap> Agency </th>
                <th nowrap> Caregiver Notification </th>
                <th nowrap> Patient Notification </th>
                <th nowrap> Services </th>
                <th nowrap> User </th>
                
                <th nowrap> Created Date/Created By </th>
                @canany(['group-notification-edit','group-notification-delete'])
                    <th width="10%"> Action </th>
                @endcan
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            <tr>
                <td width="5%">{{ $i++}}</td>
                <td width="10%">{{ $row->name}}</td>
                <td width="15%" title="{{$row->agency}}">
                    {{$row->agency}}
                </td>
                <td width="5%" title="{{$row->caregiver_notification}}">
                    {{$row->caregiver_notification}}
                </td>
                <td width="5%" title="{{$row->patients_notification}}">
                    {{$row->patients_notification}}
                </td>
                <td width="15%" title="{{$row->servicesRow}}">
                    {{$row->servicesRow}}
                </td>
                <td width="15%" title="{{$row->userRow}}">
                    {{$row->userRow}}
                </td>
                
                <td width="10%">{{ date('m/d/Y',strtotime($row->created_at))}} <br> {{ $row->userData->first_name}}  {{ $row->userData->last_name}}</td>
                @canany(['group-notification-edit','group-notification-delete'])
                <td width="10%"> 
                    @can('group-notification-edit')
                        <a href="{{url('group-notification')}}/{{$row->id}}/edit"><i class="fa fa-edit"></i></a>
                    @endcan
                    @can('group-notification-delete')
                    <a href="javascript:void(0)" onclick="deleteGroupNotification('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                    @endcan
                </td>
                @endcan
            </tr>
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="11">
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