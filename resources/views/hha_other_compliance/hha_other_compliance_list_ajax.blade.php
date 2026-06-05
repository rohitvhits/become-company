<table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span><input type="checkbox" id="cboxid"></span>
                        <div class="sorting-btn">

                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                No
                </th>

                <th style="white-space:nowrap">
                Agency Name
                </th>
                <th style="white-space:nowrap">
                Office Name
                </th>
                <th style="white-space:nowrap">
                Caregiver Full Name
                </th>

                <th style="white-space:nowrap">
                Caregiver Code
                </th>
                <th style="white-space:nowrap">
                Caregiver Phone
                </th>
            
                <th style="white-space:nowrap">
                Medical Name
                </th>
                <th style="white-space:nowrap">
                    Due Date
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>DOB</span>
                        
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Discipline</span>
                        
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Team</span>
                        
                    </div>
                </th>
                <th style="white-space:nowrap">
                    Status
                </th>
                <th style="white-space:nowrap">
                    Action
                </th>

            </tr>
            
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            <tr>
                <td>
                    @if($row->patient_id !='')

                    @else
                    <input type="checkbox" name="cbox" class="cbox" value="{{ $row->id}}">
                    @endif
                </td>
                <td>{{ $i++}}</td>
                
                <td>{{ $row->agency_name}}</td>
                <td>{{ $row->office_name}}</td>
                <td><a title="" style="color:#007bff !important" onclick="openCaregiverModal('{{ $row->agency_id}}','{{ $row->caregiver_id}}','{{ $row->caregiver_first_name}} {{ $row->caregiver_middle_name}} {{ $row->caregiver_last_name}}')">{{ $row->caregiver_first_name}} {{ $row->caregiver_middle_name}} {{ $row->caregiver_last_name}}</a></td>
                <td><a title="" style="color:#007bff !important" onclick="openCaregiverModal('{{ $row->agency_id}}','{{ $row->caregiver_id}}','{{ $row->caregiver_first_name}} {{ $row->caregiver_middle_name}} {{ $row->caregiver_last_name}}')">{{ $row->office_code .' - '.$row->caregiver_code}}</a></td>
                <td>{{ $row->caregiver_phone}}</td>
                <td>{{ $row->medical_name}}</td>
                
                <td>
                @if($row->due_date !="0000-00-00 00:00:00")
                {{ date('m/d/Y',strtotime($row->due_date))}}
            @endif
            </td>
                <td>{{  ($row->dob=="")?"NA": date('m/d/Y',strtotime($row->dob))}}</td>
                <td>{{ $row->EmploymentTypesDiscipline}}</td>
                <td>{{ ($row->TeamName  !="")?$row->TeamName:"NA"}}</td>
                <td>
                    @if($row->patient_id !='')
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->patient_id }}"><span class="badge badge-success">Added</span></a>
                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif


                </td>
                <td> @if($row->patient_id =='')
                    <a href="javascript:void(0)" onclick="singleDataAppointment({{ $row->id}})" title="Add Appointment"><i class="fa fa-calendar"></i> </a>
                    @else
                    <a href="{{ url('patient/view')}}/{{ $row->patient_id }}" ><i class="fa fa-eye"></i> View</a>
                    @endif
                </td>
            </tr>
            @endforeach

            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="11">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
            @endif
        </tbody>
    </table>

<div class="pull-right hha_other_compliance_paginate pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
<script>
    $('#appointment_id').html("{{$query->total()}}");
    
</script>