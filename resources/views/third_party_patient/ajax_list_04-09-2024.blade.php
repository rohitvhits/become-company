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
                    <!-- <div class="sorting-div"><span><input type="checkbox" id="cboxid"></span>
                        <div class="sorting-btn">

                        </div>
                    </div> -->
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>No</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Agency Name</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="agency_name" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="agency_name" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Patient ID</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="patientId" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="patientId" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Requested ID</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="requested_service_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="requested_service_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Full Name</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="firstName" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="firstName" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Date of Birth</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="dob" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="dob" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Gender</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="gender" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="gender" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Service Name</span>
                        <div class="sorting-btn">
                            <!-- <button type="button" class="record_id" data-field="appointment_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="appointment_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button> -->
                        </div>
                    </div>
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Patient Status</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="status" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="status" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Appointment Status</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="appointment_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="appointment_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>API Partner</span>
                        <div class="sorting-btn">
                            <!-- <button type="button" class="record_id" data-field="appointment_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="appointment_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button> -->
                        </div>
                    </div>
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>API Name</span>
                        <div class="sorting-btn">
                            <!-- <button type="button" class="record_id" data-field="appointment_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="appointment_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button> -->
                        </div>
                    </div>
                </th>



                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Created Date</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="created_date" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="created_date" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Action</span>
                        <div class="sorting-btn">

                        </div>
                    </div>
                </th>

            </tr>
            <form method="get" action="">
                <tr>

                    <td></td>
                    <td style="white-space:nowrap">
                        <input type="button" name="search" class="btn btn-secondary btn-fw pull-right btn-sm " id="clear" value="Reset" onclick="resetVisitingAidList()">
                        <input type="button" name="search" class="btn btn-primary btn-fw pull-right btn-sm mr-3" id="searchid" value="Search" onclick="visitingAidList(1)">

                    </td>
                    <td style="white-space:nowrap">
                        <select name="agency_id" class="form-control" id="agency_id">
                            <option value="">Select Agency</option>
                            @foreach($agencyList as $val)
                            <option value="{{ $val->id}}" <?php if (isset($searchData['agency_id']) && $searchData['agency_id'] == $val->id) { ?>selected<?php } ?>>{{ $val->agency_name}}</option>
                            @endforeach
                        </select>
                    </td>

                    <td style="white-space:nowrap"></td>
                    <td style="white-space:nowrap"></td>
                    <td style="white-space:nowrap">

                        <input type="text" name="full_name" id="full_name" class="form-control" value="{{ $searchData['full_name'] ?? ''}}">
                    </td>
                    <td style="white-space:nowrap">

                        <input type="text" name="code" id="dob" class="form-control dob" value="{{ $searchData['dob'] ?? ''}}">
                    </td>
                    <td style="white-space:nowrap">

                        <select name="gender" class="form-control" id="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" @if(isset($searchData['gender']) && $searchData['gender']=='Male' ) selected @endif>Male</option>
                            <option value="Female" @if(isset($searchData['gender']) && $searchData['gender']=='Female' ) selected @endif>Female</option>
                        </select>
                    </td>
                    <td></td>
                    <td style="white-space:nowrap">

                        <select name="gender" class="form-control" id="patient_status">
                            <option value="">Select Patient Status</option>
                            <option value="Pending" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Pending' ) selected @endif>Pending</option>
                            <option value="cancelled" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='cancelled' ) selected @endif>Cancelled</option>
                            <option value="booked" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='booked' ) selected @endif>Booked</option>
                            <option value="completed" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='completed' ) selected @endif>Completed</option>
                            <option value="noshow" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='noshow' ) selected @endif>No Show</option>
                            <option value="arrived" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='arrived' ) selected @endif>Arrived</option>
                            <option value="processing" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='processing' ) selected @endif>Processing</option>
                            <option value="Not interested" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Not interested' ) selected @endif>Not Interested</option>
                            <option value="hospitalized/rehab" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='hospitalized/rehab' ) selected @endif>Hospitalized/Rehab</option>
                            <option value="unableToContact" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='unableToContact' ) selected @endif>Unable To Contact</option>
                            <option value="refused" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='refused' ) selected @endif>Refused</option>
                            <option value="checkin" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='checkin' ) selected @endif>Mark As Clockin</option>
                            <option value="PendingTermination" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='PendingTermination' ) selected @endif>Pending Terminated</option>
                            <option value="Onhold" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Onhold' ) selected @endif>On Hold</option>
                            <option value="Onleave" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Onleave' ) selected @endif>On Leave</option>
                            <option value="Terminated" @if(isset($searchData['patient_status']) && $searchData['patient_status']=='Terminated' ) selected @endif>Terminated</option>
                        </select>
                    </td>

                    <td style="white-space:nowrap">
                        <select class="form-control" name="status" id="status">

                            <option value="">All</option>
                            <option value="Pending" @if(isset($searchData['status']) && $searchData['status']=='Pending' ) selected @endif>Pending</option>
                            <option value="Booked" @if(isset($searchData['status']) && $searchData['status']=='Booked' ) selected @endif>Added</option>

                        </select>

                    </td>
                    <td></td>
                    <td></td>

                    <td style="white-space:nowrap">
                        <input type="text" name="due_date" id="due_date" class="form-control datepickernn" autocomplete="off" value="{{ $searchData['created_date'] ?? '' }}">
                    </td>


                    <td>
                        <input type="button" name="search" class="btn btn-secondary btn-fw pull-right btn-sm " id="clear" value="Reset" onclick="resetVisitingAidList()">
                        <input type="button" name="search" class="btn btn-primary btn-fw pull-right btn-sm mr-3" id="searchid" value="Search" onclick="visitingAidList(1)">

                    </td>

                </tr>
            </form>
        </thead>
        <tbody>

            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            <input type="hidden" id="{{ $row->id}}" value="{{ $row->agency_id}}">
            <tr>
                <td>
                    @if($row->patient_id !='')

                    @else
                    <input type="checkbox" name="cbox" id="{{ $row->id}}" class="cbox" value="{{ $row->id}}">
                    @endif
                </td>
                <td>{{ $i++}}</td>
                <td>{{ $row->agencyDetails->agency_name}}</td>
                <td style="text-align:center">
                    @if($row->patient_id !="")
                        <a href="{{ url('patient/view')}}/{{ $row->id}}" target="_blank">{{ $row->patient_id}}</a>
                    @endif

                    <a href="javascript:void(0)" onclick="linkPatient('{{ $row->id}}','{{ $row->agency_id}}')" title="Link Patient" class="@if($row->patient_id !='') hide @endif"><i class="fa fa-user"></i></a>
                </td>
                <td>
                    {{ $row->requested_service_id}}
                    @if($row->patient_id !='' && $row->requested_service_id =='')
                    <input class="btn btn-primary btn-fw btn-sm mr-3" onclick="linkServiceRequest('{{ $row->id}}','{{ $row->patient_id}}')" class="@if($row->patient_id =='' || $row->requested_service_id !='') hide @endif" value="Link Services">
                    @endif
                </td>

                <td>{{ $row->first_name}} {{ $row->last_name}}</td>
                <td>{{ date('m/d/Y',strtotime($row->dob))}}</td>
                <td>{{ $row->gender}}</td>
                <td>{{ $row->serviceName }}</td>
                <td>
                    @if($row->status =='arrived')
                    <span class="badge badge-secondary">Arrived</span>
                    @elseif($row->status =='booked')
                    <span class="badge badge-info">Booked</span>
                    @elseif($row->status =='cancelled')
                    <span class="badge badge-secondary">Cancelled</span>
                    @elseif($row->status =='completed')
                    <span class="badge badge-success">Completed</span>

                    @elseif($row->status =='hospitalized/rehab')
                    <span class="badge badge-defualt">hospitalized/rehab</span>
                    @elseif($row->status =='In Service')
                    <span class="badge badge-primary">In Service</span>
                    @elseif($row->status =='noshow')
                    <span class="badge badge-pink">noshow</span>

                    @elseif($row->status =='Not interested')
                    <span class="badge badge-secondary">Not interested</span>
                    @elseif($row->status =='On Hold')
                    <span class="badge badge-secondary">On Hold</span>
                    @elseif($row->status =='On Leave')
                    <span class="badge badge-info">On Leave</span>
                    @elseif($row->status =='Pending Termination')
                    <span class="badge badge-danger">Pending Termination</span>
                    @elseif($row->status =='processing')
                    <span class="badge badge-secondary">Processing</span>
                    @elseif($row->status =='refused')
                    <span class="badge badge-light">Refused</span>
                    @elseif($row->status =='Terminated')
                    <span class="badge badge-danger">Terminated</span>

                    @elseif($row->status =='unableToContact')
                    <span class="badge badge-danger">Unable To Contact</span>


                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif
                </td>

                <td>
                    @if($row->patient_id !='')
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->patient_id }}"><span class="badge badge-success">Added</span></a>
                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif

                </td>
                <td>{{ $row->partner_agency}}</td>
                <td>@if(isset($row->agencyGenerateDetails) && $row->agencyGenerateDetails->notes !=""){{ $row->agencyGenerateDetails->notes}} @endif</td>

                <td>{{ date('m/d/Y h:i A',strtotime($row->created_date))}}</td>


                <td>

                    @if(isset($_GET['debug']) && $_GET['debug'] ==1)
                    <a href="javascript:void(0)" onclick="addAppointment('{{ $row->id}}','single')" data-id="{{ $row->agency_id}}"><i class="fa fa-plus"></i> Add Appointment</a>
                    @endif
                    @if($row->patient_id =='')
                    @can('third-party-patient-add')
                    <a href="javascript:void(0)" onclick="addAppointment('{{ $row->id}}','single','{{ $row->agency_id}}')" data-id="{{ $row->agency_id}}" title="Add Appointment"><i class="fa fa-calendar"></i></a>
                    @endcan
                    @else
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->patient_id }}"><i class="fa fa-eye"></i></a>
                    @endif
                    @if($row->patient_id =='')

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