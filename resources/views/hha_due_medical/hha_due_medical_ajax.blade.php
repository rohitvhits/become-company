<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
           
            <th>No</th>
            <th style="white-space:nowrap">Agency Name</th>
            <th style="white-space:nowrap">Office Name</th>
            <th style="white-space:nowrap">Caregiver Full Name</th>
            <th style="white-space:nowrap">Caregiver Code</th>
            <th  style="white-space:nowrap">Caregiver Phone</th>
            <th>DOB</th>
            <th style="white-space:nowrap">Caregiver Status</th>
            <th style="white-space:nowrap">Hire Date</th>
            <th style="white-space:nowrap">Language</th>
            <th style="white-space:nowrap">Discipline</th>
            <th style="white-space:nowrap">Employeement Type</th>
            <th style="white-space:nowrap">Medical Name</th>
            <th style="white-space:nowrap">Due Date</th>
            <th style="white-space:nowrap">Date Perform</th>
            <th style="white-space:nowrap">Medical Status</th>
            <th style="white-space:nowrap">Appointment Status</th>
            <th style="white-space:nowrap">First Work Date</th>
            <th style="white-space:nowrap">Last Work Date</th>
            <th style="white-space:nowrap">Last SYNC Date</th>
            <th style="white-space:nowrap">Action</th>
        </tr>
    </thead>
    <tbody>
        
    @php
    $i = 1 + (($query->currentPage() - 1) * $query->perPage());
        $officeCode = "";
        $officeName = "";
        @endphp
        @if (count($query) > 0)
            @foreach ($query as $row)
        
            @if(isset($office_list[$row->office_id]))
                @php
                    $parts = explode(' - ', $office_list[$row->office_id]);
                    $officeCode = $parts[1] ?? ''; // safely get the second part
                    $officeName = $parts[0] ?? '';
                @endphp
            @endif

            <tr>
                

                <td>{{ $i++}}</td>
                <td>
                    @if(isset($agencyListDetails[$row->agency_id]))
                            {{ $agencyListDetails[$row->agency_id] }}
                    @endif
                </td>
                <td>
                        
                    {{ $officeName}}
                    
                </td>
                <td><a title="" style="color:#007bff !important" onclick="openCaregiverModal('{{ $row->agency_id}}','{{ $row->caregiver_id}}','{{ $row->caregiver_first_name}} {{ $row->caregiver_middle_name}} {{ $row->caregiver_last_name}}')">{{ $row->first_name}} {{ $row->middle_name}} {{ $row->last_name}}</a></td>


                <td><a title="" style="color:#007bff !important" onclick="openCaregiverModal('{{ $row->agency_id}}','{{ $row->caregiver_id}}','{{ $row->caregiver_first_name}} {{ $row->caregiver_middle_name}} {{ $row->caregiver_last_name}}')">{{ $officeCode.' - '.$row->caregiver_code}}</a></td>
                <td>{{ $row->mobile_or_sms}}</td>
                <td>{{  ($row->dob !="" && $row->dob !="0000-00-00")?date('m/d/Y',strtotime($row->dob)):"NA"}}</td>
                
                
                <td>{{ $row->caregiverStatus}}</td>
                <td>
                {{  ($row->hire_date !="" && $row->hire_date !="0000-00-00" && $row->hire_date !="1969-12-31")?date('m/d/Y',strtotime($row->hire_date)):"NA"}}
                </td>
                <td>{{ $row->language}}</td>
                <td>{{ $row->EmploymentTypesDiscipline}}</td>
                <td>{{ $row->employment_type}}</td>
                <td>{{ $row->medical_name}}</td>

                <td>@if($row->due_date !="" && $row->due_date !="0000-00-00 00:00:00") {{ date('m/d/Y',strtotime($row->due_date))}} @endif</td>
                <td>@if($row->date_perform !="" && $row->date_perform !="0000-00-00 00:00:00") {{ date('m/d/Y',strtotime($row->date_perform))}} @endif</td>
                
                <td>{{ $row->status}}</td>
            
                <td>
                    @if($row->patientId !='')
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->patientId }}"><span class="badge badge-success">Added</span></a>
                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif


                </td>
                
                <td>@if($row->first_work_date !="" && $row->first_work_date !='0000-00-00' && $row->first_work_date !='1969-12-31'){{ date('m/d/Y',strtotime($row->first_work_date))}} @endif</td>
                <td>
                    @if($row->last_work_date !="" && $row->last_work_date !='0000-00-00' && $row->first_work_date !='1969-12-31'){{ date('m/d/Y',strtotime($row->last_work_date))}} @endif
                </td>
                <td>
                    @if($row->updated_date !="" && $row->updated_date !='0000-00-00' && $row->updated_date !='1969-12-31'){{ date('m/d/Y h:i A',strtotime($row->updated_date))}} @endif
                </td>
                <td>
                    @if($row->patientId =='')
                        @can('add-appointment-hha-medical')
                        <a href="javascript:void(0)" onclick="singleDataAppointment('{{ $row->id}}')" title=" Add Appointment"><i class="fa fa-calendar"></i></a>
                        @endcan
                    @else
                    <a href="{{ url('patient/view')}}/{{ $row->patientId }}" ><i class="fa fa-eye"></i> View</a>
                    @endif
                </td>
                
            </tr>
            @endforeach
        @endif
        @if (count($query) == 0)
            <tr>
                <td colspan="20">
                    <span style="text-align:center">No record available</span>
                    
                </td>
            </tr>
            @endif
    </tbody>
</table>
<div class="pull-right pegination-margin hha_appointment_paginate">
{{ $query->links() }}
</div>
