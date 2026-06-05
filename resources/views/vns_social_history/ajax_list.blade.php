<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
        <th>No</th>
            <th>Template Name</th>
            <th>Name</th>
            <th>Created Date / Created By</th>
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
                    <td class="mailbox-subject">{{ ucfirst($val->template_name ?? 'N/A')}}</td>
                    <td>{{ ucfirst($val->name)}}</td>
                    <td class="mailbox-date">{{ date('m/d/Y h:i A', strtotime($val->created_date)) }}<br>
                    {{ $val->first_name .' '.$val->last_name}}
                    </td>
                   <td>
                        @can('edit-vns-social-history')
                        <a href="javascript:void(0)" data-toggle="tooltip" title="Edit" onclick="getDetails('{{ $val->id}}')"><i class="fa fa-edit"></i></a>
                        @endcan
                        @can('delete-vns-social-history')
                        <a  href="javascript:void(0)"  data-toggle="tooltip" title="Delete" onclick="socialHistoryDelete('{{ $val->id}}')"><i class="fa fa-trash"></i></a>
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
                <td colspan="5" class="text-center">No record available</td>
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
