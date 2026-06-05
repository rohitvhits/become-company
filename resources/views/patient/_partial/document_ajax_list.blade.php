<style>
    #dooc .table-responsive1 {
    display: block;
    width: calc(100vh - -700px);
    -webkit-overflow-scrolling: touch;
    overflow: visible !important;
}

        .table-responsive td {
    position: static !important; /* Prevent dropdown clipping */
}
#dooc .table-responsive {
    overflow: visible !important;
}

#dooc .table-responsive1 .dropdown-menu {
    position: absolute !important; /* Ensures proper rendering */
    will-change: transform; /* Fix dropdown positioning */
}
    </style>
<div id="dooc" class="table-responsive1">
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th nowrap>Document Name</th>
                <th>Requested Id</th>
                <th nowrap>Attachment</th>
                <th nowrap>Attachment Service</th>
                <th nowrap>Document <br> Completion Date</th>
                @if(auth()->user()->agency_fk =="")
                <th nowrap>Document Status</th>
                @endif
                <th nowrap>Created Date/ Created By</th>
                <th>Send Third Party</th>
                <th nowrap>Send Back to Agency</th>
                <!-- <th>Last Updated By</th> -->
                @if(auth()->user()->agency_fk =="")
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <?php
           
            if (count($document_list) > 0) {
                $cnt = ($document_list->currentPage() - 1) * $document_list->perPage() + 1;
                $final = [];
                foreach ($document_list as $va) {
                    $mergeStatuss="";
                    if(!empty($va->services[0])){
                        foreach($va->services as $srd){
                            $final[] = $srd->id;
                        }
                    }
                    $color = $va->deleted_flag == 'Y' ?     'deleted' : '';
                    $docFlagClasss = '';
                    if($va->deleted_flag == 'N'){
                       
                        if($va->flag =='1'){
                            $docFlagClasss = "pale-yellow-color";
                        }if($va->flag =='0'){
                            $docFlagClasss = '';
                        }
                    }
            ?>

                    <tr class="{{ $color }} {{$docFlagClasss}}">
                        <input type="hidden" name="src{{$va->id}}" value="<?php echo json_encode($final);?>"> 
                        <td><?php echo $cnt; ?>
                        @if($record->id !=  $va->patient_id)
                           <?php
    $mergeStatuss="merge";
                           ?>
                        <span style="margin-left:10px;top: 0;background: #00BBE0;padding: 1px 5px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;">Merge</span>
                        @endif
                    </td>
                        <td ><?php echo $va->document_name; ?>
                        @if($va->internal_use == 1)<br> <div class="badge badge-primary badge-pill">Internal Use </div> @endif
                        @if($va->medication_list == 1)<br>
                            <span style="background:#d4edda;color:#155724;border: 1px solid #3da354;border-radius: 10px;padding:2px 6px;font-size:11px;">Medication List</span>
                        @endif
                        @if($va->insurance_elg == 1)<br>
                            <span style="background:#cfe2ff;color:#084298;border: 1px solid #3d8bfd;border-radius: 10px;padding:2px 6px;font-size:11px;">Insurance Elg</span>
                        @endif
                        @if($va->mdo_tag == 1)
                        <br>
                        <span
                            style="background:#E9D5FF;color:#7C3AED;border: 1px solid #7C3AED;border-radius: 10px;padding:2px 6px;font-size:11px;
                            {{ !empty($va->mdo_source_name) ? 'cursor:pointer;' : '' }}"

                            @if(!empty($va->mdo_source_name))
                                data-toggle="popover"
                                data-trigger="click"
                                data-placement="top"
                                data-html="true"
                                data-content="<strong>MDO Source:</strong> {{ $va->mdo_source_name }}"
                            @endif
                        >
                            MDO
                        </span>
                    @endif
                        @if($va->templete_id !== null)
                            <br><div class="badge badge-primary badge-pill">Esign</div>
                        @endif
                        @if($va->info_only == 1)
                            <br><div class="badge badge-warning badge-pill">Info Only</div>
                        @endif
                    </td>
                        <td ><?php echo $va->request_service_id; ?></td>
                        <td nowrap>
                            @if ($va->attachment != '' && $va->deleted_flag == 'N')
                            @php
                                $userDownloadDocumentFlag =1;
                                if (auth()->user()->agency_fk != "") {
                                    
                                    $userDownloadDocumentFlag = 0;
                                    if(!in_array('DownloadDocument',$appointmentPermission)){
                                        $userDownloadDocumentFlag = 1;
                                    }
                                   
                                    
                                }
                                
                            @endphp
                                @if($userDownloadDocumentFlag ==1)
                                    <a target="_blank" href="<?php echo URL::to('/'); ?>/dpp/<?php echo $va->id; ?>?merge={{$mergeStatuss}}"><i class="fa fa-download"></i> Download</a>
                                <br>
                                @endif
                                <a href="{{ url('view-pdf-response')}}?id={{ $va->id}}" data-fancybox="" data-type="iframe" class="fancybox"><i class="fa fa-eye"></i>View</a>
                            @else
                            @if( $va->deleted_flag != 'Y')
                            <a data-toggle="modal" data-target="#exampleModal-upload-doc" data-whatever="@mdo" onclick="getUploadDocument('{{ $va->id }}')"><i class="fa fa-upload"></i> Upload document </a>
                            @endif
                            @endif
                        </td>
                        <td>
                            <?php 
                            $serviceArray = [];
                                $names = array('badge-primary','badge-success','badge-info','badge-warning','badge-dark');
                                if(!empty($va->services[0])){
                                    $tempCountercc = 0;
                                    foreach($va->services as $srv){
                                        $serviceArray[] = $srv->id;
                                        ?>
<span
class="badge <?php echo $names[$tempCountercc % count($names)]; ?>"><?php echo $srv->name; ?></span>
                                    <?php 
                                    $tempCountercc++;
                                    }
                                }
                            ?>

                        </td>
                        
                        <td>
                        @if($va->document_completed_date !="")
                        {{Common::convertMDY($va->document_completed_date)}}
                        @endif
                        
                        </td>
                        @if(auth()->user()->agency_fk =="")
                        <td>
                            @if($va->document_review_status =="Approved")
                                <span class="badge badge-outline-success" style="color:#d76718;">Approved</span>
                            @elseif($va->document_review_status =="Rejected")
                                <span class="badge badge-outline-danger" style="color:#d76718;">Rejected</span>
                            @else
                            <span class="badge badge-outline-primary" style="color:#d76718;">Pending</span>
                            @endif
                            @if(auth()->user()->agency_fk =="")
                                @if(isset($va->assignUserReviewDocument->id))
                                <p> {{ $va->assignUserReviewDocument->first_name.' '.$va->assignUserReviewDocument->last_name}} assign new Document</p>
                                @endif
                                @if(isset($va->reviewUserDetails->id))
                                    <p>Review Date: {{Common::convertMDYTime($va->document_review_date)}}<br></p>
                                    <p>Review By: {{ $va->reviewUserDetails->first_name.' '.$va->reviewUserDetails->last_name}}</p>
                                @endif
                            @endif
                            <br>
                            @can('regenerate-document')
                                <!-- <a class="btn mt-1 badge badge-primary" onclick="documentRegenerate('{{ $va->id}}')" title="Regenerate PDF">Regenerate PDF</a> -->
                            @endcan
                        </td>
                      @endif
                        
                        <td><?php echo Common::convertMDYTime($va->created_date); ?><br>{{ $va->first_name }} {{ $va->last_name }} </td>
                        <td>@if($va->send_third_party ==1) Yes @else
                                @if(isset($va->send_third_party_date) && $va->send_third_party_date !="" && $va->send_third_party_date !='0000-00-00')
                                    Yes
                                @elseif(isset($va->send_third_party_document_date) && $va->send_third_party_document_date !="" && $va->send_third_party_document_date !='0000-00-00')
                                    Yes
                                @else
                                    @if(isset($va->send_rnpad_document_date) && $va->send_rnpad_document_date !="" && $va->send_rnpad_document_date !='0000-00-00')
                                    Yes
                                    @else
                                        No
                                    @endif
                                @endif

                        @endif</td>
                        <td class="text-center">
                            @php
                                $mailLog = $sendMailLogs[$va->id] ?? null;
                                $isSendBack = $mailLog !== null;
                                $emails = [];
                            @endphp
                            @if($isSendBack)
                                @php
                                    $rawEmails = json_decode($mailLog->email, true);
                                    
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
                        <td style="overflow: unset !important">
                            @if($record->id ==  $va->patient_id)
                                <div class="btn-group pull-right status-dropdoown mr-2"  @if ($va->deleted_flag == 'Y') disabled @endif>
                                    <button type="button" class="btn btn-warning" title="Status">Action</button>
                                    <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" @if ($va->deleted_flag == 'Y') disabled @endif id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                        @if ($va->deleted_flag != 'Y')
                                            <a class="dropdown-item" href="javascriopt:void(0);" onclick="deleteRecordDocument('{{$record->id}}','{{$va->id}}')" title="Delete">Delete</a>
                                        @else
                                            <a class="dropdown-item" href="javascript:void(0)">Deleted</a>
                                        @endif
                                        
                                        <input type="hidden" name="existing_services" id="ser{{ $va->id}}" value="<?php echo json_encode($serviceArray);?>">

                                        @if($va->request_service_id !="")
                                            <a class="dropdown-item" data-toggle="modal" data-target="#edit-exampleModal-services" data-service="" data-whatever="@mdo" onclick="editPatientRequestService('{{ $va->request_service_id}}','{{$va->id}}','{{ $va->document_name }}','{{ $va->document_completed_date }}')" title="Edit Service" >Edit</a> 
                                        @else
                                            <a class="dropdown-item" data-toggle="modal" data-target="#edit-exampleModal-services" data-service="<?php echo json_encode($serviceArray);?>" data-whatever="@mdo" onclick="viewServices('{{ $va->id}}');editPatientRequestService('{{ $va->request_service_id}}','{{$va->id}}','{{ $va->document_name }}','{{ $va->document_completed_date }}')" title="Edit Service" >Edit</a> 
                                        @endif
                                
                                        <?php
                                        if (isset($record->inflowcare_id) && $record->inflowcare_id != '') {
                                        ?>
                                            <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal-5" data-whatever="@mdo" onclick="getEditDocument('{{ $va->id }}','{{ $va->document_name }}')">Edit</a>
                                        <?php } ?>
                                        @if($record->type == "Caregiver")
                                    
                                            @if ($va->attachment != '' && isset($agencyDetails->app_name) &&
                                                $agencyDetails->app_name != '' &&
                                                    $user['user_type_fk'] == 184 &&
                                                    ($va->uploaded_to_hha == 0 || true ) && ( $record->hha_id !='' || $record->link_hha_caregiver !="") )

                                                <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal-hha-update" data-whatever="@mdo" onclick="getMedicalResult({{ $record->agency_id }},{{ $va->id }})">Update HHX</a>
                                            @endif
                                            @if ($va->attachment != '' && isset($agencyDetails->app_name) &&
                                                $agencyDetails->app_name != '' && $user['user_type_fk'] == 184 &&
                                                    $va->uploaded_complience_hha == 0 &&( $record->hha_id !='' || $record->link_hha_caregiver) ) 

                                                <a class="dropdown-item" data-toggle="modal" data-target="#other-complience-hha-update" data-whatever="@mdo" onclick="getOtherMedicalResult({{ $record->agency_id }},{{ $va->id }})">Other Complience Update HHX</a>
                                            @endif
                                        @else
                                            @if ($va->attachment != '' && isset($agencyDetails->app_name) && $agencyDetails->app_name != '' && $user['user_type_fk'] == 184 && $va->uploaded_to_hha == 0 &&( $record->hha_id !='' || $record->link_hha_patient !="") )
                                            <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal-hha-update-patient" data-whatever="@mdo" onclick="uploadPatientDocToHHA('{{ $record->agency_id }}','{{ $va->id }}')">Upload patient document to HHA</a>
                                            @endif
                                        @endif
                                            @if ($va->attachment != '' && $record->alaycare_id !="")
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="uploadToAlayaCare('{{ $va->id }}')">Send To AlayaCare</a>
                                            @endif
                                              @can('document-send-mail')
                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" onclick="showMailDocument('{{$va->id}}')" data-whatever="@mdo" data-target="#exampleModal-send-mail">Send Mail</a>
                                        @endcan

                                        @if(auth()->user()->agency_fk =="")
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="openMarkSendBackModal('{{$va->id}}','{{$va->patient_id}}')">
                                                Mark as Send Back to Agency
                                            </a>
                                        @endif

                                        @can('link-to-visiting-aid')
                                        
                                        <!-- <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" onclick="requestsServices()" data-whatever="@mdo" data-target="#ModalLinkToVisitingAid">Link To Visiting Aid</a>
                                        -->
                                        @endcan

                                        @can('flag-doc-change-status')
                                            @if($va->deleted_flag == 'N')
                                                @if($va->flag == 0)
                                                    @php $flag = 'Flag'; @endphp
                                                @else
                                                    @php $flag = 'Flagged'; @endphp
                                                @endif
                                                <a onclick="flagDocumentChange('{{$va->id}}');" class="dropdown-item" title="Flag">{{$flag}}</a>
                                            @endif
                                        @endcan

                                        @if(auth()->user()->agency_fk =="")
                                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" onclick="viewDocumentDetails('{{$va->id}}')" data-whatever="@mdo">View Document</a>
                                        @endif
                                        
                                        @if($record->platform_type =="arla")
                                            @can('send-arla')
                                                <a onclick="sendDocumentArla('{{$va->request_service_id}}','{{ $va->id}}','{{ $va->document_completed_date}}');" class="dropdown-item" title="Send Arla Document">Send Arla Document</a>
                                            @endcan
                                        @endif

                                        @if($va->templete_id === null)
                                        <a class="dropdown-item" href="{{ url('/esign/write-document?id=' . $va->id) }}&type={{uniqid()}}" target="_blank" title="Edit Pdf">Add Sign/Stamp</a>
                                        @endif
                                        @can('e-fax-document')
                                        <a onclick="efaxModal('{{ $va->id}}');" class="dropdown-item" title="E-Fax">E-Fax</a>
                                        @endcan

                                        @if(auth()->user()->agency_fk =="")
                                        
                                            @if(count($thirdPartyApiList) >0)
                                                @foreach($thirdPartyApiList as $trp)
                                                    <a class="dropdown-item" title="{{ $trp->third_party_name}}" onclick="sendThirdPartyApiCall('{{ $trp->id}}','{{ $va->id}}','{{ $trp->portal_end_point}}')">{{ $trp->third_party_name}}</a>
                                                @endforeach
                                            @endif
                                        @endif

                                        @can('send-to-rnpad')
                                            @if($record->third_party_callback_url !="" && empty($va->send_rnpad_document_date))
                                            
                                            <a onclick="openRNPadModal('{{$va->id}}');" class="dropdown-item" title="Send To RN pad">Send To RN pad</a>
                                            @endif
                                            
                                        @endcan

                                        @can('send-to-task-health')
                                            @if($va->call_back_url !="" && empty($va->send_task_health_document_date))
                                                <a onclick="openTaskHealthModal('{{$va->id}}');" class="dropdown-item" title="Send To Task Health">Send To Task Health</a>
                                            @endif
                                        @endcan

                                        {{-- @can('send-to-thirdparty')
                                            @if(empty($va->send_third_party_date))
                                                <a onclick="openThirdPartModal('{{$va->id}}');" class="dropdown-item" title="Send To Third Party">Send To Third Party</a>
                                            @endif
                                        @endcan --}}

                                        @can('ai-analyse-doc')
                                        @if(auth()->user()->agency_fk == "")
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="openAiAnalysisForDoc('{{$va->id}}','{{$va->document_name}}')">View AI Analysis</a>
                                        @endif
                                        @endcan

                                        <a data-toggle="modal" class="dropdown-item" data-target="#exampleModal-create-mq-order" onclick="createMDOrders('{{$va->id}}')"> Add MD Order</a>
                                        @can('send-to-visiting-aid-thirdparty')
                                            @if ($va->attachment != '' && in_array($record->agency_id,[224,2]))
                                            <a data-toggle="modal" class="dropdown-item" data-target="#exampleModal-visiting-aid-document" onclick="getPendingVisitingMedical('{{$va->patient_id}}','{{$va->id}}')">Send To Visiting Aid</a>
                                            @endif
                                        
                                        @endcan
                                        
                                        @if($record->robort_id !="")
                                            @can('remote-send_document')
                                                @if ($va->attachment != '')
                                                <a href="javascript:void(0)" class="dropdown-item" onclick="sendRemoteDocumentByDocumentId('{{$va->patient_id}}','{{$va->id}}')">Send Remote Document</a>
                                                @endif
                                            @endcan
                                        @endif

                                        @if(strpos($record->patient_code, env('INFLOWCARE_IDENTIFICATION')) !== false && $va->attachment != '')
                                            @can('send-inflowcare-document')
                                            <a href="javascript:void(0)" class="dropdown-item" onclick="sendInflowcareByDocumentId('{{$va->patient_id}}','{{$va->id}}')">Send Inflowcare</a>
                                            @endcan
                                        @endif
                                    
                                    </div>
                                </div>
                            @endif
                        </td>
                        @endif
                    </tr>
                <?php $cnt++;
                }
            }
            if (count($document_list) == 0) { ?>
                <tr>
                    <td colspan="10"> Data not found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="pagination pull-right dlog-pegination pegination-margin">
            {{ $document_list->links() }}
        </div>
</div>

<style>
#medication_list_counter,
#insurance_elg_counter,
#mdo_counter {
    transition: all 0.2s ease;
}

