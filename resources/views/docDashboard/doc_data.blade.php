<div class="table-responsive ">
<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="10%">Agency Name</th>
            <th width="8%">Portal ID</th>
            <th width="12%">Patient Name</th>
            <th width="12%">Portal Status</th>
            <th width="12%">Type</th>
    
            <th width="10%">Document Name</th>
            <th width="10%">Attachment</th>
            <th width="10%">Requested Id</th>
            <th width="10%">Document Completion Date</th>
            <th width="10%">Status</th>
            <th width="10%">Created Date /<br> Created By</th>
            <th width="5%">Action</th>
        </tr>
    </thead>
    <tbody>
        
        @if(count($document_list) >0)
            @php 
                $cnt =($page * 50) -49;
            @endphp
            @foreach($document_list as $val)
            
                @php 
                $color = $val->deleted_flag == 'Y' ?     'deleted' : '';
                @endphp
                <tr class="{{ $color }}">
                <div id="preason{{ $cnt}}" style="display:none">
                {{ $val->status_note}}
            </div>
                    <td nowrap>{{ $cnt}}</td>
                    <td>{{ $val->patientDetails->agencyDetail->agency_name}}</td>
                    <td>
                        <a href="{{ url('patient/view/')}}/{{ $val->patientDetails->id}}" target="_blank">{{ $val->patientDetails->id}}</a>
                    </td>
                    <td><a href="{{ url('patient/view/')}}/{{ $val->patientDetails->id}}" target="_blank">{{ $val->patientDetails->first_name.' '.$val->patientDetails->last_name}}</a></td>
                    <td>{{ ucwords($val->patientDetails->status)}}</td>
                    <td>{{ $val->patientDetails->type}}</td>
                    <td>{{ $val->document_name}}
                        @if($val->internal_use ==1)
                        <div class="badge badge-primary badge-pill">Internal Use </div>
                        @endif
                    </td>
            
                    <td>
                        @if ($val->attachment != '' && $val->deleted_flag == 'N')
                            <a target="_blank" href="{{ url('/dpp')}}/{{ $val->id}}"><i class="fa fa-download"></i> Download</a>
                            @else
                            @if( $val->deleted_flag != 'Y')
                            <a data-toggle="modal" data-target="#exampleModal-upload-doc" data-whatever="@mdo" onclick="getUploadDocument('{{ $val->id}}')"><i class="fa fa-upload"></i> Upload document </a>
                            @endif
                        @endif
                    </td>
                    <td>
                        {{ $val->request_service_id}}
                    </td>
                    <input type="hidden" id="ser{{ $val->id}}" value="">
                    <td>
                        @if(isset($val->document_completed_date) && $val->document_completed_date !="")
                            {{  Common::convertMDY($val->document_completed_date) }}
                        @endif
                        <span id="doc_completed_id{{ $val->id}}" style="display:none">
                        @if(isset($val->document_completed_date) && $val->document_completed_date !="")
                            {{  Common::convertMDY($val->document_completed_date) }}
                        @endif
                        </span>
                    </td>
                    @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                    <td >
                        @if($val->document_review_status =="Approved")
                            <span class="badge badge-outline-success" style="color:#d76718;">Approved</span>
                        @elseif($val->document_review_status =="Rejected")
                            <span class="badge badge-outline-danger" style="color:#d76718;">Rejected</span>
                        @else
                            <span class="badge badge-outline-primary" style="color:#d76718;">Pending</span>
                        @endif
                    </td>

                    @endif
                    <td>
                    {{ Common::convertMDY($val->created_date)}}<br>
                    @if(isset($val->userDetails->first_name) && isset($val->userDetails->last_name))
                        {{$val->userDetails->first_name.' '.$val->userDetails->last_name}}
                    @endif
                    </td>
                    
                    
                    
                    @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                        <td>
                            <a href="javascript:void(0)" data-toggle="modal"  onclick="review('{{ $val->id}}')" data-whatever="@mdo"><i class="fa fa-eye"></i></a>
                        </td>
                    @endif
                </tr>
                @php 

                $cnt++;
                @endphp
            @endforeach
        @endif
        
        @if(count($document_list) ==0)
                <tr>
                    <td colspan="15" style="text-align: center;">No record available</td>
                </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin"> 
    {{ $document_list->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
</div>

<script>
    var total = "{{ $document_list->total()}}";
    $('#total_record_id').html(total)
    $('#blank_div').attr('style','margin-top:25px')
    if(total ==0){
        $('#blank_div').attr('style','margin-top:10%')
    }

</script>