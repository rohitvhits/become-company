<div class="table-responsive">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>
                <th>#</th>
                <th>Agency Name</th>
                <th>Patient Name</th>
                <th>Form Name</th>
                <th>Status</th>
                <th>Mark As Completed Date/ By</th>
                <th>Created Date/ Created By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1 + ($query->currentPage() - 1) * $query->perPage();
                $countCompleted = 0;
                $countPending = 0;
            @endphp
            @if (count($query) > 0)
                @foreach ($query as $row)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $row->agencies->agency_name }}</td>
                        <td><a href="{{ url('patient/view/') }}/{{ $row->patient->id }}"
                                target="_blank">{{ $row->patient->first_name . ' ' . $row->patient->last_name }}</a></td>
                        <td>{{ $row->forms->title }}</td>
                        <td>
                            @if ($row->mark_as_completed == 1)
                                @php $countCompleted++; @endphp
                                <span class="badge bg-success">Completed</span>
                            @else
                                @php $countPending++; @endphp
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            {{ $row->mark_as_completed_date ? date('m/d/Y', strtotime($row->mark_as_completed_date)) : '-' }}
                            <br>
                            {{ isset($row->userMarkAsComplatedDetails) ? $row->userMarkAsComplatedDetails->first_name . ' ' . $row->userMarkAsComplatedDetails->last_name : '' }}
                        </td>
                        <td>
                            {{ $row->created_at ? date('m/d/Y', strtotime($row->created_at)) : '-' }} <br>
                            {{ isset($row->users) ? $row->users->first_name . ' ' . $row->users->last_name : '' }}
                        </td>
                        <td style="overflow: unset !important">
                            <div class="btn-group pull-right status-dropdoown mr-2">
                                <button type="button" class="btn btn-warning" title="Action">Action</button>
                                <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split"
                                    id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                    <a href="{{ url('agency-all-form-table/view', $row->id) }}?type=FORM_TYPE" data-fancybox data-type="iframe" class="fancybox dropdown-item">View</a>
                                    @if ($row->templateById && $row->templateById->id)
                                        @if ($row->mark_as_completed == '1')
                                            @can('agency-all-form-move-to-esign')
                                                <a href="javascript:void(0)"
                                                    class="moveToEsign{{ $row->id }} addMoveToEsign dropdown-item"
                                                    data-template-id="{{ $row->templateById->id }}" data-id="{{ $row->id }}"
                                                    data-eid="{{ $row->patient_id }}" data-eidc="{{ $row->patient->patient_code }}"
                                                    data-receipt-name="{{ $row->patient->first_name .' '. $row->patient->last_name}}"
                                                    data-type="caregiver">Move To Esign</a>
                                            @endcan
                                        @endif

                                        @can('agency-all-form-download')
                                            <a href="javascript:void(0)"
                                                class="download-icon downloadIcon disabled-icon formdownloadbtn{{$row->id }} dropdown-item"
                                                data-id="{{ $row->id }}" data-form-id="{{ $row->form_id }}"
                                                data-patient-id="{{ $row->patient_id }}" data-agency-id="{{ $row->agency_id }}"
                                                data-template-id="{{ $row->templateById->id }}" data-form-name="{{ $row->forms->title }}">Download
                                                PDF</a>
                                        @endcan
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
            <input type="hidden" name="pending-count" id="pending_count" value="{{ $pendingCountData }}">
            <input type="hidden" name="completed-count" id="completed_count" value="{{ $completeCountData }}">
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
