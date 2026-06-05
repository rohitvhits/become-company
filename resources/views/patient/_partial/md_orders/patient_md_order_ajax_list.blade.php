<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th nowrap>#</th>
           
            <th nowrap>Document Name</th>
            <th nowrap>Start Date</th>
            <th nowrap>End Date</th>
           
            <th nowrap>Created Date / Created By</th>
            @if(auth()->user()->agency_fk =="")
            <th nowrap>Action</th>
            @endif
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
                    @if(auth()->user()->agency_fk =="")
                    <td>
                       
                            <a class="mr-1" onclick="editMDOrder('{{ $val->id}}')"><i class="fa fa-edit"></i></a>
                            <a  onclick="deleteMDOrder('{{ $val->id}}')"><i class="fa fa-trash"></i></a>
                        
                    </td>
                    @endif
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr>
                <td colspan="6">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right mqOrder_paginate pegination-margin" id="mqOrder_paginate">
{{ $query->links() }}
</div>