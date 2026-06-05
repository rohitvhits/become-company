@php
        $i = 0;
    @endphp

<style>
    #order-listing_length,
    #order-listing_paginate,
    #order-listing_info {
        display: none;
    }

    #order-listing_filter {
        text-align: right;
    }

    ..select2-container {
        width: 200px !important;
    }

    .table-head-fix tbody {
        display: block;
        max-height: calc(100vh - 350px);
        overflow-y: scroll;
    }

    .table-head-fix tbody::-webkit-scrollbar {
        width: 0;
        height: 0;
    }

    .table-head-fix thead,
    .table-head-fix tbody tr {
        display: table;
        width: 100%;
    }

    .recordtabletdwidth th:nth-child(1),
    .recordtabletdwidth td:nth-child(1) {
        min-width: 100px;
        max-width: 100px;
        width: 100px;
    }

    .recordtabletdwidth th:nth-child(2),
    .recordtabletdwidth td:nth-child(2) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .recordtabletdwidth th:nth-child(3),
    .recordtabletdwidth td:nth-child(3) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }


    .recordtabletdwidth th:nth-child(4),
    .recordtabletdwidth td:nth-child(4) {
        min-width: 220px;
        max-width: 220px;
        width: 220px;
    }

    .recordtabletdwidth th:nth-child(5),
    .recordtabletdwidth td:nth-child(5) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }

    .recordtabletdwidth th:nth-child(6),
    .recordtabletdwidth td:nth-child(6) {
        min-width: 200px;
        max-width: 200px;
        width: 200px;
    }

    .recordtabletdwidth th:nth-child(7),
    .recordtabletdwidth td:nth-child(7) {
        min-width: 210px;
        max-width: 210px;
        width: 210px;
    }

    .recordtabletdwidth th:nth-child(8),
    .recordtabletdwidth td:nth-child(8) {
        min-width: 176px;
        max-width: 176px;
        width: 176px;
    }

    .recordtabletdwidth th:nth-child(9),
    .recordtabletdwidth td:nth-child(9) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }
    .recordtabletdwidth th:nth-child(10),
    .recordtabletdwidth td:nth-child(10) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }
    .recordtabletdwidth th:nth-child(11),
    .recordtabletdwidth td:nth-child(11) {
        min-width: 250px;
        max-width: 250px;
        width: 250px;
    }
    .recordtabletdwidth th:nth-child(12),
    .recordtabletdwidth td:nth-child(12) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }
    .recordtabletdwidth th:nth-child(13),
    .recordtabletdwidth td:nth-child(13) {
        min-width: 180px;
        max-width: 180px;
        width: 180px;
    }

    
    .recordtabletdwidth td,
    .recordtabletdwidth th {
        white-space: inherit;
    }

   
    .sorting-btn {
        display: flex;
        flex-direction: column;
        margin-left: auto;
    }

    .sorting-div {
        display: flex;
        align-items: center;
    }

    .sorting-btn button {
        padding: 0;
        margin: 0;
        border: 0;
        background: transparent;
        line-height: 0.5;
    }

    .sorting-btn button i {
        line-height: 0.3;
    }

    .order-listing-loader {
        position: absolute;
        left: 0;
        top: 0;
        background: #ffffff94;
        bottom: 0;
        right: 0;
        width: 100%;
        font-size: 30px;
        display: none;
        align-items: center;
        justify-content: center;

    }

