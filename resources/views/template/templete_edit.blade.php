@include('include/header')

@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<style>
    .error {
        color: red;
    }
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .radioClass{
        margin-left:60px;
    }

    .hide{
        display:none
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Template Edit</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form method='post' action='<?php echo URL::to('/updateTemplate'); ?>' name="addPhysician" role="form"
                            id="addPhysician" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="id" value="<?php echo $edit_template->id; ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="FirstName">Template Name <span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="FirstName"
                                                name="template_name" value="<?php echo $edit_template->template_name; ?>">
                                            <span style="color:red;" id="tempname_error"><?php echo $errors->template->first('template_name'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="LastName">Document type <span
                                                class="error">*</span></label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="document_type" id="document_id">
                                                <option value="">Select Document Type</option>
                                                <?php if (isset($document_list) && $document_list) {
                          foreach ($document_list as $val) { ?>
                                                <option value="<?php echo $val->id; ?>" <?php if (isset($edit_template->document_type) && $edit_template->document_type != '') {
                                                    if ($edit_template->document_type == $val->id) {
                                                        echo "selected='selected'";
                                                    }
                                                } ?>>
                                                    <?php echo $val->name; ?></option>
                                                <?php }
                        } ?>
                                            </select>
                                            <span style="color:red;" id="document_error"><?php echo $errors->template->first('document_type'); ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6 @if(strtolower($edit_template->lookup_fields) == 'caregiver') @else hide @endif" id="template_type_div">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="template_type">Template Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="template_type" id="template_type">
                                                <option value="">Select Template Type</option>
                                                <option value="location" @if(isset($edit_template->template_type) && $edit_template->template_type == 'location') selected @endif>Location</option>
                                                <option value="telehealth" @if(isset($edit_template->template_type) && $edit_template->template_type == 'telehealth') selected @endif>Telehealth</option>
                                            </select>
                                            <span style="color:red;" id="template_type_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="Phone">Upload Document</label>
                                        <div class="col-sm-9">
                                            <div class="m-dropzone dropzone" id="m-dropzone-one">
                                                <div class="m-dropzone__msg dz-message needsclick">
                                                    <h3 class="m-dropzone__msg-title">
                                                        Drop files here or click to upload.
                                                    </h3>

                                                </div>
                                            </div>
                                            <span style="color:red;" id="updocument_error"><?php echo $errors->add_physician->first('Phone'); ?></span>
                                        </div>
                                        <div id="appointment-form">
                                            <input name="attached_files" id="docufile_imp" type="hidden"
                                                value="<?php echo $edit_template->upload_document; ?>" />
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="Phone">Description</label>
                                        <div class="col-sm-9">
                                            <textarea name="remark" class="form-control" style="height:150px;"><?php echo $edit_template->remark; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="Phone">Agency</label>
                                    <div class="col-sm-9">
                                    <select class="form-control js-example-basic-multiple" multiple name="agency_id[]" id="agency_id">
                                            <option value="" disabled>Select Agency</option>
                                            <?php if (isset($agency_list) && $agency_list) {
                                                foreach ($agency_list as $val) { ?>
                                                    <option value="<?php echo $val->id; ?>"  @if(in_array($val->id,explode(',',$edit_template->agency_id))) selected @endif><?php echo $val->agency_name; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="FirstName">Email Notification (Email with separeted commas)</label>
                                <div class="col-sm-9">
                                <textarea class="form-control" id="emailid"  name="email_notification"><?php echo $edit_template->email_notification; ?></textarea>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6 @if(strtolower($edit_template->lookup_fields) =='patient') @else hide @endif" id="resolution_update_id">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="FirstName">Resolution Update</label>
                                        <div class="col-sm-9 mt-2">
                                            <input type="checkbox" name="resolution_update" value="Y" @if($edit_template->resolution_update =='Y') checked @endif> Yes
                                        </div>
                                    </div>
                                </div>
                        </div>
                       
                       
                        <div class="row">
                            <div class="col-md-6  @if(strtolower($edit_template->lookup_fields) =='patient') @else hide @endif"  id="esign_workflow_div">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="esign_workflow">E-Sign Workflow</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="esign_workflow" id="esign_workflow">
                                            <option value="normal" @if($edit_template->esign_workflow == 'normal') selected @endif>Normal</option>
                                            <option value="form_complete" @if($edit_template->esign_workflow == 'form_complete') selected @endif>Form Complete</option>
                                            <option value="form_complete_with_sign" @if($edit_template->esign_workflow == 'form_complete_with_sign') selected @endif>Form Completed with Sign</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label" for="LastName">E-signature <span class="error">*</span></label>
                                            <div class="col-sm-6 mt-2">
                                            <input type="radio" name="esign" value="1" <?php if($edit_template->esign ==1){ echo "checked='checked'";}?>> Yes
                                            <input class="radioClass" type="radio" name="esign" value="0" <?php if($edit_template->esign ==0){ echo "checked='checked'";}?>> No
                                            <span style="color:red;" id="vesign_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label" for="LastName">Checkbox Mark</label>
                                            <div class="col-sm-6 mt-2">
                                                <input type="checkbox" name="checkbox_mark_flag" value="1" <?php if($edit_template->checkbox_mark_flag ==1){ echo "checked='checked'";}?>> Yes
                                                    <p class="text-muted">If the checkbox is checked, draw a square mark (☐); otherwise, draw a check mark (✓)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label" for="LastName">Show Verification</label>
                                            <div class="col-sm-6 mt-2">
                                                <input type="checkbox" name="show_verify_by" value="Y" @if($edit_template->show_verify_by =="Y") checked @endif> Yes

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-form-label" for="LastName">Send Caregiver Email</label>
                                            <div class="col-sm-6 mt-2">
                                                <input type="checkbox" name="send_caregiver_email" value="Y" @if($edit_template->send_caregiver_email =="Y") checked @endif> Yes
                                                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                    
                            </div>
                        </div><hr>
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="LastName">VNS Form</label>
                                        <div class="col-sm-9 mt-2">
                                            <input type="checkbox" name="custom_template" value="1" <?php if($edit_template->custom_template =="0"){ echo "checked='checked'";}?>> Yes
                                            
                                        </div>
                                        <ul style="margin-left:10px">
                                            <li class="text-muted">
                                                The VNS dropdown will appear on the detail page based on the selected Agency’s portal.
                                            </li>
                                            <li class="text-muted">
                                                When the VNS Form is enabled, filling in other Template details is not mandatory.
                                            </li>
                                            <li class="text-muted">
                                                Users will not be able to add Signers or perform any Actions such as Signature, Stamp, or Text.
                                            </li>
                                            <li class="text-muted">
                                                The template will not be displayed in the E-Sign section and will only be available in the VNS Form dropdown.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                        <button type="submit" class="btn btn-primary mr-2">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    <!-- /Page Content -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <link href="{{ asset('assets/vendors/dropzone/dropzone.css')}}" rel="stylesheet"
        type="text/css" />
    <script src="{{ asset('assets/vendors/dropzone/dropzone.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
     <script src="{{ asset('assets/js/select2.js')}}"></script>

    <script type="text/javascript">
        var token = "{{ csrf_token() }}";
        $("div#m-dropzone-one").dropzone({
            maxFiles: 1,
            url: "<?php echo URL::to('/'); ?>/template/uploadFiles?_token=" + token,
            addRemoveLinks: true,
            acceptedFiles: '.pdf',
            success: function(file, response) {

                file.previewElement.classList.add("dz-success");
                console.log(response);
                if (response != false) {
                    var currentValue = $("#appointment-form input[name='attached_files']").val();

                    $("#appointment-form input[name='attached_files']").val(response);

                }
            },
            maxfilesexceeded: function(file) {
                this.removeAllFiles();
                this.addFile(file);
            },
            init: function() {

                var thisDropzone = this;
                var mockFile = {
                    name: '<?php if ($edit_template->upload_document != '') {
                        echo $edit_template->upload_document;
                    } else {
                        echo 'no-images.png';
                    } ?>',
                    size: '3000'
                };

                thisDropzone.options.addedfile.call(thisDropzone, mockFile);

                thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                    "<?php echo URL::to('/'); ?>/dosusinguploads/docusign/<?php echo $edit_template->upload_document; ?>"
                    ); //uploadsfolder is the folder where you have all those uploaded files
                thisDropzone.options.complete.call(thisDropzone, mockFile);

                this.on('error', function(file, response) {

                });
            },

        });

        $('#addPhysician').submit(function(e) {
            var FirstName = $('#FirstName').val();
            var document_id = $('#document_id').val();
            var docufile_imp = $('#docufile_imp').val();
            var custom_template = $("input[name='custom_template']:checked").val();
            var cnt = 0;
            $('#updocument_error').html(" ");
            $('#tempname_error').html(" ");
            $('#document_error').html(" ");
            $('#lookup_error').html(" ");
            $('#vesign_error').html(" ");
            $('#template_type_error').html(" ");
            if (FirstName == '') {
                $('#tempname_error').html(" Required ! ");
                cnt = 1;
            }
            if (document_id == '') {
                $('#document_error').html(" Required ! ");
                cnt = 1;
            }

            if(custom_template !=1){
                if (docufile_imp == '') {
                  $('#updocument_error').html(" Required ! ");
                  cnt = 1;
                }
            }

            // Template Type required only when Caregiver is selected
            if (!$('#template_type_div').hasClass('hide')) {
                // var template_type = $('#template_type').val();
                // if (template_type == '') {
                //     $('#template_type_error').html(" Required ");
                //     cnt = 1;
                // }
            }

            if (cnt == 1) {
                return false;
            } else {
                return true;
            }
        });

        function showRelution(){
            var lookupFields = $('input[name="lookup_field"]:checked').val();
            $('#resolution_update_id').addClass('hide');
            $('#template_type_div').addClass('hide');
            $('input[name="resolution_update"]').prop("checked",false);
            $('#template_type').val('');
            $('#template_type_error').html('');
            if(lookupFields =='patient'){
                $('#resolution_update_id').removeClass('hide');
            }
            if(lookupFields =='caregiver'){
                $('#template_type_div').removeClass('hide');
            }
        }
    </script>

    <!-- End Date Picker -->
    @include('include/footer')
