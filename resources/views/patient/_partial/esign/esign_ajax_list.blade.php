<table  class="table table-bordered">
    <thead>
        <tr>
            {{--  @if(strtolower($record->type) =='caregiver')
            <th style="width:40px;"><input type="checkbox" id="esignSelectAll" title="Select All"></th>
            @endif--}}
            <th style="width:100px;">Record</th>
            <th>Template Name</th>
            <th>Status</th>
            <th>Sender</th>
            <th>Review By</th>
            <th>Signers</th>
            <th>Added By</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php $cnt = ($page * 50)-49; @endphp
        @if(count($response) >0)
            @foreach ($response as $v)
                @php
                    $moveToDocument = '';
                    $pdf_status = '';
                    $completed_on = '';
                    $status = '';
                    $previewOption = '-';
                    $groupId = "'" . $v->groupId . "'";
                    $sendSMSOption = '';
                    $deleteOption = '';
                    $viewSigner = '';
                    $viewLog = '';
                    $showPdfOption = '';
                    $editPdf = '';
                    $downloadWriteDocument = '';
                    $countOfSigner = '-';
                    $copyEsignLink="";
                    $formCompleteOption = '';
                    $requireSignatureOption = '';
                    $autoNotifiedBadge = '';
                   
                @endphp

                {{-- Auto-notified badge --}}
                @if(isset($v->auto_notified) && $v->auto_notified == 1)
                    @php
                        $autoNotifiedBadge = '<span class="badge badge-info" title="Signer was auto-notified" style="font-size:10px;">Auto-Notified</span>';
                    @endphp
                @endif

                @if ($v->signerRemaining == 0)

                    @php $completed_on = "<span class=''>Completed on <br>{$v->completed_on}</span>"; @endphp

                    @if ($v->pdf_status === '0')
                        @php
                            $revert = $esignRevert ? "<i class='fa fa-undo undoData' data-esign-type='0' data-id='{$v->id}' aria-hidden='true' title='Revert'></i>" : '';
                            $pdf_status = "<label for='' class='badge badge-outline-danger' style='color:#ff0000;' data-toggle='popover{$v->id}' data-pid='{$v->id}' data-content='{$v->pdf_status_reason}' data-original-title='Rejected' onclick='showData({$v->id})'>Rejected</label><br>{$revert}";
                        @endphp
                    @elseif ($v->pdf_status === '1')
                        @php
                            $revert = $esignRevert ? "<i class='fa fa-undo undoData' data-esign-type='0' data-id='{$v->id}' aria-hidden='true' title='Revert'></i>" : '';
                            $pdf_status = "<label for='' class='badge badge-outline-success' style='color:#28a745;'>Approved</label><br>{$revert}";
                        @endphp
                    @endif

                    @php
                        $status = is_null($v->pdf_status)
                            ? "<label for='' class='badge badge-outline-success' style='color:#3bb001;'>Completed</label>"
                            : $pdf_status;
                    @endphp

                    @if ($v->pdf_status === '1' && $v->templete_id != 0 || $v->templete_id == 0)
                        @if ($esignMoveDocument)
                            @php
                                $templateName = $v->templateDetails->template_name ?? ($v->writeDocumentDetails->document_name ?? '');
                                $pdfGenerate = $v->pdf_generate ?? '';
                                $moveToDocument = '<a data-toggle="modal" href="javascript:void(0)" data-group-id="' . $v->groupId . '" data-templete-id="' . $v->templete_id . '" data-template-name="' . htmlspecialchars($templateName, ENT_QUOTES) . '" data-pdf-generate="' . $pdfGenerate . '" data-type="Esign" data-target="#esignMoveDocumentModal-1" data-whatever="@mdo" onclick="viewServices();requestsServices();showDocumentApproval()" class="dropdown-item" data-agency-form-id="' . $v->agency_form_id . '" data-esign-doc-id="' . $v->id . '">Move To Document</a>';
                            @endphp
                        @endif
                    @endif
                @else


                    @if($v->signerRemaining != 0 && $v->templete_id != 0)
                        @php
                            $copyEsignLink='<a  href="javascript:void(0)" onclick="copyEsignLink(' . $groupId . ')" class="dropdown-item">Copy Esign</a>';
                        @endphp
                    @endif

                    @if ($v->pdf_status === '0')
                        @php
                            $revert = $esignRevert ? "<i class='fa fa-undo undoData' data-id='{$v->id}' aria-hidden='true' title='Revert'></i>" : '';
                            $pdf_status = "<label for='' class='badge badge-outline-danger' style='color:#ff0000;' data-toggle='popover{$v->id}' data-pid='{$v->id}' data-content='{$v->pdf_status_reason}' data-original-title='Rejected' onclick='showData({$v->id})'>Rejected</label><br>{$revert}";
                        @endphp
                    @elseif ($v->pdf_status === '1')
                        @php
                            $revert = $esignRevert ? "<i class='fa fa-undo undoData' data-id='{$v->id}' aria-hidden='true' title='Revert'></i>" : '';
                            $pdf_status = "<label for='' class='badge badge-outline-success' style='color:#28a745;'>Approved</label><br>{$revert}";
                        @endphp
                    @endif

                    @php
                        $status = is_null($v->pdf_status)
                            ? "<label for='' class='badge badge-outline-warning' style='color:#d76718;'>Pending</label>"
                            : $pdf_status;
                    @endphp
                @endif

                @if ($esignView && $v->templete_id != 0 && !in_array($v->pdf_status,['0','1']) && empty($v->vns_id))

                    @php

                        $viewSigner = '<a href="#" onclick="getSignerNew(' . $groupId . ',' . $v->id . ',' . $v->main_intakeId . ')" class="dropdown-item">View </a>';
                    @endphp
                @endif

                @if ($v->signerRemaining != 0 && $v->templete_id != 0 && $esignSendSms)
                    @php
                        $sendSMSOption = '<a href="javascript:void(0)" onclick="getSendSMSNew1(' . $groupId . ')" class="dropdown-item">Send SMS</a>';
                    @endphp
                @endif

                @if ($esignDelete)
                    @php
                        $deleteOption = '<a href="javascript:void(0)" onclick="getDeleteEsignTemplate(' . $groupId . ')" class="dropdown-item">Delete</a>';
                    @endphp
                @endif

                @if ($esignViewLog)
                    @php
                        $viewLog = '<a href="javascript:void(0)" class="dropdown-item viewLog" data-document-id="' . $v->groupId . '" data-template-id="' . $v->templete_id . '">View Log</a>';
                    @endphp
                @endif

                @if ($v->signerRemaining == 0 && $v->templete_id != 0)
                    @php
                        $previewUrl = url('/esign/preview-pdf-response?id=' . $v->id . '&group_id=' . $v->groupId);
                        $previewOption = is_null($v->pdf_status)
                            ? "<a href='{$previewUrl}' data-fancybox='' data-type='iframe' class='btn btn-primary btn-sm fancybox'>Review</a>"
                            : ($v->review_first_name ?? '')  . '<br>' . ($v->review_date ?? '');
                    @endphp
                @endif

                @if (!empty($v->pdf_generate) && $v->templete_id != 0 && $esignPdfDownload)
                    @php
                        $showPdfOption = '<a href="' . url('/dre/' . $v->groupId) . '" class="dropdown-item">File</a>';
                    @endphp
                @endif

                @if ($v->status === 'Pending' && $v->templete_id == 0 && $esignView)
                    @php
                        $editPdf = '<a class="dropdown-item" href="' . url('/esign/write-document/?id=' . $v->id) . '" target="_blank" title="Edit Pdf">Add Sign/Stamp</a>';
                    @endphp
                @endif

                @if ($v->status === 'Completed' && $v->templete_id == 0 && $esignPdfDownload)
                    @php
                        $downloadWriteDocument = '<a target="_blank" href="' . url('/dre-write-document/' . $v->id) . '" class="dropdown-item">Download</a>';
                    @endphp
                @endif

                @php
                  $countOfSigner = $v->templete_id == 0 ? '<b>-</b>' : "<b>{$v->completedCount}</b>/<b>{$v->sentOnCount}</b>";


                    $actionButton = '<div class="btn-group pull-right status-dropdoown mr-2">
                                        <button type="button" class="btn btn-warning" title="Action">Action</button>
                                        <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                            id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                            ' . $formCompleteOption . $requireSignatureOption . $deleteOption . $showPdfOption . $viewSigner . $sendSMSOption . $moveToDocument . $viewLog . $editPdf . $downloadWriteDocument .$copyEsignLink. '
                                        </div>
                                    </div>';

                    $review = $esignReview ? $previewOption : '-';
                @endphp

                @if(!empty($v->vns_id))
                    @php
                        $countOfSigner = '<b>-</b>';
                    @endphp
                @endif
                <tr>
                  {{--  @if(strtolower($record->type) =='caregiver')
                        <td>
                            @if($record->type == 'Caregiver' && !empty($v->caregiverSignPending) && $v->templete_id != 0 && $esignSendSms)
                                <input type="checkbox" class="esign-bulk-checkbox" value="{{ $v->groupId }}" data-template-name="{{ $v->templateDetails->template_name ?? '' }}">
                            @endif
                        </td>
                    @endif--}}
                    <td>{{ $cnt++ }}
                    @if($record->id != $v->main_intakeId)
                            <span class="badge badge-info">Merge</span>
                        @endif
                    </td>
                    <td>
                        @if(strtolower($record->type) =='patient')
                            @if($v->templete_id != 0 && $esignView && $v->signerRemaining != 0 && !in_array($v->pdf_status,['0','1']) && empty($v->vns_id))
                                {{-- Clickable hyperlink to open fillable PDF --}}
                                <a href="{{ url('esign/next-signer') }}/{{ $v->groupId}}" target="_blank" class="text-primary" style="text-decoration:none;cursor:pointer;">
                                    {!! $v->templateDetails->template_name ?? ($v->writeDocumentDetails->document_name ?? '-') !!}
                                </a>
                            @elseif($v->signerRemaining == 0 && $v->templete_id != 0 && !empty($v->pdf_generate) && $esignPdfDownload)
                                {!! $v->templateDetails->template_name ?? ($v->writeDocumentDetails->document_name ?? '-') !!}
                            @else
                                {!! $v->templateDetails->template_name ?? ($v->writeDocumentDetails->document_name ?? '-') !!}
                            @endif
                        @else
                            {!! $v->templateDetails->template_name ?? ($v->writeDocumentDetails->document_name ?? '-') !!}
                        @endif
                        
                    </td>
                    <td>
                        {!! $status !!}<br>{!! $completed_on !!}
                        @if(!empty($autoNotifiedBadge))
                            <br>{!! $autoNotifiedBadge !!}
                        @endif
                     
                    </td>
                    <td>{{ $v->user_first_name }}</td>
                    <td>{!! $review !!}</td>
                    <td>{!! $countOfSigner !!}</td>
                    <td>{{ $v->user_first_name }}<br>{{ $v->created_date }}</td>
                    <td style="overflow: unset !important">
                        @if(isset($record->id) && $record->id == $v->main_intakeId)
                            {!! $actionButton !!}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
        @if(count($response) ==0)
        <tr><td colspan="9">No record available</td></tr>

        @endif
    </tbody>
</table>

<div class="pull-right esign_paginate pegination-margin" id="esign_paginate">
{{ $response->links() }}
</div>