#medication_list_counter:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.25) !important;
    border-left-width: 4px;
}

#insurance_elg_counter:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(13, 110, 253, 0.25) !important;
    border-left-width: 4px;
}

#mdo_counter:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(13, 110, 253, 0.25) !important;
    border-left-width: 4px;
}
</style>

<script>
$(document).ready(function() {
    // Update counters
    var medicationListCount = {{ $medication_list_count ?? 0 }};
    var insuranceElgCount = {{ $insurance_elg_count ?? 0 }};
    var mdoCount = {{ $mdo_count ?? 0 }};

    $('#medication_count').text(medicationListCount);
    $('#insurance_count').text(insuranceElgCount);
    $('#mdo_count').text(mdoCount);

    // Initialize MDO popovers - dismiss on outside click
    $('[data-toggle="popover"]').popover();
    $('body').on('click', function(e) {
        $('[data-toggle="popover"]').each(function() {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
});
</script>

<!-- ===== Mark as Send Back to Agency Modal ===== -->
<div class="modal fade" id="markSendBackAgencyModal" tabindex="-1" role="dialog" aria-labelledby="markSendBackAgencyLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markSendBackAgencyLabel">
                    Mark as Send Back to Agency
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="msba_document_id">
                <input type="hidden" id="msba_patient_id">
                <p class="text-muted mb-3" style="font-size:13px;">
                    This will mark the document as <strong>Send Back to Agency: Yes</strong>. Optionally add a note below.
                </p>
                <div class="form-group">
                    <label class="col-form-label">Note <span class="text-muted" style="font-size:11px;">(optional)</span></label>
                    <textarea id="msba_note" class="form-control" rows="3" placeholder="Enter note..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="msba_submit_btn" onclick="submitMarkSendBackToAgency()">
                    <i class="fa fa-check mr-1"></i> Confirm
                </button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function openMarkSendBackModal(documentId, patientId) {
    $('#msba_document_id').val(documentId);
    $('#msba_patient_id').val(patientId);
    $('#msba_note').val('');
    $('#markSendBackAgencyModal').modal('show');
}

function submitMarkSendBackToAgency() {
    var documentId = $('#msba_document_id').val();
    var patientId  = $('#msba_patient_id').val();
    var note       = $('#msba_note').val().trim();

    $('#msba_submit_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

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
            toastr.success(res.message || 'Marked as Send Back to Agency successfully');
            var $modal = $('#markSendBackAgencyModal');
            $modal.one('hidden.bs.modal', function() {
                if (typeof loadDocumentAjaxList === 'function') {
                    loadDocumentAjaxList();
                }
            });
            $modal.modal('hide');
        },
        error: function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong. Please try again.';
            toastr.error(msg);
        },
        complete: function() {
            $('#msba_submit_btn').prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirm');
        }
    });
}
</script>