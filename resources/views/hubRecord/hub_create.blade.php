@include('include/header')

@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('/assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
    rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/token-input.css')}}" type="text/css" />
<style>
    .error {
        color: Red;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }


</style>
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<div class="main-panel">
    
    <div class="content-wrapper">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Create New / Search For Existing</h5>
        </div>
        <div class="col-12 grid-margin-top">
            @if (Session::has('error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif
        </div>
        <div class="row">

            <div class="col-12 grid-margin">
                <div class="card">
                    <form class="form-sample" action='<?php echo URL::to('/update-remaining-hub-details'); ?>' name="adduser" method="post" id="main_form_submit_id">
                        <div class="card-body">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                           

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                      

                                        <?php if ($user->agency_fk == '') {
                                            $flag = 0;
                                        ?>
                                            <div class="col-md-8">
                                                <div class="form-group ">
                                                    <label for="">Company Name<span style="color:red">*</span></label>
                                                    <div>
                                                        <select name="agency_id" class="form-control" id="agency_ids">
                                                            <option value="">Select Company Name</option>
                                                            @if(count($agencyList) > 0)
                                                            @foreach($agencyList as $vsl)
                                                            @php
                                                            $flag = 0;

                                                            @endphp
                                                            @if($vsl->app_name !="")
                                                            @php
                                                            $flag = 1;
                                                            @endphp
                                                            @endif
                                                            <option data-app-name="{{ $flag}}" value="{{ $vsl->id}}" @if(old('agency_id')==$vsl->id) selected @endif>
                                                                {{ $vsl->agency_name}}
                                                            </option>
                                                            @endforeach
                                                            @endif

                                                        </select>

                                                        <span class="error mt-2" id="agency_error" for="file_name"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else {
                                            $flag = 0;
                                            $finalArray = [];
                                            foreach ($agencyList as $vsl) {
                                                $tempArray = [];
                                                if ($vsl->id == $user->agency_fk) {
                                                    $tempArray['id'] = $vsl->id;
                                                    $tempArray['agency_name'] = $vsl->agency_name;
                                                    $tempArray['app_name'] = $vsl->app_name;
                                                    $finalArray[] = $tempArray;
                                                    if ($vsl->app_name != "") {

                                                        $flag = 1;
                                                    }
                                                }
                                            }

                                            $result = array_merge($finalArray, $userAgencyList);

                                        ?>

                                            @if(!empty($result[0]))
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="">Company Name <span style="color:red">*</span></label>
                                                    <div>
                                                        @if(!empty($result[0]))
                                                        <select name="agency_id" class="form-control" id="agency_ids">
                                                            <option value="">Select Company Name</option>
                                                            @foreach($result as $agn)
                                                            <option value="{{$agn['id']}}" data-app-name="@if($agn['app_name'] !='') 1 @else '' @endif">{{$agn['agency_name']}}</option>
                                                            @endforeach
                                                        </select>

                                                        @else
                                                        <input type="hidden" name="agency_id" value="<?php echo $user->agency_fk; ?>">
                                                        @endif
                                                        <span class="error mt-2" id="agency_error" for="file_name"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                        <?php } ?>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-8">
                                        <label class="">Seach from Existing Record<span style="color:red">*</span> <a id="click_event" href="javascript:void(0)" data-toggle="modal" title="Add New" onclick="checkAgency()" ><i class="fa fa-plus-circle"></i> Add new</a></label>
                                            <div class="form-group">
                                        
                                                <input type="text" class="form-control search_patient" name="search_patient" id="search_patient" placeholder="Search By (First Name,Last Name,Mobile No,Gender,SSN)">
                                                <span class="search_patient_error error"></span>
                                                
                                            </div>
                                            <span class="notes_existing_class">
                                            <b >Notes:</b><p class="text-mute">Search Record (First Name,Last Name,Mobile No,Gender,SSN)</p>
                                            </span>
                                        </div>
                                           
                                    </div>
                                   
                                    
                                    <div class="row">    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hire Date<span class="error mt-2">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Hire Date" id="create_hire_date" name="create_hire_date" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="" maxlength="50">
                                                    <span id="create_hire_date_error" class="error mt-2"><?php echo $errors->add_agency->first('create_hire_date'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Work Contact<span class="error mt-2">*</span></label>
                                                <div>
                                                <input type="text" class="form-control  form-control-sm" placeholder="Enter Work Contact" id="create_work_contact" name="create_work_contact" data-inputmask-alias="(999) 999-9999" im-insert="true" value="">
                                                    <span id="create_work_contact_error" class="error mt-2"><?php echo $errors->add_agency->first('create_work_contact'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Work Email<span class="error mt-2">*</span></label>
                                                <div>
                                                <input type="text" class="form-control form-control-sm" placeholder="Enter Work Email" id="create_work_email" name="create_work_email" value="" data-gtm-form-interact-field-id="2">
                                                    <span id="create_work_email_error" class="error mt-2"><?php echo $errors->add_agency->first('create_work_email'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Last Work Date</label>
                                                <div>
                                                <input type="text" class="form-control charCls form-control-sm" placeholder="Enter Last Work Date" id="create_last_worked_date" name="create_last_worked_date" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="mm/dd/yyyy" im-insert="false" value="">
                                                    <span id="create_last_worked_date_error" class="error mt-2"><?php echo $errors->add_agency->first('create_last_worked_date'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="row table-responsive"  id="show_demographic-detail">

                                        
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary mr-2" id="insertButton">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('hubRecord/modal/hub_record_add')
    <!-- /Main Content -->

    <!-- /Page Content -->
    <script src="<?php echo URL::to('/'); ?>/assets/js/jquery.tokeninput.js"></script>
   <script>
        var _SAVE_HUB_DETAILS = "{{ url('hub-record/save/')}}";
        var _GET_COUNTRY_CODE  ="{{ url('get-county')}}";
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var _REDIRECTION_URL = '{{ url("/hub-record") }}';
        var _FLAG="add";
    </script>
   
    <script type="text/javascript" src="{{ asset('assets/modulejs/hub_record/hub_record.js')}}?time={{ time()}}"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/vendors/select2/select2.min.js"></script>
    <script src="<?php echo URL::to('/'); ?>/assets/js/select2.js"></script>
 
    <script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/daterangepicker.min.js')}}"></script>
    <script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
    
   <script>
$(":input").inputmask();  


function checkAgency(){
    
    var agency_ids = $('#agency_ids').val();
    $("#agency_error").html("");
    $('#agency_hha_enabled').addClass('hide');
    if(agency_ids ==""){
        $("#agency_error").html("Please select Company Name");
        return false
    }else{
        $('#patient_agency_id').val(agency_ids)
        $("#agency_error").html("");
        var hha=$('#agency_ids option:selected').attr('data-app-name');
       
        if(hha =='1'){
            $('#agency_hha_enabled').removeClass('hide');
        }
        $('#click_event').attr('data-target','#hub_add_modal')
        $('#agency_id').val(agency_ids)
      
    }
$('#total_search_appointment').html(0)
   
}
function openCreateModel(){
    $('#add_new_hub')[0].reset();
    $('#hubModal').modal('show');
    $('#agency_name_error').html("");
    $('#last_name_error').html("");
    $('#phone_error').html("");
    $('#mobile_error').html("");
    $('#dob_error').html("");
    $('#radio_type_error').html("");
    $('#email_error').html("");
    $('#other_name_error').html("");
    $('#address2_error').html("");
}



    $('#main_form_submit_id').submit(function(e){
        var temp = 0;
       
        var search_patient = $('#search_patient').val();
        var agency_ids = $('#agency_ids').val();
        var create_hire_date = $('#create_hire_date').val();
        var create_work_contact = $('#create_work_contact').val();
        var create_work_email = $('#create_work_email').val();

        $("#agency_error").html("");
        $("#create_hire_date_error").html("");
        $("#create_work_contact_error").html("");
        $("#create_work_email_error").html("");
       

        if(search_patient ==""){
            $('.search_patient_error').html("Please select Existing Record");
            temp++;
        }
        if(agency_ids ==""){
            $('#agency_error').html("Please select Company Name");
            temp++;
        }
        
        if(create_hire_date ==""){
            $('#create_hire_date_error').html("Please enter Hire Date");
            temp++;
        }

        if(create_work_contact ==""){
            $('#create_work_contact_error').html("Please enter Work Contact");
            temp++;
        }

        if(create_work_email ==""){
            $('#create_work_email_error').html("Please enter Work Email");
            temp++;
        }


        if (temp == 0) {
            $("#insertButton").prop('disabled', true);
            return true;
        } else {
            return false;
        }
    })

    
$('#agency_ids').change(function(e) {
  
    jQuery.noConflict()
    $('.search_patient').tokenInput('destroy');
    if($('#agency_ids').val() !=""){
        $(".search_patient").tokenInput("{{ url('search-hub-record')}}?agency_id=" + $('#agency_ids').val(), {
            onAdd: function(index, val, type) {
            
            },
        
            tokenLimit: 1,
            onReady: function() {
                    setTimeout(function () {
                        $(".token-input-dropdown").css({
                            "max-height": "180px",
                            "overflow-y": "auto"
                        });
                    }, 500);
                }
        });
    }
});

</script>
    @include('include/footer')