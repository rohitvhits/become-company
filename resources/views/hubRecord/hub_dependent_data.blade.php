<table id="order-listing1" class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>SSN</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($query) && count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp
        
            @foreach($query as $val)
                <tr>
                    <td>
                        {{ $val->first_name }} {{ $val->last_name }}
                    </td>
                    <td>
                        {{$val->ssn??'-'}}
                    </td>
                    <td>
                        <a target="_blank" href="{{ url('/hub-record/view')}}/{{ $val->dependent_id }}" title="View"><i class="fa fa-eye"></i></a>
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr class="txt-center">
                <td colspan="9">No record available</td>
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