</style>
<div class="table-responsive">
    <div class="order-listing-loader">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>

                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Record</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="id" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
               
                    <th style="white-space:nowrap">
                        <div class="sorting-div"><span>Agency Name</span>
                            <div class="sorting-btn">
                                <button type="button" class="record_id" data-field="agency_fk" data-sort="asc"><i
                                        class="fa fa-sort-up"></i> </button><button type="button"
                                    class="record_id" data-field="agency_fk" data-sort="desc"><i
                                        class="fa fa-sort-down"></i> </button>
                            </div>
                        </div>
                    </th>
                    <th style="white-space:nowrap">
                        <div class="sorting-div"><span>Doctor Name</span>
                            <div class="sorting-btn">
                                <button type="button" class="record_id" data-field="agency_fk" data-sort="asc"><i
                                        class="fa fa-sort-up"></i> </button><button type="button"
                                    class="record_id" data-field="agency_fk" data-sort="desc"><i
                                        class="fa fa-sort-down"></i> </button>
                            </div>
                        </div>
                    </th>
                    <th style="white-space:nowrap">
                        <div class="sorting-div"><span>Type</span>
                            <div class="sorting-btn">
                                <button type="button" class="record_id" data-field="agency_fk" data-sort="asc"><i
                                        class="fa fa-sort-up"></i> </button><button type="button"
                                    class="record_id" data-field="agency_fk" data-sort="desc"><i
                                        class="fa fa-sort-down"></i> </button>
                            </div>
                        </div>
                    </th>
                
                <th>
                    <div class="sorting-div"><span>Patient Name</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="name" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="name" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Phone Number</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="email" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="email" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
               <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Date of Birth</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="phone" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="phone" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                        <div class="sorting-div"><span>Location</span>
                            <div class="sorting-btn">
                                <button type="button" class="record_id" data-field="dob" data-sort="asc"><i
                                        class="fa fa-sort-up"></i> </button><button type="button"
                                    class="record_id" data-field="dob" data-sort="desc"><i
                                        class="fa fa-sort-down"></i> </button>
                            </div>
                        </div>
                    </th>
                
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Appointment Date</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="emc" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="emc" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Appointment Time</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="medicaid" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="medicaid" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th> 
                
                
                   <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Service</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="medicaid" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="medicaid" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Status</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="medicaid" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="medicaid" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Created Date</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="medicaid" data-sort="asc"><i
                                    class="fa fa-sort-up"></i> </button><button type="button" class="record_id"
                                data-field="medicaid" data-sort="desc"><i class="fa fa-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </th> 
                

            </tr>
            <form method="get" action="" id="search_form">
                <tr>
                    <td>
                        <button name="button" value="Search" class="btn btn-primary btn-sm btn-rounded" id="search_id">Search</button>
                        
                    </td>
                    <td>
                        <select name="agency_id" id="agency_ids" class="form-control">
                            <option value="">Select Agency</option>
                                @if(!empty($agency_list))
                                    @foreach ($agency_list as $val)

                                        <option value="{{ $val->id}}" @if($agency_name ==$val->id) selected @endif>{{ $val->agency_name}}</option>
                                        
                                    @endforeach
                                @endif
                        </select>
                    </td>
                    <td>
                        <select name="doctor_id" id="doctor_id" class="form-control">
                            <option value="">Select Doctor</option>
                                @if(!empty($doctor_list))
                                    @foreach ($doctor_list as $doc)

                                        <option value="{{ $doc->id}}" @if($doctor_id ==$val->id) selected @endif>{{ $doc->full_name}}</option>
                                        
                                    @endforeach
                                @endif
                        </select>
                    </td>
                    <td>
                        <select name="type" class="form-control" id="type">
                            <option value="">Select Type</option>
                            <option value="Caregiver" @if($type =="Caregiver") selected @endif>Caregiver</option>
                            <option value="Patient"  @if($type =="Caregiver") selected @endif>Patient</option>
                            
                        </select>
                    </td>
                    <td>
                        <input type="text" name="full_name" id="full_name" value="{{ $full_name }}" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="phone_no" id="phone_no" value="{{ $phone_no }}"  class="form-control">
                    </td>
                    <td>
                        <input type="text" name="dob" id="dob" value="{{ $dob }}"  class="datepicker form-control">
                    </td>
                    <td>
                        <select name="locationId" class="form-control" id="locationId">
                            <option value="">Select Location</option>
                            @foreach($location_list as $vsl)
                                <option value="{{ $vsl->id }}" @if($locationId == $vsl->id) selected @endif>{{ $vsl->address1}} {{ $vsl->city}}</option>
                            @endforeach
                            
                        </select>
                    </td>
                    <td>
                        <input type="text" name="appoinment_date" id="appoinment_date" value="{{ $appoinment_date}}" class="datepicker1 form-control">
                    </td>
                    <td>
                        
                    </td>
                    
                    <td>
                       <select class="js-example-basic-multiple w-100" multiple="multiple" name="service_id[]" id="service_id">
											
                            <option value="">Select Service</option>
                                @php 
                                    $final_array =array();
                                    if(!empty($service_id)){
                                        foreach($service_id as $vals){
                                            $final_array[] = $vals;
                                        }
                                    }
                                @endphp
                               
                            @foreach($serviceList as $service)
                            <option value="{{$service->id}}" @if(in_array($service->id,$final_array)) selected @endif>{{ $service->name}}</option>
                            @endforeach
                        </select>

                    </td>
                    <td>
                        
                    
                    </td>
                    <td>
                    <input type="text" name="created_date" value="{{ $datepickernn}}" id="datepickernn"  class="datepickernn form-control" style="width:86px">
                    </td>
                    
                    
                   
                </tr>
            </form>
        </thead>
        <tbody>
            @php
                $i = 1 + ($open_record_list->currentPage() - 1) * $open_record_list->perPage();
            @endphp
            @if (count($open_record_list) > 0)
                @foreach ($open_record_list as $row)
                    <tr>
                        <td><a href="{{ url('/patient/view/')}}/{{ $row->id}}"> #{{$row->id}}</a></td>
                        <td>{{ ucwords($row->agency_name)}}</td>
                        <td>{{ ucfirst($row->full_name)}}</td>
                        <td>{{ ucwords($row->type)}}</td>
                       <td>{{ ucwords($row->first_name)}} {{ ucwords($row->last_name) }}</td>
                        <td>{{ $row->phone }}</td>
                        
                         <td>@if ($row->dob != '0000-00-00') 
                                {{Common::convertMDY($row->dob)}}
                            @endif
                        </td>
                        <td>{{  $row->address1 }} {{ $row->city }}</td>
                       
                       <td>@if ($row->appointment_date != '')
                            {{ Common::convertMDY($row->appointment_date) }}
                            
                            @endif    
                        </td>
                        <td>
                            @if($row->start_time != '' && $row->end_time)

                                {{ date('h:i A', strtotime($row->start_time)) }} {{ date('h:i A', strtotime($row->end_time)) }} 
                            @endif
                           
                        </td>
                        <td>{{ $row->name}}</td>
						<td>
                            @if($row->status == 'Pending')
                                <label class='badge badge-warning'>Pending</label>
                             @endif
                             @if($row->status == 'booked')
                                <label class='badge badge-info'>Booked</label>
                             @endif
                             
                             
                             
                             @if($row->status == 'completed')
                                <label class='badge badge-success'>Completed</label>
                             @endif
                             @if($row->status == 'cancelled')
                                <label class='badge badge-danger'>Cancelled</label>
                             @endif
                        </td>
                        <td>{{ Common::convertMDY($row->created_date)}}</td>
                       
                    </tr>
                   
                @endforeach

            @endif
            @if (count($open_record_list) == 0)
                <tr>
                    <td colspan="12">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $open_record_list->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
<script>
    var totalCount = {{$open_record_list->total()}};
    $(".datepicker").datepicker();
    $(".datepicker1").datepicker();
    
    $(document).ready(function(e){
        
        setTimeout(function(e){ $('#totalCount_id').html(totalCount);},1000);
    });
    
</script>
 <script>

$(function () {
			var start = moment().subtract(0, 'days');
			var end = moment();
			$('.datepickernn').daterangepicker({
				startDate: start,
				endDate: end,
				autoUpdateInput: false,
				startOfWeek: 'sunday',
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
					'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
					'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks').endOf('isoWeek')],
					'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1, 'weeks').endOf('isoWeek')],
				}
			}, function (chosen_date, end_date) {

				$('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
			})
			
});

$('.js-example-basic-multiple').select2();
		</script>