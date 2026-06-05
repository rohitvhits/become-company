<style>

</style>
<div class="">
    
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
               
                <th style="white-space:nowrap">
                    No
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Agency Name</span>
                       
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Caregiver ID</span>
                       
                    </div>
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
                Gender
                </th>
                
                <th style="white-space:nowrap">
                DOB
                </th>

                <th style="white-space:nowrap">
                Last Work Date
                </th>
                
                <th style="white-space:nowrap">
                Discipline
                </th>
                <th style="white-space:nowrap">
                Team
                </th>
                <th style="white-space:nowrap">
                Status
                </th>
                <th style="white-space:nowrap">
                Last Sync Date
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
                <td>{{ $i++}}</td>
                <td>@if(isset($row->agencyDetails->agency_name) && $row->agencyDetails->agency_name !=""){{ $row->agencyDetails->agency_name }} @endif</td>
                <td><a title="" style="color:#007bff !important" onclick="openCaregiverModal('{{ $row->agency_fk}}','{{ $row->caregiver_id}}','{{ $row->first_name}} {{ $row->middle_name}} {{ $row->last_name}}')">{{ $row->caregiver_id}}</a></td>
                <td><a title="" style="color:#007bff !important" onclick="openCaregiverModal('{{ $row->agency_fk}}','{{ $row->caregiver_id}}','{{ $row->first_name}} {{ $row->middle_name}} {{ $row->last_name}}')">{{ $row->first_name}} {{ $row->middle_name}} {{ $row->last_name}}</a></td>
                <td><a title="" style="color:#007bff !important" onclick="openCaregiverModal('{{ $row->agency_fk}}','{{ $row->caregiver_id}}','{{ $row->first_name}} {{ $row->middle_name}} {{ $row->last_name}}')">{{ $row->caregiver_code}}</a></td>
                <td>{{ $row->mobile_or_sms}}</td>
               <td>{{ $row->gender}}</td>
                <td>{{  ($row->dob=="")?"NA": date('m/d/Y',strtotime($row->dob))}}</td>
                <td>@if($row->last_work_date !="" && $row->last_work_date !="0000-00-00" && $row->last_work_date !='1969-12-31') {{ date('m/d/Y',strtotime($row->last_work_date))}} @endif</td>
                <td>{{ $row->EmploymentTypesDiscipline}}</td>
                <td>{{ ($row->TeamName  !="")?$row->TeamName:"NA"}}</td>
                <td>
                    {{ $row->status}}
                </td>
                <td>@if($row->hhasyncdatetime !="" && $row->hhasyncdatetime !="0000-00-00 00:00:00" && $row->hhasyncdatetime !='1969-12-31') {{ date('m/d/Y h:i A',strtotime($row->hhasyncdatetime))}} @endif</td>
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
</div>
<div class="pull-right pegination-margin hha_caregiver_paginate">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
<script>
    $('#appointment_id').html("{{$query->total()}}");
    
</script>