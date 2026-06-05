<style>
   
    </style>
<div class="tab-pane" id="task-section">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="card-title mb-0">Task</p>
        <?php if ($user['user_type_fk'] == 184) { ?>
        <p class="mb-0 tx-13 pull-right">
            <a data-toggle="modal" class=" btn btn-info btn-sm  d-none d-md-block" data-target="#exampleModal-task"
                data-whatever="@mdo" style="color:#fff"><i class="mdi mdi-plus"></i> Add Task</a>
        </p>
        <?php } ?>
    </div>
    <div class="row">
        <div class="col-12">
            <div id="task_resp_id" >

            </div>
        </div>
    </div>
</div>