<div class="tableData table-responsive">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th nowrap>Id</th>
                <th nowrap>Status</th>
                @if (in_array($user->user_type_fk, array(3, 184))) 
                <th nowrap> Agency Name </th>
                @endif
                <th nowrap> Type </th>
                <th nowrap> Patient Code </th>
                <th nowrap> Name/Mobile/DOB/Services </th>
                <th nowrap> Created Date/Created By </th>
                <th nowrap> Reason </th>
            </tr>
        </thead>
        <tbody>
            
            @php
            $cnt =1;
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            
            @php $clickHtml = $href = $target =''; @endphp
            @if($row->is_flag_read == 0)
                @php
                   
                    $clickHtml = "makeFlagRead('" . addslashes($row->flag_id) . "', '". addslashes($row->id) ."','Appointment');";
                    $href = "javascript:void(0)";
                    $target = '';
                @endphp
            @else
                @php
                    $clickHtml = "";
                    $href = url('patient/view/' . $row->id);
                    $target = '_blank';
                @endphp
            @endif
            <tr>
                
                <td nowrap>
                <div id="preason{{ $cnt}}" style="display:none">
                    {{ $row->reasonNotes}}
                </div>
                    <div>
                        <a target="{{$target}}" onclick="{{$clickHtml}}" href="{{$href}}"><?= '#' . '' . $row->id ?></a>
                        @if($row->is_flag_read == 0)
                            <div style="position:relative"><span class="add_new_record left_record" >New</span></div>
                        @endif
                    </div>
                </td>
                <td nowrap>
                        @if (strtolower($row->status) == 'pending')
                            <label class='badge badge-warning'>Pending</label>
                        @endif
                        @if(strtolower($row->status) == 'booked')
                            <label class='badge badge-info'>Booked</label>
                        @endif
                        @if (strtolower($row->status) == 'completed')
                            <label class='badge badge-success'>Completed</label>
                        @endif
                        @if (strtolower($row->status) == 'cancelled' || strtolower($row->status) == 'pending termination')
                            <label class='badge badge-danger'>{{ $row->status}}</label>
                        @endif
                        @if (strtolower($row->status) == 'noshow')
                            <label class='badge badge-secondary'>No Show</label>
                        @endif
                        @if (strtolower($row->status) == 'refused' || strtolower($row->status) == 'terminated')
                            <label class='badge badge-danger'>{{ $row->status}}</label>
                        @endif
                        @if (strtolower($row->status) == 'processing' || strtolower($row->status) =='on leave')
                            <label class='badge badge-info'>{{ $row->status}}</label>
                        @endif
                        @if (strtolower($row->status) == 'arrived') 
                            <label class='badge badge-primary'>Arrived</label>
                        @endif
                        @if (strtolower($row->status) == 'checkin')
                            <label class='badge badge-primary'>Mark as ClockIn</label>
                        @endif
                        @if (strtolower($row->status) == 'not interested')
                            <label class='badge badge-primary'>Not Interested</label>
                        @endif
                        @if (strtolower($row->status) == 'hospitalized/rehab')
                            <label class='badge badge-secondary'>Hospitalized/Rehab</label>
                        @endif
                        @if (strtolower($row->status) == 'unabletocontact') 
                            <label class='badge badge-primary'>Unable To Contact</label>
                        @endif
                        @if (strtolower(trim($row->status)) == 'on hold')
                            <label class='badge badge-secondary'>On Hold</label>
                        @endif
                        @if (($row->status) == '1st Attempt - Unable to Contact' || ($row->status) == '2nd Attempt - Unable to Contact' || ($row->status) == '3rd Attempt - Unable to Contact' || ($row->status) == 'Patient Asked to Reschedule' || ($row->status) == 'New Order Received')
                            <label for="" class='badge badge-info'>{{($row->status)}}</label>
                        @endif

                        @if (($row->status) == 'Telehealth Completed' || ($row->status) == 'Telehealth Completed , Pending Forms' || ($row->status) == 'Form Completed' || ($row->status) == 'Service Provided')
                            <label for="" class='badge badge-success'>{{($row->status)}}</label>
                        @endif

                        @if (($row->status) == 'Patient Deceased' || ($row->status) == 'Appointment was missed' || ($row->status) == 'Appointment Missed' || ($row->status) == 'Closed Temporarily')
                            <label for="" class='badge badge-danger'>{{($row->status)}}</label>
                        @endif

                        @if (($row->status) == 'Signed' || ($row->status) == 'Signed & Sent Back to the Agency' || ($row->status) == 'New Form Requested')
                            <label for="" class='badge badge-primary'>{{($row->status)}}</label>
                        @endif
@if (strtolower($row->status) == 'inactive')
                            <label for="" class='badge badge-danger'>{{ ucfirst($row->status)}}</label>
                        @endif
                </td>
                 @if (in_array($user->user_type_fk, array(3, 184))) 
                    <td nowrap><?= $row->agency_name ?> </td>
                @endif

                <td nowrap>{{$row->type}}
                </td>
                <td nowrap>{{ $row->patient_code}}</td>
                <td nowrap>
                    {{ $row->first_name}}  {{$row->last_name}} <br />
                    {{$row->mobile}} <br />
                    @if(isset($row->dob) && $row->dob !='0001-01-01' && $row->dob !='1000-01-01' )
                            {{ date('m/d/Y',strtotime($row->dob)) }}
                    @endif
                            ( {{$row->gender}})<br />
                
                    {{$row->name}} <br />
                </td>
                <td nowrap><?= date('m/d/Y h:i A', strtotime($row->created_at)); ?><br />
                    {{$row->uFname??$row->uFname}} {{$row->uLname??$row->uLname}}
                </td>
                <td nowrap>
                @php 
                $out = strlen($row->reasonNotes) > 50 ? substr($row->reasonNotes,0,50)."..." : $row->reasonNotes;
                @endphp    
                <a href="javascript:void(0)"  style="text-decoration: none; color: inherit;" onclick="patientReasonDescription('{{  $cnt}}')">{{$out}} </a></td>
            </tr>
        @php 
            $cnt++;
        @endphp
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="8">
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