<div class="tab-pane" id="text-messages-section">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Text Message</p>
        <div class="pull-right">
            <img src="{{ asset('/ajax-loader.gif') }}" alt="loader" id="loadertag122" style="display: none; ">
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="text-chat-messages" id="text-sms-messages">
                <div id="text-chat-messages-inner" class="text-notes-messages">
                </div>
            </div>
            <div class="chat-message  custom-chat">
                <form id="textMessageSubmits" method="post" onsubmit="return false;">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <textarea style="width: 100%; min-height: 80px; max-height: 200px; overflow-y: auto; resize: vertical; margin-bottom: 6px;" name="msg-box" id="smsTextMessage" class="form-control"></textarea>
                    <span class="error text-danger d-block mb-1" id="smsTextMessageError"></span>
                    <button class="btn btn-success btn-sm" id="text-sms-send-btn" onclick="sendTextMessagefile()">Send</button>
                    @can('text-message-ai-help-me-write')
                         @if(auth()->user()->agency_fk =="")
                    <button type="button" class="btn-hmw ml-1" data-hmw-context="sms" data-hmw-field="smsTextMessage" onclick="aiHelpMeWrite('sms', 'smsTextMessage', 'textarea')" data-toggle="tooltip" title="Help me write with AI">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="white" style="vertical-align:middle;flex-shrink:0;"><path d="M12 3c-1.2 5.4-5.4 7.8-9 9 3.6 1.2 7.8 3.6 9 9 1.2-5.4 5.4-7.8 9-9-3.6-1.2-7.8-3.6-9-9z"/><path d="M5 3c-.6 2.7-2.3 3.7-4 4 1.7.3 3.4 1.3 4 4 .6-2.7 2.3-3.7 4-4-1.7-.3-3.4-1.3-4-4z" opacity=".8"/></svg>
                        <span>Help me write</span>
                    </button>
                    @endif
                    @endcan
                </form>
            </div>
        </div>
    </div>
</div>

