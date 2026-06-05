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
                    <span class="input-box">
                        <textarea style="margin-bottom: 0 !important; width: 100%;" name="msg-box" id="smsTextMessage"></textarea>
                    </span>
                    <span class="error" id="smsTextMessageError"></span><br>
                    <button class="btn btn-success btn-sm" id="text-sms-send-btn"
                        onclick="sendTextMessagefile()">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

