<style>

</style>
<div class="">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th width="10%">Id</th>
                <th width="10%">Status</th>
                <th width="25%"> Agency Name </th>
                <th width="10%"> Type </th>
                <th width="10%"> Patient Code </th>
                <th width="25%"> Name/Mobile/DOB/Services </th>
                <th width="10%"> Created Date/Cretaed By </th>
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
                    <div>
                        <a href="{{url('patient/view/')}} {{$row->id}}"><?= '#' . '' . $row->id ?></a>
                    </div>
                </td>
                <td >
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

                </td>
                 @if (in_array($user->user_type_fk, array(3, 184))) 
                    <td><?= $row->agency_name ?> </td>
                @endif

                <td >{{$row->type}}
                </td>
                <td >{{ $row->patient_code}}</td>
                <td >
                    {{ $row->first_name}}  {{$row->last_name}} <br />
                    {{$row->mobile}} <br />
                    @if(isset($row->dob) && $row->dob !='0001-01-01' && $row->dob !='1000-01-01' )
                            {{ date('m/d/Y',strtotime($row->dob)) }}
                    @endif
                            ( {{$row->gender}})<br />
                
                    {{$row->name}} <br />
                </td>
                <td ><?= date('m/d/Y h:i A', strtotime($row->created_date)); ?><br />
                    {{$row->users->first_name??$row->users->first_name}} {{$row->users->last_name??$row->users->last_name}}
                </td>
            </tr>
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