<style>

</style>
<div class="">
    <div class="order-listing-loader">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>No</span>
                       
                    </div>
                </th>

                 <th style="white-space:nowrap">
                    <div class="sorting-div"><span>HRC-Code</span>
                        
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Agency Name</span>
                        
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Caregiver Name</span>
                        
                    </div>
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Last Work Date</span>
                      
                    </div>
                </th>
               <th style="white-space:nowrap">
                    <div class="sorting-div"><span>NY Best Med Liaison Name</span>
                       
                    </div>
                </th>
            
                
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Due Date</span>
                    
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Number of attempts</span>
                        
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Medicals/Training Notes</span>
                        
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Coordinator Name</span>
                        
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Status</span>
                        
                    </div>
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
                
                 <td>
                 <a href="{{ url('/patient/view/')}}/{{ $row->id}}" target="_blank">{{  $row->patient_code}}</a>
                </td>
                <td>
                
               {{  $row->agencyDetail->agency_name}}
                
                </td>
               <td>
                
               <a href="{{ url('/patient/view/')}}/{{ $row->id}}" target="_blank">{{  $row->first_name}} {{  $row->last_name}}  </a>
                
                </td>
                <td>
                    @if($row->hhaAppoinmets !="")
                        @if(isset($row->hhaAppoinmets->hhaCaregivers->last_work_date) && $row->hhaAppoinmets->hhaCaregivers->last_work_date !="")
                        {{ date('m/d/Y',strtotime($row->hhaAppoinmets->hhaCaregivers->last_work_date))}}
                        @endif
                    @endif
                </td>
                <td>
                    @if($row->assignToUser  !="")    
                        {{ $row->assignToUser->first_name }}    {{ $row->assignToUser->last_name }}
                        @endif
                </td>
                 
              
                <td>
                    @if($row->due_date !="" || $row->due_date !="1969-12-31")    
                        {{ date('m/d/Y',strtotime($row->due_date))}}
                    @endif
                </td>
                
               
                <td>{{ $row->total_agency}}</td>
                <td>NA</td>
                <td>NA</td>
                <td>
                    @if(strtolower($row->status) == 'pending')
                    <label class='badge badge-warning'>Pending</label>
                    @elseif(strtolower($row->status) == 'booked')
                    <label class='badge badge-info'>Booked</label>
                    @elseif(strtolower($row->status) == 'completed')
                    <label class='badge badge-success'>Completed</label>
                    @elseif(strtolower($row->status) == 'cancelled')
                    <label class='badge badge-danger'>Cancelled</label>
                    @elseif(strtolower($row->status) == 'noshow')
                    <label class='badge badge-danger'>No Show</label>
                    @elseif(strtolower($row->status) == 'refused')
                    <label class='badge badge-danger'>Refused</label>
                    @elseif(strtolower($row->status) == 'processing')
                    <label class='badge badge-info'>processing</label>
                    @elseif(strtolower($row->status) == 'arrived')
                    <label class='badge badge-primary'>Arrived</label>
                    @elseif(strtolower($row->status) == 'checkin')
                    <label class='badge badge-primary'>Mark as ClockIn</label>
                    @elseif(strtolower($row->status) == 'not interested')
                    <label class='badge badge-primary'>Not Interested</label>
                    @elseif(strtolower($row->status) == 'hospitalized/rehab')
                    <label class='badge badge-secondary'>Hospitalized/Rehab</label>
                    @elseif(strtolower($row->status) == 'unabletocontact')
                    <label class='badge badge-primary'>Unable To Contact</label>
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
</div>
<div class="pull-right pegination-margin">
{{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
<script>
     $('#appointment_id').html("{{$query->total()}}");
    
</script>