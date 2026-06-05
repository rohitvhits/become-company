<style>
    .notes_content {
        width: 100%;
    }
</style>
<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">Notes 

            <div class="pull-right" >
                <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag1" style="display: none; ">
                @if($user['user_type_fk'] == 184 )

                <input type="radio" class="mb-3" value="Agency" checked='checked' name="radio1" onclick="getClickAble('Agency');">Agency

                <input type="radio" class="mb-3" value="Self"  name="radio1" onclick="getClickAble('Self');"> Only NYBest

                @else
                <input type="radio" class="mb-3" value="Agency" name="radio1" onclick="getClickAble('Agency');" checked='checked'>Agency
                <input type="hidden" class="mb-3"value="Normal" name="radioType">
                @endif
                <br>

                @if($user['user_type_fk'] == 184 )
                <input type="radio" class="mb-3" value="Call" name="radioType" onclick="getClickAbleNew('Call');">Call
                <input type="radio" class="mb-3" value="Normal" checked='checked' name="radioType" onclick="getClickAbleNew('Normal');"> Normal
                @endif


            </div>
        </h4>

    </div>

    <div class="card-body">
        <div class="chat-messages" id="sms-messages">
            <div id="chat-messages-inner" class="notes-messages"></div>
        </div>
    </div>
    <div class="card-footer " style="justify-content:left !important">

        <div class="col-md-12 chat-message  custom-chat">
            <form id="attachsubmits" method="post" onsubmit="return false;">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                @if($notesFlag ==1)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-check-flat form-check-primary">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="notes_message_id" value="1" id="notes_message_id" onclick="getNotesHHACaregiverSubject()">
                            
                        Send Notes To HHA
                        <i class="input-helper"></i></label>
                        </div>
                    </div>
                    
                </div>
                @endif
                <div class="row hide" id="send_hha_subject_id">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleFormControlSelect2">Subject</label>
                            <select class="form-control" id="subjectNotesId" name="subjectNotesId">
                                <option value="">Select Subject</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="exampleFormControlSelect2">Message</label>
                            <span class="input-box notes_content">
                                <p style="margin-bottom: 0 !important; width: 100%;height:100px" name="msg-box" id="text-sms-box" class="tribute-demo-input form-control mt-2 text-share"></p>
                                <div id="suggestions-container"></div>
                                <input type="hidden" name="selectedEmail" id="selectedEmail">
                                <?php
                                $types = "Agency";
                                $radioType = "Normal";
                                if ($user['user_type_fk'] == 184) {
                                    $types = "";
                                    $radioType = "";
                                }
                                ?>
                                <input type="hidden" name="agency_id" id="user_agency_id" value="{{ $types}}">
                                <input type="hidden" name="agency_id_main" value="<?php echo $record->agency_id; ?>">
                                <input type="hidden" name="notes_sendType" value="<?php
                                                                                    if ($user->user_type_fk == 184) {
                                                                                        echo 'Hospital';
                                                                                    } else {
                                                                                        echo 'Agency';
                                                                                    }
                                                                                    ?>">
                            </span>
                        <span id="notes_error_msg" class="text-danger"></span>
                        </div>
                    </div>
                    
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                    <button class="btn btn-success btn-sm" id="text-sms-send-btn" onclick="sendMessagefile()"><i id="send_message_loader" class="circle-loader hide" style="margin-right:25px"></i> Send</button>
                    </div>
                    
                </div>

            </form>
        </div>


    </div>
</div>