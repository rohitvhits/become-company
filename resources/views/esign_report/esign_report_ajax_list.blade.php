<div class="table-responsive">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="12%">Agency Name</th>
                <th width="12%">Patient Name</th>
                <th width="10%">Template Name</th>
                <th width="10%">Status</th>
                <th width="15%">Sender</th>
                <th width="13%">Completed Date</th>
                <th width="13%">Created Date/Created By</th>
                <th width="10%">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
                @foreach ($query as $row)
                    <tr>
                        <td width="5%">{{ $i++ }}</td>
                        <td width="12%">{{ isset($row->patient->agencyDetail->agency_name) ? $row->patient->agencyDetail->agency_name : 'N/A' }}
                        </td>
                        <td width="12%"><a href="{{ url('patient/view/') }}/{{ $row->main_intakeId }}"
                            target="_blank">{{ $row->patient->first_name . ' ' . $row->patient->last_name }} ({{$row->patient->id}})</a></td>
                        <td width="10%">
                            {{-- {{ $row->templateDetails->template_name }} --}}
                            @if($row->templateDetails && $row->templateDetails->template_name)
                            {{ $row->templateDetails->template_name }}
                            @elseif($row->writeDocumentDetails && $row->writeDocumentDetails->document_name)
                                {{ $row->writeDocumentDetails->document_name }}
                            @else
                                -
                            @endif
                        </td>
                        <td width="10%">
                            @if ($row->status == 'Completed')
                                <span class="badge bg-success">{{ $row->status ?? '-' }}</span>
                            @else
                                <span class="badge bg-warning">{{ $row->status ?? '-' }}</span>
                            @endif
                        </td>
                        <td width="15%">{{ $row->sender_name }}</td>
                        <td width="13%">{{ $row->completed_on ? date('m/d/Y', strtotime($row->completed_on)) : '-' }}</td>
                        <td width="13%">{{ $row->created_date ? date('m/d/Y h:i A', strtotime($row->created_date)) : '-' }}<br>{{ isset($row->userDetails) ? $row->userDetails->first_name . ' ' . $row->userDetails->last_name : '' }}
                        </td>
                        <td width="10%" style="overflow: unset !important">
                            <div class="btn-group status-dropdoown mr-2">
                                <button type="button" class="btn btn-warning" title="Action">Action</button>
                                <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                    id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                    @if($row->status == "Pending")
                                    <a href="javascript:void(0)" onclick="getDeleteEsignReport('{{ $row->groupId }}')"
                                        class="dropdown-item">Delete</a>
                                    @endif
                                    @if ($row->pdf_generate != "" && $row->pdf_generate != null)
                                    <a href="{{ url('/dre/' . $row->groupId . '?templete_id=' . $row->templete_id) }}" class="dropdown-item">File</a>
                                    @endif
                                    @if($row->signerRemaining != 0)
                                    <a href="javascript:void(0)"
                                    onclick="getSigner('{{ $row->groupId}}', '{{ $row->id }}', '{{ $row->main_intakeId }}')" class="dropdown-item">View</a>
                                    <a href="javascript:void(0)" onclick="getSendSMSEsignReport('{{ $row->groupId }}', '{{ $row->patient->mobile }}', '{{ $row->patient->email }}','{{ $row->main_intakeId }}')" 
                                    class="dropdown-item">Send SMS</a>
                                    @endif
                                    <!-- <a href="javascript:void(0)"
                                        onclick="esignHistory('{{ $row->id }}','{{ $row->main_intakeId }}')"
                                        class="dropdown-item">Esign History</a> -->
                                    @if($row->signerRemaining == 0)
                                    <a data-toggle="modal" href="javascript:void(0)"
                                        data-group-id="{{ $row->groupId }}" data-templete-id="{{ $row->templete_id }}" data-target="#esignMoveDocumentModal-1" data-whatever="@mdo" data-patient-id="{{ $row->main_intakeId }}"
                                        data-patient-type="{{ $row->patient->type }}"
                                        onclick="viewServices('{{ $row->patient->type}}','{{$row->patient->agency_id }}');requestsServices('{{ $row->main_intakeId }}');" class="dropdown-item" data-agency-form-id="{{ $row->agency_form_id }}" data-esign-doc-id="{{ $row->id}}">Move To
                                        Document</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if (count($query) == 0)
                <tr>
                    <td colspan="12">
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