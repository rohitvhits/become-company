<div class="">
    <div class="order-listing-loader">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
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
                    <div class="sorting-div"><span>No</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Agency Name</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="emp.agency_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="emp.agency_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Employee Name</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="emp.first_name" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="emp.first_name" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Employee Code</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="emp.emp_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="emp.emp_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Date of Birth</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="emp.birthday" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="emp.birthday" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Employee Phone</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="emp.phone" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="emp.phone" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Skill Name</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="alayacare_employee_skill.skill_name" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="alayacare_employee_skill.skill_name" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Due Date</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="alayacare_employee_skill.due_date" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="alayacare_employee_skill.due_date" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                        </div>
                    </div>
                </th>
                <th style="white-space:nowrap">
                    <div class="sorting-div"><span>Status</span>
                        <div class="sorting-btn">
                            <button type="button" class="record_id" data-field="alayacare_employee_skill.patient_id" data-sort="asc"><i class="fa fa-sort-up"></i> </button><button type="button" class="record_id" data-field="alayacare_employee_skill.patient_id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
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
                    <td style="white-space:nowrap"><input type="button" name="search" class="btn btn-primary btn-fw pull-right btn-sm" id="searchid" value="Search" onclick="dueSkillList(1)"></td>
                    <td style="white-space:nowrap">

                        <select class="form-control" name="agency_fk1" id="agency_fk">
                            <option value="">Select agency</option>
                            @foreach($agencyList as $val)
                            <option value="{{ $val->id}}" @if($agency_fk==$val->id) selected @endif>{{ $val->agency_name}}</option>
                            @endforeach

                        </select>
                    </td>
                    <td>
                        <input type="text" name="full_name" id="full_name" class="form-control"  value="{{$full_name}}">
                    </td>
                    <td>
                        <input type="text" name="code" id="code" class="form-control"  value="{{$code}}">
                    </td>
                    <td></td>
                    <td>
                        <input type="text" name="caregiver_phone" id="caregiver_phone" class="form-control" value="{{$caregiver_phone}}">
                    </td>
                    <td>
                        <input type="text" name="medical_name" id="medical_name" class="form-control" value="{{$skill_name}}">
                    </td>
                    <td>
                        <input type="text" name="due_date" id="due_date" class="form-control datepickernn" autocomplete="off" value="{{$due_date}}">
                    </td>
                    <td>
                        <select class="form-control" name="status" id="status">

                            <option value="">All</option>
                            <option value="Pending" @if($status=="Pending" ) selected @endif>Pending</option>
                            <option value="Booked" @if($status=="Booked" ) selected @endif>Added</option>

                        </select>
                    </td>
                    <td> <input type="button" name="search" class="btn btn-primary btn-fw pull-right btn-sm" id="searchid" value="Search" onclick="dueSkillList(1)"></td>
                </tr>

            </form>
        </thead>
        <tbody>
            @php
            $i = 1 + ($list->currentPage() - 1) * $list->perPage();
            @endphp
            @if (count($list) > 0)
            @foreach ($list as $row)
            <tr>
                <td>
                    @if($row->patient_id !='')

                    @else
                    <input type="checkbox" name="cbox" class="cbox" value="{{ $row->id}}">
                    @endif
                </td>
                <td>{{ $i++}}</td>
                 <td>{{ $row->agency_name}}</td>
                 <td>{{ $row->first_name}} {{ $row->last_name}}</td>
                <td>{{ $row->employee_id}}</td>
                <td>{{ Common::convertMDY($row->birthday)}}</td>
                <td>{{ $row->phone}}</td>
                <td>{{ $row->skill_name}}</td>
                <td>{{ Common::convertMDYTime($row->due_date)}}</td>
                <td>
                @if($row->patient_id !='')
                    <a target="_blank" href="{{ url('patient/view')}}/{{ $row->patient_id }}"><span class="badge badge-success">Added</span></a>
                    @else
                    <span class="badge badge-primary">Pending</span>
                    @endif
                </td>
                <td> @if($row->patient_id =='')
                     @can('add-appointment-alayacare-due-skill')
                    <a href="javascript:void(0)" onclick="singleDataAppointment('{{ $row->id}}')"><i class="fa fa-plus"></i> Add Appointment</a>
                    @endcan
                    @else
                    <a href="{{ url('patient/view')}}/{{ $row->patient_id }}" ><i class="fa fa-eye"></i> View</a>
                    @endif
                </td>
            </tr>
            @endforeach
            @endif
            @if (count($list) == 0)
                <tr>
                    <td colspan="12">No record available</td>
                </tr>
            @endif

        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $list->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>

<script>
    $('#appointment_id').html("{{$list->total()}}");

</script>