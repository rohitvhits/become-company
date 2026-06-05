<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th nowrap>#</th>
          
            <th nowrap>Agency Name</th>
         
            <th nowrap>Portal ID</th>
            <th nowrap>Name</th>
            <th nowrap>Portal Status</th>
            <th nowrap>Document Name</th>
            <th nowrap>Start Date</th>
            <th nowrap>End Date</th>
            <th nowrap>Created Date / Created By</th>
        </tr>
    </thead>

    <tbody>
        @if(count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp
        
            @foreach($query as $val)
                <tr>
                    <td>{{ $cnt++}}</td>
                   
                        <td>
                            @if(isset($val->patientDetails->agencyDetail->id))
                            {{ $val->patientDetails->agencyDetail->agency_name}}
                            @endif
                        </td>
                  
                    <td>
                        @if(isset($val->patientDetails->id))
                           <a href="{{ url('patient/view')}}/{{ $val->patientDetails->id}}" target="_blank"> {{ $val->patientDetails->id}}</a>
                        @endif
                    </td>
                    <td>
                        @if(isset($val->patientDetails->id))
                        <a href="{{ url('patient/view')}}/{{ $val->patientDetails->id}}" target="_blank">{{ $val->patientDetails->first_name.' '.$val->patientDetails->last_name}}</a>
                        @endif
                    </td>
                    <td>
                        @if(isset($val->patientDetails->id))
                        {{ ucfirst($val->patientDetails->status)}}
                        @endif
                    </td>
                    <td>
                        @if(isset($val->documentDetails->id))
                            {{ $val->documentDetails->document_name}}
                        @endif
                    </td>
                    <td>
                        {{ Common::convertMDY($val->start_date)}}
                    </td>
                    <td>
                        {{ Common::convertMDY($val->end_date)}}
                    </td>
                    <td>
                        {{ Common::convertMDYTime($val->created_date)}} <br>
                        @if(isset($val->users->id))
                        {{ $val->users->first_name.' '.$val->users->last_name}}
                        @endif
                    </td>
                    
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr>
                <td colspan="7">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right mqOrder_paginate pegination-margin" id="mqOrder_paginate">
{{ $query->links() }}
</div>

<script>
    $('#total_record_id').html('{{ $query->total()}}')
</script>