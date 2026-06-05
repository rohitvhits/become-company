<style>
    .notes_content {
        width: 100%;
    }
    .vertical_line{
        width:2px;
        height:25px;
        background-color:#d9d7d7;
    }
</style>
<div class="">
    <div class="header">
        <label for="notes">Notes</label>
        <div class="radio-group">
            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag1" style="display: none; ">
            @if($user['user_type_fk'] == 184 )
                <label>
                    <input type="radio" value="Agency" checked='checked' name="radio1" onclick="getClickAble('Agency');">Agency
                </label>
                <label>
                    <input type="radio" value="Self"  name="radio1" onclick="getClickAble('Self');"> Only NYBest
                </label>
            @else
                <label>
                    <input type="radio" value="Agency" name="radio1" onclick="getClickAble('Agency');" checked='checked'>Agency
                </label>
                <input type="hidden" value="Normal" name="radioType">
            @endif
            <div class="vertical_line"></div>
            @if($user['user_type_fk'] == 184 )
                <label><input type="radio" value="Call" name="radioType" onclick="getClickAbleNew('Call');">Call</label>
                <label><input type="radio" value="Normal" checked='checked' name="radioType" onclick="getClickAbleNew('Normal');"> Normal</label>
            @endif
        </div>
    </div>
    <div class="chat-messages" id="sms-messages">
        <div id="chat-messages-inner" class="notes-messages" style="max-height: 250px;overflow: auto;"></div>
    </div>
    </br></br>
    <div class="message-section">
    <div class="chat-message custom-chat">
            <form id="attachsubmits" method="post" onsubmit="return false;">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                
                            <div class="message-footer-section">
                                <label for="exampleFormControlSelect2"><b>Message</b></label>
                                @if($notesFlag ==1)
                                            <div class="form-check form-check-flat form-check-primary">
                                            <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="notes_message_id" value="1" id="notes_message_id" onclick="getNotesHHACaregiverSubject()">
                                                
                                            Send Notes To HHA
                                            <i class="input-helper"></i></label>
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
                            </div>
                            <span class="input-box notes_content">
                                <textarea name="msg-box" id="text-sms-box" class="tribute-demo-input form-control mt-2 text-share"></textarea>
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
                        

                <div class="row mt-2">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6">
                    <button class="btn btn-success btn-sm" id="text-sms-send-btn" style="float:right" onclick="sendMessagefile()"><i id="send_message_loader" class="circle-loader hide" ></i> Send</button>
                    </div>
                    
                </div>

            </form>
        </div>
    </div>
</div>