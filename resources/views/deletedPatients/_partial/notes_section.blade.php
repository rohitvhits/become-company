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
            <!-- <div class="vertical_line"></div> -->
            
        </div>
    </div>
    <div class="chat-messages" id="sms-messages">
        <div id="chat-messages-inner" class="notes-messages" style="max-height: 250px;overflow: auto;"></div>
    </div>
    </br></br>
    <div class="message-section">
    <div class="chat-message custom-chat">
            
        </div>
    </div>
</div>