<table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th style="white-space:nowrap">
                    <input type="checkbox" id="cboxid">
                </th>
               <th style="white-space:nowrap">
                    No
                </th>
                <th style="white-space:nowrap">
                    Office Name
                </th>
                <th style="white-space:nowrap">
                    <span>Agency Name</span>
                </th>
                 <th style="white-space:nowrap">
                    <span>Patient Full Name <br> Gender</span>
                </th>
               
                <th style="white-space:nowrap">
                    <span>Admission ID</span>
                </th>
                <th style="white-space:nowrap">
                    <span>Home Phone</span>
                </th>
            
                <th style="white-space:nowrap">
                    <span>Coordinator Name</span>
                </th>
                <th style="white-space:nowrap">
                    <span>Service  Start Date</span>
                </th>
                <th style="white-space:nowrap">
                    <span>DOB</span>
                </th>
                <th style="white-space:nowrap">
                    <span>Discipline</span>
                </th>
                <th style="white-space:nowrap">
                    <span>Medicaid Number</span>
                </th>
                <th style="white-space:nowrap">
                    <span>Medicare <br> Number</span>
                </th>
                <th style="white-space:nowrap">
                    <span>HHA Status</span>
                </th>
                <th style="white-space:nowrap">
                    <span>Last Sync Date</span>
                </th>
                <th style="white-space:nowrap">
                    <span>Status</span>
                </th>
                
                <th style="white-space:nowrap">
                    <span>Action</span>
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
                    @if($row->patient_record_id !='')

                    @else
                    <input type="checkbox" name="cbox" class="cbox" value="{{ $row->id}}">
                    @endif
                </td>
                <td>{{ $i++}}</td>
                <td>{{$row->office_name.' '.$row->office_code}} </td>
                <td>{{ $row->agencyDetail->agency_name??""}}</td>
                <td><a onclick="openHHAPatientModal('{{ $row->agency_fk}}','{{ $row->patient_id}}','{{ addslashes($row->first_name . ' ' . $row->last_name) }}')">{{ $row->first_name}}  {{ $row->last_name}}</a> <br> {{ $row->gender}}</td>
               
                <td><a onclick="openHHAPatientModal('{{ $row->agency_fk}}','{{ $row->patient_id}}','{{ addslashes($row->first_name . ' ' . $row->last_name) }}')">{{ $row->admission_id}}</a></td>
                
                <td>{{ $row->home_phone}}</td>
                <td>{{ $row->coordinator_name}}</td>
                
                <td>{{ date('m/d/Y',strtotime($row->service_start_date))}}</td>
                <td>{{  ($row->dob=="")?"NA": date('m/d/Y',strtotime($row->dob))}}</td>
               <td>@if($row->EmploymentTypesDiscipline !="") {{ $row->EmploymentTypesDiscipline}} @else N/A @endif</td>
               <td>@if($row->medicaid_number !=""){{ $row->medicaid_number}} @else @endif</td>
                <td>{{ $row->medicare_number}}</td>
              <td>{{ $row->status}}</td>
              <td>{{ date('m/d/Y h:i A',strtotime($row->hhasyncdatetime))}}</td>

              <td>
              @if($row->patient_record_id !='')
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->patient_record_id }}"><span class="badge badge-success">Added</span></a>
                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif
              </td>
       
               <td> @if($row->patient_record_id =='')
                    @can('add-appointment-hha-patient')
                        <a href="javascript:void(0)" onclick="singleDataAppointment('{{ $row->id}}','{{ $row->agency_fk }}')"  title=" Add Appointment"><i class="fa fa-calendar"></i></a>
                    @endcan
                    @else
                    <a href="{{ url('patient/view')}}/{{ $row->patient_record_id }}" ><i class="fa fa-eye"></i> View</a>
                    @endif
                </td>
            </tr>
            @endforeach

            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="20">
                    <center><b>Data not found</b></center>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
