<div class="row">
    <div class="col-12">
        <div class="tableData">
            <div class="order-listing-loader">
                <i class="fa fa-spinner fa-spin"></i>
            </div>

            <table id="order-listing1" class="table table-bordered table-width1">
                <thead>
                    <tr>
                        <!-- <th nowrap></th> -->
                        <th nowrap>Record Id</th>
                        <th nowrap>Agency Name</th>
                        <th nowrap>Patient ID</th>
                        <th nowrap>Requested ID</th>
                        <th nowrap>Name/Mobile/DOB/Services/Gender/Priority</th>
                        <th nowrap>Type/Discipline</th>

                        <th nowrap>Service Name</th>
                        <th nowrap>Service Status</th>
                     
                        <th nowrap>Portal</th>
                        <th nowrap>API Name</th>
                        <th nowrap>Due Date</th>
                        <th nowrap>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1 + ($query->currentPage() - 1) * $query->perPage();
                    @endphp

                    @if (count($query) > 0)
                        @foreach ($query as $row)
                            <input type="hidden" id="{{ $row->id }}" value="{{ $row->agency_id }}">
                            <tr>
                              
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->agencyDetails->agency_name }}</td>
                                <td style="text-align:center">
                                    @if($row->patient_id != "")
                                        <a href="{{ url('patient/view') }}/{{ $row->patient_id }}" target="_blank">{{ $row->patient_id }}</a>
                                    @endif
                                    <a href="javascript:void(0)" onclick="linkPatient('{{ $row->id }}','{{ $row->agency_id }}')" title="Link Patient" class="@if($row->patient_id != '') hide @endif">
                                        <i class="fa fa-user"></i>
                                    </a>
                                </td>
                                <td>
                                    {{ $row->requested_service_id }}
                                    @if($row->patient_id != '' && $row->requested_service_id == '')
                                        <input class="btn btn-primary btn-fw btn-sm mr-3" onclick="linkServiceRequest('{{ $row->id }}','{{ $row->patient_id }}')" class="@if($row->patient_id == '' || $row->requested_service_id != '') hide @endif" value="Link Services">
                                    @endif
                                </td>
                                <td>
                                    {{ $row->first_name }} {{ $row->last_name }}<br />
                                    {{ $row->mobile }}<br />
                                   
                                    @if(isset($row->dob) && $row->dob != '0001-01-01' && $row->dob !="0000-00-00" && $row->dob != '1000-01-01')
                                        {{ date('m/d/Y', strtotime($row->dob)) }}<br />
                                    @endif
                                    <!-- {{ $row->name }}<br /> -->
                                    {{$row->gender}}<br />
                                    {{ $row->third_party_priority}}
                                </td>
                                <td>
                                    {{ $row->type }}<br />
                                    {{ $row->discipline }}<br />
                                    @if($row->location_branch != "")
                                        <p class="text-muted" style="font-size:10px">({{ $row->location_branch }})</p>
                                    @endif
                                </td>
                              
                                <td>{{ $row->serviceName }}</td>
                                <td>
                                    @if(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status)) 
                                        {{ ucfirst($row->serviceDetails->status)}}
                                    @endif
                                </td>
                                <td>{{ $row->platform_type }}</td>
                                <td>
                                    @if(isset($row->agencyGenerateDetails) && $row->agencyGenerateDetails->notes != "")
                                        {{ $row->agencyGenerateDetails->notes }}
                                    @endif
                                </td>
                                <td>@if(isset($row->due_date) && $row->due_date !="" && $row->due_date !="0000-00-00"){{ date('m/d/Y', strtotime($row->due_date)) }}@endif</td>
                                <td>{{ date('m/d/Y h:i A', strtotime($row->created_date)) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="15">
                                <center><b>Data not found</b></center>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="pull-right pagination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

<script>
    $('#appointment_id').html("{{ $query->total() }}");
</script>
