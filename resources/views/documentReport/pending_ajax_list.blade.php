<div class="table-responsive ">
<table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th>#</th>
                <th nowrap>Agency Name</th>
                <th nowrap>Portal ID</th>
                <th nowrap>Type</th>
                <th nowrap>Patient Name</th>
                <th nowrap>Document Name</th>
                <th nowrap>Attachment</th>
                <th nowrap>Requested Id</th>
                <th nowrap>Attachment Service</th>
                <th >Document Completion Date</th>
                <th nowrap>Status</th>
                <th style="white-space: pre-line;">Review Date Date /<br> Review By</th>
                <th nowrap>Created Date /<br> Created By</th>
              
                <!-- <th>Last Updated By</th> -->
                @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                <th>Action</th>
                @endif

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
                            <a href="{{ url('patient/view/')}}/{{ $val->patientDetails->id}}" target="_blank">{{ $val->patientDetails->id}}</a></td>
                        <td>{{ $val->patientDetails->type}}</td>
                        <td><a href="{{ url('patient/view/')}}/{{ $val->patientDetails->id}}" target="_blank">{{ $val->patientDetails->first_name.' '.$val->patientDetails->last_name}}</a></td>
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
                        <td>
                            @php
                                $serviceArray = [];
                                $names = array('badge-primary','badge-success','badge-info','badge-warning','badge-dark');
                            @endphp
                            @if(!empty($val->services[0]))
                                @php
                                $tempCountercc = 0;
                                @endphp
                                
                                @foreach($val->services as $srv)
                                    @php 
                                        $serviceArray[] = $srv->id;
                                    @endphp
                                    <span
class="badge <?php echo $names[$tempCountercc % count($names)]; ?>">{{ $srv->name}}</span>
@php
                                $tempCountercc++;
                                @endphp                            
    @endforeach
                            @endif
                            <input type="hidden" id="ser{{ $val->id}}" value="<?php echo json_encode($serviceArray);?>">
                            @if(empty($val->services[0]) && $val->patientDetails->agencyDetail->agency_name=='Hamaspik Annual Caregivers')
                            <!-- <a data-toggle="modal" onclick="addAllServices('{{ $val->id}}','{{$val->patientDetails->id}}')"><i class="fa fa-upload"></i> Add </a> -->
                            @endif

                        </td>
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
                        <td >
                    @if($val->document_review_status =="Approved")
                        <span class="badge badge-outline-success" style="color:#d76718;">Approved</span>
                        @elseif($val->document_review_status =="Rejected")
                        <span class="badge badge-outline-danger" style="color:#d76718;">Rejected</span>
                        @else
                        <span class="badge badge-outline-primary" style="color:#d76718;">Pending</span>
                        @endif

                        @if(isset($val->assignUserReviewDocument->id))
                        <div class="d-flex align-items-center profile-feed-item" style="gap: 4px;padding:8px 0px;">
                            <img src="{{ asset('assets/images/faces/face5.jpg')}}" alt="profile" class="img-sm rounded-circle">
                            <div class="">
                                <h6 class="text-center mb-0" style="line-height: 1.2;">
                               
                                {{ $val->assignUserReviewDocument->first_name.' '.$val->assignUserReviewDocument->last_name}}
                                </h6>
                                
                            </div>
                        </div>
                       
                        @else 

                        @endif
                     
                    </td>
                   <td>
                        @if(isset($val->reviewUserDetails->id))
                        {{  Common::convertMDYTime($val->document_review_date) }}<br>
                        {{ $val->reviewUserDetails->first_name.' '.$val->reviewUserDetails->last_name}}
                        @else
                        @if($val->assign_document_review !="" )
                            <!-- <a class="btn btn-primary">Review </a> -->
                            @endif
                        @endif
                        
                        </td>

                        <td>
                        {{ Common::convertMDY($val->created_date)}}<br>
                        {{$val->userDetails->first_name.' '.$val->userDetails->last_name}}
                        </td>
                        
                       
                       
                        @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                            <td>
                        
                           
                                <input type="hidden" name="existing_services" id="ser{{ $val->id}}" value="<?php echo json_encode($serviceArray);?>">
                                @if($val->request_service_id !="")
                <a data-toggle="modal" data-target="#edit-exampleModal-services" data-service="" data-whatever="@mdo" onclick="editPatientRequestServiceDocument('{{ $val->request_service_id}}','{{$val->id}}','{{$val->patientDetails->id}}','{{ $val->patientDetails->type}}','{{ $val->patientDetails->agency_id}}','document_report')" title="Edit Service" ><i class="fa fa-edit"></i></a> 
                                            @else
                <a data-toggle="modal" data-target="#edit-exampleModal-services" data-service="<?php echo json_encode($serviceArray);?>" data-whatever="@mdo" onclick="viewServicesNew('{{$val->patientDetails->id}}','{{ $val->patientDetails->type}}','{{ $val->patientDetails->agency_id}}');editPatientRequestServiceDocument('{{ $val->request_service_id}}','{{$val->id}}','{{$val->patientDetails->id}}','{{ $val->patientDetails->type}}','{{ $val->patientDetails->agency_id}}')" title="Edit Service" ><i class="fa fa-edit"></i></a> 
                                            @endif
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
   
    $('#blank_div').attr('style','margin-top:25px')
    if(total ==0){
        $('#blank_div').attr('style','margin-top:10%')
    }

</script>