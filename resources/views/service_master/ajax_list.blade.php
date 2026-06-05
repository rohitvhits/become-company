<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
        <th>No</th>
            <th>Type</th>
            <th>Service Name</th>
            <th>Created Date</th>
            @can('service-on-off')
            <th> Service Status</th>
            @endcan
            @can('service-master-assign-nybest')
            <th>Show Nybest User</th>
            @endcan
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($query) >0)
        @php
        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
        @endphp
        
            @foreach($query as $val)
                <tr>
                <td>{{ $i}}</td>
                    <td>{{ ucfirst($val->types)}}</td>
                   
                    <td class="mailbox-subject">{{ ucfirst($val->name)}}</td>
                    <td class="mailbox-date">{{ date('m/d/Y h:i A', strtotime($val->created_at)) }}<br>
                    {{ $val->first_name .' '.$val->last_name}}
                    </td
                    @can('service-on-off')>
                    <td>
                        <label class="toggle-switch toggle-switch-success"  title="{{ $val->is_disable == 1 ? 'Disable Service' : 'Enable Service' }}">
                            <input type="checkbox" data-id="{{ $val->id}}" name="is_disabled" value="1" onChange="changeStatus('{{$val->id}}')" id="is_disable_{{$val->id}}" @if($val->is_disable ==1) checked @endif>
                            <span class="toggle-slider round"></span>
                        </label>
                    </td>
                    @endcan
                    @can('service-master-assign-nybest')
                        <td>
                        
                            <label class="toggle-switch toggle-switch-success"  title="{{ $val->enabled_nybest_user == 1 ? 'Disabled NyBest User' : 'Enabled NyBest User' }}">
                                <input type="checkbox" data-id="{{ $val->id}}" name="enabled_nubest_user" value="1" onChange="enableNyBestUser('{{$val->id}}')" id="enabled_nubest_user_{{$val->id}}" @if($val->enabled_nybest_user ==1) checked @endif>
                                <span class="toggle-slider round"></span>
                            </label>
                    
                        </td>
                    @endcan
                   <td>
                        @can('service-master-edit')
                        <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" onclick="getDetails('{{ $val->id}}')"><i class="fa fa-edit"></i></a>
                        @endcan
                        @can('service-master-delete')
                        <a  href="javascript:void(0)"  data-toggle="tooltip" title="Delete" onclick="serviceDelete('{{ $val->id}}')"><i class="fa fa-trash"></i></a>
                        @endcan
                       
                   </td>
                   
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
        @endif
        @if(count($query) ==0)
            <tr>
                <td colspan="8" class="text-center">No record available</td>
            </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

<script>
var total = "{{ count($query)}}"
    $('#blank_div').attr('style','margin-top:30px')
    if(total ==0){
        $('#blank_div').attr('style','margin-top:10%')
    }

</script>