<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Company</th>
            <th>Created Date / Created By</th>
        </tr>
    </thead>

    <tbody>
        @if(isset($query) && count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
                $no=1;
            @endphp
        
            @foreach($query as $val)
                <tr>
                    <td>
                        {{ $no++}}
                    </td>
                    <td>
                        {{ $val->agency_name }}
                    </td>
        
                    <td>
                        {{ Common::convertMDYTime($val->created_date)}} <br>
                        @if(isset($val->users->first_name))
                        {{ $val->users->first_name.' '.$val->users->last_name}}
                        @endif
                    </td>
                 
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr class="txt-center">
            <td colspan="3">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right hub_list_paginate pegination-margin" id="hub_list_paginate">
{{ $query->links() }}
</div>

<script>
    var total = "{{ $query->total()}}";
    $('#total_record_id').html(total)
    $('#blank_div').attr('style','margin-top:25px')
    if(total ==0){
        $('#blank_div').attr('style','margin-top:10%')
    }
</script>