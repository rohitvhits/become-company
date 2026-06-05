<style>
    .notes_content { width: 100%; }
    .vertical_line { width:2px; height:25px; background-color:#d9d7d7; }
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
            <a class="btn btn-primary btn-sm" id="print_id">Print</a>
            <!-- <div class="vertical_line"></div> -->
            
        </div>
    </div>
    <div class="chat-messages" id="sms-messages">
        <div id="chat-messages-inner" class="notes-messages" style="max-height: 550px;overflow: auto;"></div>
    </div>
    </br></br>
    @if($addNotesAppointmentFlag ==1)
    <div class="message-section">
        <div class="chat-message custom-chat">
            <form id="attachsubmits" method="post" onsubmit="return false;">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="col-md-12 row">
                        <div class="col-md-7">
                            <div class="row">
                                <label for="exampleFormControlSelect2"><b>Message</b></label>
                            </div>
                            <div class="row radio-group">
                                @if($user['user_type_fk'] == 184 )
                                    <label><input type="radio" value="Call" name="radioType" onclick="getClickAbleNew('Call');">Call</label>
                                    <label><input type="radio" value="Normal" checked='checked' name="radioType" onclick="getClickAbleNew('Normal');"> Normal</label>
                                @endif
                            </div>
                            
                        </div>
                        <div class="col-md-5">
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
                                        <div class="col-md-6 hide"  id="send_hha_subject_id">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="exampleFormControlSelect2">Reason</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" id="subjectNotesId" name="subjectNotesId">
                                                        <option value="">Select Reason</option>
                                                    </select>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @endif
                        </div>

                    </div>
                    <div class="row" >
                        <div class="col-md-12">
                        <span class="input-box notes_content">
                                <div contenteditable="true" name="msg-box" id="text-sms-box" class="tribute-demo-input form-control mt-2 text-share" style="min-height:50px; height:auto; overflow-y:auto; white-space: pre-wrap; overflow-wrap: break-word; border-radius: 8px;"></div>
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
                    
                    
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <button class="btn btn-success btn-sm" id="text-sms-send-btn" onclick="sendMessagefile()">Send</button>
                        @can('notes-ai-help-me-write')
                         @if(auth()->user()->agency_fk =="")
                         
                        <button type="button" class="btn-hmw ml-1" data-hmw-context="notes" data-hmw-field="text-sms-box" onclick="aiHelpMeWrite('notes', 'text-sms-box', 'contenteditable')" data-toggle="tooltip" title="Help me write with AI">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="white" style="vertical-align:middle;flex-shrink:0;"><path d="M12 3c-1.2 5.4-5.4 7.8-9 9 3.6 1.2 7.8 3.6 9 9 1.2-5.4 5.4-7.8 9-9-3.6-1.2-7.8-3.6-9-9z"/><path d="M5 3c-.6 2.7-2.3 3.7-4 4 1.7.3 3.4 1.3 4 4 .6-2.7 2.3-3.7 4-4-1.7-.3-3.4-1.3-4-4z" opacity=".8"/></svg>
                            <span>Help me write</span>
                        </button>
                        @endif
                        @endcan
                        <img class="hide" src="{{ asset('ajax-loader.gif')}}" alt="loader" id="send_message_loader">    
                    
                    </div>
                    
                    
                </div>

            </form>
        </div>
    </div>
    @endif
</div>