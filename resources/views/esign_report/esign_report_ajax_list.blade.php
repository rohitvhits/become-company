<div class="table-responsive">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                {{--<th width="3%"><input type="checkbox" id="esignReportSelectAll" title="Select All"></th>--}}
                <th width="5%">#</th>
                <th width="10%">Agency Name</th> 
                <th width="10%">Patient Name</th>
                <th width="10%">Type</th>
                <th width="10%">Template Name</th>
                <th width="10%">Template Type</th>
                <th width="10%">Status</th>
                <th width="7%">Signers</th>
                <th width="10%">Sender</th>
                <th width="10%">Review By</th>
                <th width="12%">Created Date/Created By</th>
                <th width="10%">Action</th>
            </tr>
        </thead>
        <tbody>
            
            @php
                $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0) 
                @foreach ($query as $row)
                    @php
                        $allSignersCompleted = isset($row->completedSignerCount) && isset($row->totalSignerCount) && $row->totalSignerCount > 0 && $row->completedSignerCount == $row->totalSignerCount;
                        $groupId = "'" . $row->groupId . "'";

                        // Status
                        $moveToDocument = '';
                        $pdf_status = '';
                        $completed_on_label = '';
                        $statusLabel = '';
                        $sendSMSOption = '';
                        $deleteOption = '';
                        $viewSigner = '';
                        $viewLog = '';
                        $showPdfOption = '';
                        $editPdf = '';
                        $downloadWriteDocument = '';
                        $copyEsignLink = '';
                        $previewOption = '-';
                        $countOfSigner = $row->templete_id == 0 ? '<b>-</b>' : '<b>' . ($row->completedSignerCount ?? 0) . '</b>/<b>' . ($row->totalSignerCount ?? 0) . '</b>';
                    @endphp

                    @if ($row->signerRemaining == 0)
                        @php $completed_on_label = "<span class=''>Completed on <br>" . (Common::convertMDYTime($row->completed_on) ?? '') . "</span>"; @endphp

                        @if ($row->pdf_status === '0')
                            @php
                                $revert = $esignRevert ? "<i class='fa fa-undo undoData' data-id='{$row->id}' aria-hidden='true' title='Revert'></i>" : '';
                                $pdf_status = "<label class='badge badge-outline-danger' style='color:#ff0000;' data-toggle='popover{$row->id}' data-pid='{$row->id}' data-content='{$row->pdf_status_reason}' data-original-title='Rejected' onclick='showData({$row->id})'>Rejected</label><br>{$revert}";
                            @endphp
                        @elseif ($row->pdf_status === '1')
                            @php
                                $revert = $esignRevert ? "<i class='fa fa-undo undoData' data-id='{$row->id}' aria-hidden='true' title='Revert'></i>" : '';
                                $pdf_status = "<label class='badge badge-outline-success' style='color:#28a745;'>Approved</label><br>{$revert}";
                            @endphp
                        @endif

                        @php
                            $statusLabel = is_null($row->pdf_status)
                                ? "<label class='badge badge-outline-success' style='color:#3bb001;'>Completed</label>"
                                : $pdf_status;
                        @endphp

                        @if (($row->pdf_status === '1' && $row->templete_id != 0) || $row->templete_id == 0)
                            @if ($esignMoveDocument)
                                @php
                                    $templateName = $row->templateDetails->template_name ?? ($row->writeDocumentDetails->document_name ?? '');
                                    $pdfGenerate = $row->pdf_generate ?? '';
                                     $moveToDocument = '<a href="javascript:void(0)" 
                                    class="dropdown-item move-to-document"
                                    data-group-id="' . $row->groupId . '"
                                    data-templete-id="' . $row->templete_id . '"
                                    data-template-name="' . htmlspecialchars($templateName, ENT_QUOTES) . '"
                                    data-pdf-generate="' . $pdfGenerate . '"
                                    data-patient-type="' . $row->patient->type . '"
                                    data-agency-id="' . $row->patient->agency_id . '"
                                    data-intake-id="' . $row->main_intakeId . '"
                                    data-agency-form-id="' . $row->agency_form_id . '"
                                    data-esign-doc-id="' . $row->id . '"
                                    data-toggle="modal"
                                    data-target="#esignMoveDocumentModal-1">
                                    Move To Document
                                </a>';
                                @endphp
                            @endif
                        @endif
                    @else
                        @if($row->signerRemaining != 0 && $row->templete_id != 0)
                            @php
                                $copyEsignLink = '<a href="javascript:void(0)" onclick="copyEsignLink(' . $groupId . ')" class="dropdown-item">Copy Esign</a>';
                            @endphp
                        @endif

                        @if ($row->pdf_status === '0')
                            @php
                                $revert = $esignRevert ? "<i class='fa fa-undo undoData' data-id='{$row->id}' aria-hidden='true' title='Revert'></i>" : '';
                                $pdf_status = "<label class='badge badge-outline-danger' style='color:#ff0000;' data-toggle='popover{$row->id}' data-pid='{$row->id}' data-content='{$row->pdf_status_reason}' data-original-title='Rejected' onclick='showData({$row->id})'>Rejected</label><br>{$revert}";
                            @endphp
                        @elseif ($row->pdf_status === '1')
                            @php
                                $revert = $esignRevert ? "<i class='fa fa-undo undoData' data-id='{$row->id}' aria-hidden='true' title='Revert'></i>" : '';
                                $pdf_status = "<label class='badge badge-outline-success' style='color:#28a745;'>Approved</label><br>{$revert}";
                            @endphp
                        @endif

                        @php
                            $statusLabel = is_null($row->pdf_status)
                                ? "<label class='badge badge-outline-warning' style='color:#d76718;'>Pending</label>"
                                : $pdf_status;
                        @endphp
                    @endif

                    @if ($esignView && $row->templete_id != 0 && !in_array($row->pdf_status, ['0','1']) && empty($row->vns_id))
                        @php
                            $viewSigner = '<a href="#" onclick="getSignerNew(' . $groupId . ',' . $row->id . ',' . $row->main_intakeId . ')" class="dropdown-item">View</a>';
                        @endphp
                    @endif

                    @if ($row->signerRemaining != 0 && $row->templete_id != 0 && $esignSendSms)
                        @php
                            $sendSMSOption = '<a href="javascript:void(0)" onclick="getSendSMSByBulk(' . $groupId . ',' . $row->main_intakeId . ')" class="dropdown-item">Send SMS</a>';
                        @endphp
                    @endif

                    @if ($esignDelete)
                        @php
                            $deleteOption = '<a href="javascript:void(0)" onclick="getDeleteEsignTemplate(' . $groupId . ')" class="dropdown-item">Delete</a>';
                        @endphp
                    @endif

                    @if ($esignViewLog)
                        @php
                            $viewLog = '<a href="javascript:void(0)" class="dropdown-item viewLog" data-document-id="' . $row->groupId . '" data-template-id="' . $row->templete_id . '">View Log</a>';
                        @endphp
                    @endif

                    @if ($row->signerRemaining == 0 && $row->templete_id != 0)
                        @php
                            $previewUrl = url('/esign/preview-pdf-response?id=' . $row->id . '&group_id=' . $row->groupId.'&module_type=esignReport');
                            $previewOption = is_null($row->pdf_status)
                                ? "<a href='{$previewUrl}' data-fancybox='' data-type='iframe' class='btn btn-primary btn-sm fancybox' onclick='setHeightFancyBox()'>Review</a>"
                                : ($row->review_first_name ?? '') . '<br>' . (Common::convertMDYTime($row->review_date) ?? '');
                        @endphp
                    @endif

                    @if (!empty($row->pdf_generate) && $row->templete_id != 0 && $esignPdfDownload)
                        @php
                            $showPdfOption = '<a href="' . url('/dre/' . $row->groupId) . '" class="dropdown-item">File</a>';
                        @endphp
                    @endif

                    @if ($row->status === 'Pending' && $row->templete_id == 0 && $esignView)
                        @php
                            $editPdf = '<a class="dropdown-item" href="' . url('/esign/write-document/?id=' . $row->id) . '" target="_blank" title="Edit Pdf">Add Sign/Stamp</a>';
                        @endphp
                    @endif

                    @if ($row->status === 'Completed' && $row->templete_id == 0 && $esignPdfDownload)
                        @php
                            $downloadWriteDocument = '<a target="_blank" href="' . url('/dre-write-document/' . $row->id) . '" class="dropdown-item">Download</a>';
                        @endphp
                    @endif

                    @php
                        $review = $esignReview ? $previewOption : '-';
                    @endphp

                    <tr>
                        {{--<td width="3%">
                            @if(!$allSignersCompleted && isset($row->patient->type) && $row->patient->type == 'Caregiver' && !empty($row->caregiverSignPending) && $row->templete_id != 0 && $esignSendSms)
                                <input type="checkbox" class="esign-report-bulk-checkbox"
                                    value="{{ $row->groupId }}"
                                    data-template-name="{{ $row->templateDetails->template_name ?? '' }}"
                                    data-mobile="{{ $row->patient->mobile ?? '' }}"
                                    data-email="{{ $row->patient->email ?? '' }}"
                                    data-patient-id="{{ $row->main_intakeId }}">
                            @endif
                        </td>--}}
                        <td width="5%">{{ $i++ }}</td>
                        <td width="12%">{{ isset($row->patient->agencyDetail->agency_name) ? $row->patient->agencyDetail->agency_name : 'N/A' }}</td>
                        <td width="12%"><a href="{{ url('patient/view/') }}/{{ $row->main_intakeId }}" target="_blank">{{ $row->patient->first_name . ' ' . $row->patient->last_name }} ({{ $row->patient->id }})</a></td>
                        <td width="12%">{{ $row->patient->type }}</td>
                        <td width="10%">
                            {!! $row->templateDetails->template_name ?? ($row->writeDocumentDetails->document_name ?? '-') !!}
                        </td>
                        <td width="10%">
                            {!! $row->templateDetails->template_type ?? '-' !!}
                        </td>
                        <td width="10%">{!! $statusLabel !!}<br>{!! $completed_on_label !!}</td>
                        <td width="7%">{!! $countOfSigner !!}</td>
                        <td width="13%">{{ $row->sender_name }}</td>
                        <td width="12%">{!! $review !!}</td>
                        <td width="13%">{{ $row->created_date ? Common::convertMDYTime($row->created_date) : '-' }}<br>{{ isset($row->userDetails) ? $row->userDetails->first_name . ' ' . $row->userDetails->last_name : '' }}</td>
                        <td width="10%" style="overflow: unset !important">
                            <div class="btn-group status-dropdoown mr-2 ">
                                <button type="button" class="btn btn-warning" title="Action">Action</button>
                                <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                    id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu action-dropdown" aria-labelledby="dropdownMenuSplitButton6">
                                    {!! $deleteOption . $showPdfOption . $viewSigner . $sendSMSOption . $moveToDocument . $viewLog . $editPdf . $downloadWriteDocument . $copyEsignLink !!}
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if (count($query) == 0)
                <tr>
                    <td colspan="15">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

<script>
    var total = '{{ $query->total()}}';
    $('#blank_div').attr('style','margin-top:10%');
    if(total ==0){
        $('#blank_div').attr('style','margin-top:15%');
    }
    </script>