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
        <p class="card-title mb-0">Notes</p>
        <?php if ($user['user_type_fk'] == 184 || ($user['user_type_fk'] == 2 || $user['user_type_fk'] == 6)) { ?>
            <p class="mb-0 tx-13">
                <a data-toggle="modal"
                    class="pull-right btn btn-info btn-sm d-none d-md-block"
                    data-target="#add-notes" data-whatever="@mdo" onclick="openNotesModel()"><i
                        class="mdi mdi-plus"></i>
                    Add</a>
            </p>
        <?php } ?>
    </div>
    <div class="chat-messages" id="sms-messages">
        <div id="chat-messages-inner" class="notes-messages" style="max-height: 350px;overflow: auto;"></div>
    </div>
    </br></br>
</div>