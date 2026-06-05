<div class="table-responsive ">
<table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th>#</th>
                <th nowrap>Agency Name</th>
                <th>Portal ID</th>
                <th nowrap>Type</th>
                <th nowrap>Patient Name</th>
                <th nowrap>Document Name</th>
                <th nowrap>Attachment</th>
                <th>Requested Id</th>
                <th >Document Completion Date</th>
                @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                    <th nowrap>Status</th>
                    <th style="white-space: pre-line;">Review Date /<br> Review By</th>
                @endif
                <th nowrap>Created Date /<br> Created By</th>
                <th nowrap style="cursor:pointer; user-select:none;" onclick="sortByUpdatedDate()">Modified Date /<br> Modified By&nbsp;<i class="fa fa-sort" id="sort-updated-date-icon"></i></th>
                <th nowrap>Send Back to Agency</th>

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
                        @endif
                        <td>
                        {{ Common::convertMDY($val->created_date)}}<br>
                        @if(isset($val->userDetails->first_name) && isset($val->userDetails->last_name))
                            {{$val->userDetails->first_name.' '.$val->userDetails->last_name}}
                        @endif
                        </td>
                        <td>
                        {{ Common::convertMDY($val->updated_date)}}<br>
                        @if(isset($val->updatedUserDetails->first_name) && isset($val->updatedUserDetails->last_name))
                            {{$val->updatedUserDetails->first_name.' '.$val->updatedUserDetails->last_name}}
                        @endif
                        </td>
                        <td class="text-center">
                            @php $mailLog = $sendMailLogs[$val->id] ?? null; @endphp
                            @if($mailLog)
                                @php
                                    $rawEmails = json_decode($mailLog->email, true);
                                    $emails = [];
                                    if(!empty($rawEmails) && is_array($rawEmails)) {
                                    foreach ($rawEmails as $e) {
                                        foreach (array_map('trim', explode(',', $e)) as $single) {
                                            if ($single !== '') $emails[] = $single;
                                        }
                                    }
                                    }
                                    $emails = array_unique($emails);
                                    $popoverContent = '<strong>Email:</strong> ' . implode(', ', $emails)
                                        . '<br><strong>Date:</strong> ' . Common::convertMDYTime($mailLog->created_date)
                                        . '<br><strong>Send By:</strong> ' . $mailLog->first_name . ' ' . $mailLog->last_name;
                                    if (!empty($mailLog->note)) {
                                        $popoverContent .= '<br><strong>Note:</strong> ' . e($mailLog->note);
                                    }
                                @endphp
                                <span class="badge badge-success"
                                    data-toggle="popover"
                                    data-trigger="hover"
                                    data-placement="top"
                                    data-html="true"
                                    data-title="Send Back to Agency"
                                    data-content="{{ $popoverContent }}"
                                    style="cursor:pointer;">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </td>

                        @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                            <td>


                                <input type="hidden" name="existing_services" id="ser{{ $val->id}}" value="">
                                @if($val->request_service_id !="")
                <a data-toggle="modal" data-target="#edit-exampleModal-services" data-service="" data-whatever="@mdo" onclick="editPatientRequestServiceDocument('{{ $val->request_service_id}}','{{$val->id}}','{{$val->patientDetails->id}}','{{ $val->patientDetails->type}}','{{ $val->patientDetails->agency_id}}','document_report')" title="Edit Service" ><i class="fa fa-edit"></i></a>
                                            @else
                <a data-toggle="modal" data-target="#edit-exampleModal-services" data-service="" data-whatever="@mdo" onclick="viewServicesNew('{{$val->patientDetails->id}}','{{ $val->patientDetails->type}}','{{ $val->patientDetails->agency_id}}');editPatientRequestServiceDocument('{{ $val->request_service_id}}','{{$val->id}}','{{$val->patientDetails->id}}','{{ $val->patientDetails->type}}','{{ $val->patientDetails->agency_id}}')" title="Edit Service" ><i class="fa fa-edit"></i></a>
                                            @endif
                                            <a href="javascript:void(0)" data-toggle="modal"  onclick="review('{{ $val->id}}')" data-whatever="@mdo"><i class="fa fa-eye"></i></a>
                                            <!-- <a href="javascript:void(0)" onclick="drOpenMarkSendBackModal('{{$val->id}}','{{$val->patientDetails->id}}')" title="Mark as Send Back to Agency" style="margin-left:4px;"><i class="fa fa-reply"></i></a> -->
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

    $('[data-toggle="popover"]').popover();
    $('body').on('click', function(e) {
        $('[data-toggle="popover"]').each(function() {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
</script>

<!-- ===== Mark as Send Back to Agency Modal (Document Report) ===== -->
<div class="modal fade" id="drMarkSendBackAgencyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-reply mr-1"></i> Mark as Send Back to Agency
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="dr_msba_document_id">
                <input type="hidden" id="dr_msba_patient_id">
                <p class="text-muted mb-3" style="font-size:13px;">
                    This will mark the document as <strong>Send Back to Agency: Yes</strong>. Optionally add a note below.
                </p>
                <div class="form-group">
                    <label class="col-form-label">Note <span class="text-muted" style="font-size:11px;">(optional)</span></label>
                    <textarea id="dr_msba_note" class="form-control" rows="3" placeholder="Enter note..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="dr_msba_submit_btn" onclick="drSubmitMarkSendBackToAgency()">
                    <i class="fa fa-check mr-1"></i> Confirm
                </button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function drOpenMarkSendBackModal(documentId, patientId) {
    $('#dr_msba_document_id').val(documentId);
    $('#dr_msba_patient_id').val(patientId);
    $('#dr_msba_note').val('');
    $('#drMarkSendBackAgencyModal').modal('show');
}

function drSubmitMarkSendBackToAgency() {
    var documentId = $('#dr_msba_document_id').val();
    var patientId  = $('#dr_msba_patient_id').val();
    var note       = $('#dr_msba_note').val().trim();

    $('#dr_msba_submit_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

    $.ajax({
        url: '/patient/mark-send-back-to-agency',
        type: 'POST',
        data: {
            document_id: documentId,
            patient_id:  patientId,
            note:        note,
            '_token':    _CSRF_TOKEN
        },
        success: function(res) {
            $('#drMarkSendBackAgencyModal').modal('hide');
            toastr.success(res.message || 'Marked as Send Back to Agency successfully');
            loadAjaxList();
        },
        error: function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong. Please try again.';
            toastr.error(msg);
        },
        complete: function() {
            $('#dr_msba_submit_btn').prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirm');
        }
    });
}
</script>