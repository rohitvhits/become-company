@include('include/header')
@include('include/sidebar')
<style>
    .error {
        color: Red;
    }
    .addusertable th:nth-child(1),
    .addusertable td:nth-child(1) {
        min-width: 100px;
        max-width: 100px;
        width: 100px;
    }

    .addusertable th:nth-child(2),
    .addusertable td:nth-child(2) {
        min-width: 200px;
        max-width: 200px;
        width: 200px;
    }

    .addusertable th:nth-child(3),
    .addusertable td:nth-child(3) {
        min-width: 200px;
        max-width: 200px;
        width: 200px;
    }

    .addusertable th:nth-child(4),
    .addusertable td:nth-child(4) {
        min-width: 400px;
        max-width: 400px;
        width: 400px;
    }

    .addusertable th:nth-child(5),
    .addusertable td:nth-child(5) {
        min-width: 150px;
        max-width: 150px;
        width: 150px;
    }

    .addusertable th:nth-child(1),
    .addusertable td:nth-child(1) {
        min-width: 60px;

    }

    .minus-btn {
        max-height: 38px;
    }

    .formcontrol {
        border: 1px solid #cdd4e0;
        font-weight: 400;
        font-size: 0.875rem;
        height: 37px;
        padding: 0.375rem 0.75rem;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">User Add - {{ @$agencyName->agency_name }}</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <form class="form-sample" action='<?php echo URL::to('/agency/add_user'); ?>' name="adduser" method="post"
                        onsubmit="return validation();">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <div class="card-body">
                            <div class="row table-responsive">
                                <table class="table table-bordered orderItems addusertable" id="orderItems">
                                    <thead>
                                        <th>
                                            Action
                                        </th>

                                        <th>
                                            First Name
                                        </th>
                                        <th>
                                            Last Name
                                        </th>
                                        <th>
                                            Email
                                        </th>

                                        <th>
                                            Phone No
                                        </th>
                                        <th>
                                            Ext
                                        </th>
                                        <th>
                                            Permission Type
                                        </th>
                                        <th>
                                            Department
                                        </th>
                                        <th>
                                            Is Admin
                                        </th>
                                    </thead>
                                    <tbody id="add_more_id">
                                        @php
                                            $uniqid = uniqid();
                                        @endphp
                                        <tr id="{{ $uniqid }}">
                                            <td>
                                                <a href="javascript:void(0)"
                                                    class="btn btn-danger removeTr minus-btn ddd"
                                                    dataid="{{ $uniqid }}"><i class="fa fa-minus"></i></a>
                                            </td>

                                            <input type="hidden" name="uid" value="{{ $agencyId->id }}">

                                            <td>
                                                <input type="text" class="form-control"
                                                    placeholder="Enter First Name" id="first_name{{ $uniqid }}"
                                                    data-id="{{ $uniqid }}" name="first_name[]"
                                                    value="{{ old('first_name') }}" maxlength="50">
                                                <span style="white-space: initial" class="error mt-2"
                                                    id="first_name_{{ $uniqid }}_error">{{ $errors->add_user->first('first_name') }}</span>

                                            </td>
                                            <td>
                                                <input type="text" data-id="{{ $uniqid }}" class="form-control"
                                                    placeholder="Enter Last Name" id="last_name{{ $uniqid }}"
                                                    name="last_name[]" value="{{ old('last_name') }}" maxlength="50">
                                                <span style="white-space: initial" class="error mt-2"
                                                    id="last_name_{{ $uniqid }}_error">{{ $errors->add_user->first('last_name') }}</span>
                                            </td>
                                            <td>
                                                <!-- email -->
                                                <div class="d-flex" style="margin-top:21px;">
                                                    <div>
                                                        <input type="text" data-id="{{ $uniqid }}"
                                                            class="form-control" placeholder="Enter Email"
                                                            class="span11 mr-3" id="email{{ $uniqid }}"
                                                            name="email[]" value="{{ old('email') }}" maxlength="50">
                                                        <span style="white-space: initial"
                                                            class="error mt-2"
                                                            id="email_{{ $uniqid }}_error">
                                                            {{ $errors->add_user->first('email') }}</span>
                                                    </div>
                                                    <div class="mr-3">
                                                        @if (request('id') == '' && in_array($user['user_type_fk'], [184, 4]))
                                                            <select id="domain_id_{{ $uniqid }}"
                                                                data-id="{{ $uniqid }}"
                                                                class="form-control ml-3 mr-3" name="domain[]">
                                                                <option value="">Select Domain</option>
                                                            </select>
                                                            <span style="white-space: initial"
                                                                class="error ml-3 mt-2 "
                                                                id="domain_{{ $uniqid }}_error"><?php echo $errors->add_user->first('domain'); ?></span>
                                                        @else
                                                            <select id="domain_id_{{ $uniqid }}"
                                                                data-id="{{ $uniqid }}"
                                                                class="form-control ml-3 mr-3" name="domain[]">
                                                                <option value="">Select Domain</option>
                                                                @if (isset($domainName))
                                                                    @foreach ($domainName as $data)
                                                                        <option value="{{ $data->id }}">
                                                                            {{ '@' . $data->domain }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            <span class="error ml-3 mt-2"
                                                                style="white-space: initial"
                                                                id="domain_{{ $uniqid }}_error"><?php echo $errors->add_user->first('domain'); ?></span>
                                                        @endif
                                                    </div>
                                                    
                                                </div>
                                                <!-- email -->
                                            </td>

                                            <td>
                                                <input type="text" class="form-control" placeholder="Enter Phone No"
                                                    onkeypress="return isNumber(event)" class="span11"
                                                    id="phone{{ $uniqid }}" data-id="{{ $uniqid }}"
                                                    name="phone[]" value="<?php echo old('phone'); ?>">
                                                <span class="error mt-2"
                                                    id="phone_{{ $uniqid }}_error"><?php echo $errors->add_user->first('phone'); ?></span>
                                            </td>

                                            <td>
                                                <input type="text" class="formcontrol" placeholder="Enter Ext"
                                                    onkeypress="return isNumber(event)" class="span11"
                                                    id="ext{{ $uniqid }}" name="ext[]"
                                                    value="<?php echo old('ext'); ?>">
                                                <span class="error mt-2"
                                                    id="ext_{{ $uniqid }}_error"><?php echo $errors->add_user->first('ext'); ?></span>
                                            </td>
                                            <td>
                                                <select class="form-control" id="record_access" name="record_access[]" style="width:200px">
                                                    <option value="All" selected="">All</option>
                                                    <option value="Patient">Patient</option>
                                                    <option value="Caregiver">Caregiver</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="department[]" class="form-control" style="width:200px">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="role_access[]" value="1" class="notification_checkbox patient_checkbox">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Send Invitaion</button>
                            <input type="hidden" name="current_agency_id"
                                value="@if (isset($agencyId->id)) {{ sha1($agencyId->id) }} @endif">
                            <a href="javascript:void(0)" onclick="addNewItemRow()" style="float:right"
                                class="btn btn-primary btn-sm mt-2">Add </a>

                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>

    @include('include/footer')
    <!-- /Main Content -->
    <!-- doamin names -->
    <span id="domain_names" style="display:none">
        @if (isset($domainName))
            @foreach ($domainName as $data)
                <option value="{{ $data->id }}">{{ '@' . $data->domain }}</option>
            @endforeach
        @endif
    </span>
    <!-- domain names -->
    <!-- /Page Content -->
    <span id="login_type" style="display:none">
        @foreach ($loginType as $value)
            <option value="{{ $value->id }}">{{ $value->name }}
            </option>
        @endforeach
    </span>
    <span id="agencyas" style="display:none">
        <option value="">Select Agency</option>
        @foreach ($agencyList as $rwAgency)
            <option value="{{ $rwAgency->id }}">
                {{ $rwAgency->agency_name }}
            </option>
        @endforeach
    </span>
    <script>
        function validation() {
            var temp = 0;
            var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            var user_type_fk = '{{ $user['user_type_fk'] }}';


            $('input[name="first_name[]"]').each(function(e) {
                var dataId = $(this).attr('data-id');
                $('#first_name_' + dataId + '_error').html("");
                if ($(this).val().trim() == '') {
                    $('#first_name_' + dataId + '_error').html("Please enter First Name");
                    temp = 1;
                }
            })
            $('input[name="last_name[]"]').each(function(e) {
                var dataId = $(this).attr('data-id');
                $('#last_name_' + dataId + '_error').html("");
                if ($(this).val().trim() == '') {
                    $('#last_name_' + dataId + '_error').html("Please enter Last Name");
                    temp = 1;
                }
            })

            $('input[name="email[]"]').each(function(e) {
                var dataId = $(this).attr('data-id');
                $('#email_' + dataId + '_error').html("");
                if ($(this).val().trim() == '') {
                    $('#email_' + dataId + '_error').html("Please enter Email");
                    temp = 1;
                } else {
                    if ($(this).val().trim() != '') {
                        if (emailRegex.test($(this).val())) {
                            $('#email_' + dataId + '_error').html("Only for name allowed");
                            temp = 1;
                        }
                    }

                }
            })
            $('select[name="domain[]"]').each(function(e) {
                var dataId = $(this).attr('data-id');
                $('#domain_' + dataId + '_error').html("");
                if ($(this).val() == '') {
                    $('#domain_' + dataId + '_error').html("Please select Domain");
                    temp = 1;
                }
            })

            if (temp == 1) {
                return false;
            } else {
                return true;
            }

        }

        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode != 46 || $(this).val().indexOf('.') != -1) && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
        // $('#show_user_type').hide();
        function getUserType(val, randomId) {
            var id = val;
            $.ajax({
                type: "POST",
                url: "{{ url('getUserType') }}?id=" + id,
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {

                    if (data != '') {

                        $('#user_type' + randomId).html(data);
                    } else {
                        $('#user_type' + randomId).html(
                            '<input disabled type="text" class="form-control" value="Data not found" >');
                    }
                }
            });
        }

        function getDomainByAgencyWise(rid) {
            var agency_id = $('#agency' + rid).val();
            var html = '';
            if (agency_id != '') {
                $.ajax({
                    type: "GET",
                    url: "{{ url('domain-list') }}",
                    data: {
                        'agency_id': agency_id
                    },
                    success: function(res) {
                        var json = res.data;
                        html = '<option value="">Select Domain</option>';
                        if (json.length != 0) {
                            $.each(json, function(i, v) {
                                html += '<option value="' + v.id + '">' + v.domain + '</option>';
                            })
                        } else {

                        }

                        $('#domain_id_' + rid).html("");
                        $('#domain_id_' + rid).html(html);
                    }
                })
            }

        }

        function addNewItemRow() {
            var random = Math.round(Math.random() * 9000000000);
            var login_type = $('#login_type').html();
            var agencyas = $('#agencyas').html();
            var domain_names = $('#domain_names').html();
            var html_resp = '';
            var id = "{{ request('id') }}";
            var user_type_fk = '{{ $user['user_type_fk'] }}';
            html_resp += '<td><a href="javascript:void(0)" class="btn btn-danger removeTr minus-btn" dataid="' + random +
                '"><i class="fa fa-minus"></i></a></td>';

            html_resp += '</select><span class="error mt-2" id="agency_' + random + '_error"></span></td>' +
                '<td><input type="text" class="form-control" placeholder="Enter First Name" id="first_name' + random +
                '" data-id="' + random + '" name="first_name[]" value="" maxlength="50">' +
                '<span class="error mt-2" id="first_name_' + random + '_error"></span></td>' +
                '<td><input type="text" data-id="' + random +
                '" class="form-control" placeholder="Enter Last Name" id="last_name' + random +
                '" name="last_name[]" value="" maxlength="50">' +
                '<span class="error mt-2" id="last_name_' + random + '_error"></span></td>' +
                '<td><div class="d-flex" style="margin-top:21px;"><div><input type="text" data-id="' + random +
                '" class="form-control" placeholder="Enter Email" id="email' + random + '" name="email[]" value="" maxlength="50">' +
                '<span class="error mt-2" id="email_' + random + '_error"></span></div><div class="mr-3">';
            if (id == '' && (user_type_fk == 184 || user_type_fk == 4)) {
                html_resp += '<select id="domain_id_' + random + '" data-id="' + random +
                    '" class="form-control ml-3 mr-3" name="domain[]">' +
                    '<option value="">Select Domain</option>' +
                    '</select><span class="error ml-3 mt-2" id="domain_' + random + '_error"></span></td>';
            } else {
                html_resp += '<select id="domain_id_' + random + '" data-id="' + random +
                    '" class="form-control ml-3 mr-3" name="domain[]">' +
                    '<option value="">Select Domain</option>' + domain_names + '' +
                    '</select><span class="error ml-3 mt-2" id="domain_' + random +
                    '_error"></span></div></div></td>'
            }
            html_resp +=
                '<td><input type="text" class="form-control" placeholder="Enter Phone No" onkeypress="return isNumber(event)" id="phone' +
                random + '" data-id="' + random + '" name="phone[]" value="">' +
                '<span class="error mt-2" id="phone_' + random + '_error"></span></td>' +
                '<td><input type="text" class="form-control" placeholder="Enter Ext" onkeypress="return isNumber(event)" id="ext' +
                random + '" data-id="' + random + '"  name="ext[]" value=""><span class="error mt-2" id="ext_' +
                random + '_error"></span></td><td><select class="form-control" id="record_access" name="record_access[]" style="width:200px"><option value="All" selected="">All</option><option value="Patient">Patient</option><option value="Caregiver">Caregiver</option></select></td><td><input type="text"  name="department[]" class="form-control" style="width:200px"></td><td><input type="checkbox" name="role_access[]" value="1" class="notification_checkbox patient_checkbox"></td>';
            var nrehtmls = '';

            html_resp += nrehtmls;
            $('#add_more_id').append("<tr id='" + random + "'>" + html_resp + "</tr>")
        }

        $('body').on('click', '.removeTr', function(e) {
            var id = $(this).attr('dataid');
            $('#' + id).remove();
        })
    </script>
