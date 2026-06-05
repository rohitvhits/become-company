<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th><input type="checkbox" class="checkbox-toggle">
            </th>
            <th>Template Name</th>
            <th>Agency Name</th>
            <th>Document Type</th>
            <th>Lookup Field</th>
            <th>Created Date / Created By</th>
            <th>Last Updated Date / Updated By</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        
        @if(count($templete_list) >0) 
            @foreach($templete_list as $val)
                <tr>
                    <td>
                        <input type="checkbox" class="mcheck">
                    </td>
                    <td class="mailbox-name">{{ ucfirst($val->template_name)}}</td>
                    <td class="mailbox-subject">
                        @if(!empty($val->agency_names))
                            @foreach(explode(',', $val->agency_names) as $name)
                                <span class="badge badge-primary">{{ $name }}</span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td class="mailbox-subject">{{ ucfirst($val->name)}}</td>
                    <td class="mailbox-subject">{{ ucfirst($val->lookup_fields)}}</td>
                    <td class="mailbox-date">{{ Common::convertMDYTime($val->created_date) }} <br>{{ $val->first_name.' '.$val->last_name}}</td>
                    <td class="mailbox-date">{{ Common::convertMDYTime($val->updated_date) }} <br>{{ $val->updated_first_name.' '.$val->updated_last_name}}</td>
                    @can('template-status')
                        <td>
                            <label class="toggle-switch toggle-switch-success">
                                <input type="checkbox" name="active_status" data-id="{{$val->id}}" data-previous='{{ $val->active_status}}' class="statusActiveDeactive"
                                    {{ $val->active_status == 'Active' ? 'checked' : '' }}>
                                <span class="toggle-slider round"></span>
                            </label>
                        </td>
                    @endcan
                    <td>
                        @if($val->custom_template ==1)
                            @can('template-singer')
                            <a href="{{ url('template-signer') }}?id={{ $val->id }}" title="signer"><i class="fa fa-sign-language"></i></a>&nbsp;&nbsp;

                            @endcan
                        @endif

                            @can('template-edit')
                                <a href="<?php echo URL::to('/'); ?>/template-edit?id={{ $val->id}}" title="Edit"><i
                                        class="fa fa-pencil"></i></a>&nbsp;&nbsp;
                            @endcan

                            @can('template-delete')
                                <a href="<?php echo URL::to('/'); ?>/template-delete?id={{ $val->id}}"
                                    onclick="return confirm('Are you sure remove this template and all document signer deleted?');"
                                    title="Delete"><i class="fa fa-trash-o"></i></a>
                            @endcan
                            &nbsp;&nbsp;
                            @if($val->custom_template ==1)
                            @can('template-view')
                            <a href="{{url('document-new') }}?id={{ $val->id }}" title="Esign New"><i
                                    class="fa fa-eye"></i></a>
                            @endcan
                            @endif
                            &nbsp;&nbsp;
                            <a onclick="viewAllAgency({{ $val->id}})">View All Agency</a>
                            @if($val->custom_template ==1)
                                <a href="javascript:void(0)" onclick="openSignerNotificationModal('{{ $val->id }}')" title="Sent Signer Notification"><i class="fa fa-bell"></i></a>
                            @endif
                    </td>
                </tr>
            @endforeach
        @endif
        @if(count($templete_list) ==0)
            <tr>
                <td colspan="10" class="text-center">No record available</td>
            </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $templete_list->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

<script>
var total = "{{ count($templete_list)}}"
    $('#blank_div').attr('style','margin-top:25px')
    if(total ==0){
        $('#blank_div').attr('style','margin-top:10%')
    }

</script>