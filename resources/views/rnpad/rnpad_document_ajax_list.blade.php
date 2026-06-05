<table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
    <thead>
        <tr>
            <th style="white-space:nowrap">No</th>
            <th style="white-space:nowrap">Agency Name</th>
            <th style="white-space:nowrap">Patient Name</th>
            <th style="white-space:nowrap">Document Name</th>
            <th style="white-space:nowrap">Service</th>
            <th style="white-space:nowrap">Attachment</th>
            <th style="white-space:nowrap">Status</th>
            <th style="white-space:nowrap">Created Date <br>Created By</th>
            
            <th style="white-space:nowrap">Send Status</th>
            <th style="white-space:nowrap">Action</th>
        </tr>
    </thead>
    <tbody>
        
        @php
        $i = 1 + ($query->currentPage() - 1) * $query->perPage();
        @endphp
        @if (count($query) > 0)
        @foreach ($query as $row)
        <tr>
            <td>{{ $i++}}<br>
            @if($row->internal_use == 1)
            <span class="badge badge-primary badge-pill">Internal Use </span>
            @endif


            </td>
            <td>
                {{ $row->agency_name}}
            </td>
            <td>
                @if($row->patient)
                    <a href="{{ url('patient/view')}}/{{ $row->patient_id}}" target="_blank">{{ $row->full_name }}</a>
                @else
                N/A
                @endif
            </td>
            <td>{{ $row->document_name ?? 'N/A' }}</td>
            <td>
                @if($row->service)
                {{ $row->service ?? 'N/A' }}
                @else
                N/A
                @endif
            </td>
            <td>
                @if($row->attachment !="")
                <a target="_blank" href="<?php echo URL::to('/'); ?>/dpp/{{ $row->document_id}}"><i class="fa fa-download"></i> Download</a>
                            <br>
                            <a href="{{ url('view-pdf-response')}}?id={{ $row->document_id}}" data-fancybox="" data-type="iframe" class="fancybox"><i class="fa fa-eye"></i>View</a>
                @endif
            </td>
            <td>
                {{$row->patientServiceStatus}}
            </td>
            <td>
                @if($row->created_date)
                {{ date('m/d/Y h:i A', strtotime($row->created_date)) }}
                @else
                N/A
                @endif<br>
                {{ $row->createdUserFirstName.' '.$row->createdUserLastName}}
            </td>
            
            <td>
                @if($row->send_third_party_document_date)
                Yes
                @else
                No
                @endif
            </td>
            <td>
            @can('send-to-rnpad')
                @if(empty($row->send_third_party_document_date))
                    <a onclick="openRNPadModal('{{$row->document_id}}','{{$row->patient_id}}','{{$row->agency_id}}');" class="btn btn-primary btn-sm" title="Send To RN pad">Send To RN pad</a>
                @endif
            
            @endcan
            </td>
        </tr>
        @endforeach
        @endif

        @if (count($query) == 0)
        <tr>
            <td colspan="10">
               <span class="text-center"><b>No record available</b></span>
            </td>
        </tr>
        @endif
    </tbody>
</table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
