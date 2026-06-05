@include('include/header_lte')
<style>
    a.btn.bg-maroon.margin {
        width: 100%;
        margin-left: 0px;
    }

    .error {
        color: red;
    }

    .back-gray {
        background: #f4f4f4
    }

    .side-box {
        width: 100%;
        float: left;
        padding: 30px 15px;
    }

    .box {
        border-top: 0px !important;
    }

    .box-header.with-border {
        padding: 10px 25px !important;
    }
</style>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content">
        <div class="row">
            @include('include/testbar_nav')
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Report</h3>

                        <div class="box-tools pull-right">
                            <div class="btn-group">

                                <a href="<?php echo URL::to('/'); ?>/template" class="btn btn-default btn-sm"><i class="fa fa-fast-backward"></i></a>
                                <button type="button" class="btn btn-default btn-sm" title="search" data-toggle="modal" data-target="#modal-search"><i class="fa fa-search" aria-hidden="true"></i></button>


                                <a href="<?php echo URL::to('/'); ?>/intake_report/export?docs_type=<?php echo $docs_type; ?>&temples_name=<?php echo $temples_name; ?>&status_id=<?php echo $status_id; ?>&dates_id=<?php if (isset($dates_id) && $dates_id != '') {
                                                                                                                                                                                                                        echo date('m/d/Y', strtotime($dates_id));
                                                                                                                                                                                                                    } ?>" class="btn btn-default btn-sm" title="Export CSV"><i class="fa fa-file-o" aria-hidden="true"></i></a>
                                <button type="button" class="btn btn-default btn-sm" id="deleted_id" onclick="getDelete()" title="Bulk Delete"><i class="fa fa-trash-o"></i></button>

                            </div>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">

                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th><input type="checkbox" name="check_id" id="check_all_id"></th>
                                    <th>No</th>

                                    <th>Template Name</th>
                                    <th>Document Type</th>
                                    <th>Status</th>
                                    <th>Sender</th>
                                    <th>Receipent</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    <?php

                                    use Illuminate\Support\Facades\Input;

                                    if (count($templete_report) > 0) {

                                        if (request('page') != 1 && request('page') != "") {
                                            $cnt = request('page') * 10 - 9;
                                        } else {
                                            $cnt = 1;
                                        }
                                        foreach ($templete_report as $key) { ?>
                                            <tr>
                                                <td><input type="checkbox" name="check_ids[]" class="check_all_id" value="<?php echo $key->id; ?>"></td>
                                                <td><?php echo $cnt; ?></td>

                                                <td><?php echo $key->template_name; ?></td>
                                                <td><?php echo $key->name; ?></td>
                                                <td><?php if ($key->status == 'Pending') {
                                                        $status = '<label class="label label-warning">' . $key->status . '</label>';
                                                    } else {
                                                        $status = '<label class="label label-success">' . $key->status . '</label>';
                                                    }
                                                    echo $status; ?></td>
                                                <td><?php echo $key->ufname . ' ' . $key->ulname; ?></td>
                                                <td><?php echo $key->receipt_name . " - " . $key->main_intakeId; ?></td>
                                                <td><?php echo date('m/d/Y', strtotime($key->created_date)); ?></td>
                                                <td><?php if ($key->status == 'Pending') { ?>
                                                        <a href="<?php echo URL::to('/'); ?>/template_report/delete?id=<?php echo $key->id; ?>" onclick="return confirm('Are you sure remove this record?');"><i class="fa fa-trash-o"></i></a>
                                                    <?php }
                                                    if ($key->pdf_generate != '') {  ?>
                                                        <a target="_blank" href="<?php echo URL::to('/'); ?>/dosusinguploads/docusign/<?php echo $key->pdf_generate; ?>"><i class="fa fa-file-pdf-o"></i></a>
                                                    <?php } ?>

                                                </td>
                                            </tr>
                                        <?php $cnt++;
                                        }
                                    }
                                    if (count($templete_report) == 0) {  ?>
                                        <tr>
                                            <td colspan="10"> No record available</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div class="pull-right">

                                <div class="btn-group">
                                    <?php if (count($templete_report) > 0) { ?>
                                        <?php echo $templete_report->appends(Request::all())->links(); ?>
                                    <?php  } ?>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.table -->
                        </div>
                        <!-- /.mail-box-messages -->
                    </div>

                </div>
                <!-- /. box -->
            </div>

        </div>

    </section>

</div>

<div class="modal fade" id="modal-search" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Edit Search</h4>
            </div>
            <form action="" method="get" id="changephonenumberform">
                <div class="modal-body">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Document Type</label>
                            <select name="docs_type" class="form-control">
                                <option value="">Select Document Type</option>
                                <?php
                                if (isset($document_list) && $document_list) {
                                    foreach ($document_list as $val) { ?>
                                        <option value="<?php echo $val->id; ?>" <?php if ($docs_type == $val->id) {
                                                                                    echo "selected='selected'";
                                                                                } ?>><?php echo $val->name; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Template Name</label>
                            <select name="temples_name" class="form-control">
                                <option value="">Select Document Type</option>
                                <?php
                                if (isset($templete_list) && $templete_list) {
                                    foreach ($templete_list as $vals) { ?>
                                        <option value="<?php echo $vals->id; ?>" <?php if ($temples_name == $vals->id) {
                                                                                    echo "selected='selected'";
                                                                                } ?>> <?php echo $vals->template_name; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status_id">
                                <option value="">Select Status</option>

                                <option value="pending" <?php if ($status_id == "pending") {
                                                            echo "selected='selected'";
                                                        } ?>>Pending</option>
                                <option value="completed" <?php if ($status_id == "completed") {
                                                                echo "selected='selected'";
                                                            } ?>>Completed</option>
                                <!--<option value="completed" <?php if ($status_id == "completed") {
                                                                    echo "selected='selected'";
                                                                } ?>>Completed</option>
                                <option value="declined" <?php if ($status_id == "declined") {
                                                                echo "selected='selected'";
                                                            } ?>>Declined</option>
                                <option value="voided" <?php if ($status_id == "voided") {
                                                            echo "selected='selected'";
                                                        } ?>>Voided</option>
                                <option value="correct" <?php if ($status_id == "correct") {
                                                            echo "selected='selected'";
                                                        } ?>>Correct</option>-->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="text" name="dates_id" placeholder="Date" class="form-control date" autocomplete="off" value="<?php if (isset($dates_id) && $dates_id != '') {
                                                                                                                                            echo date('m/d/Y', strtotime($dates_id));
                                                                                                                                        } ?>">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $('.date').datepicker({
        firstDayOfWeek: 1, // The first day of week is Monday
        daysOfWeekDisabled: [0, 2, 3, 4, 5, 6],
        weekDayFormat: 'narrow', // Only first letter for the weekday names
        inputFormat: 'M/d/y',
        outputFormat: 'MM/dd/y',
        titleFormat: 'EEEE MMMM d y',
        theme: 'green',
        modal: false
    });
    $('#check_all_id').on('change', function() {
        $('.check_all_id').prop('checked', $(this).prop("checked"));

    });
    //deselect "checked all", if one of the listed checkbox product is unchecked amd select "checked all" if all of the listed checkbox product is checked
    $('.check_all_id').change(function() { //".checkbox" change 
        if ($('.check_all_id:checked').length == $('.checkbox').length) {
            $('#check_all_id').prop('checked', true);
        } else {
            $('#check_all_id').prop('checked', false);
        }

    });

    function getDelete() {
        var cnt = 0;
        var AppoveVisitIds = [];
        $.each($("input[name='check_ids[]']:checked"), function() {
            AppoveVisitIds.push($(this).val());
        });
        if (AppoveVisitIds == '') {
            alert("Please select checkbox.");
            return false;
        }
        if (AppoveVisitIds.length > 0) {
            var confirms = confirm("Are you sure remove this record?");

            if (confirms == true) {
                $.ajax({
                    url: "<?php echo URL::to('/'); ?>/bulkdelete",
                    type: "POST",
                    data: {
                        'AppoveVisitIds': AppoveVisitIds,
                        "_token": "<?php echo csrf_token(); ?>"
                    },
                    success: function(response) {
                        if (response == 1) {
                            alert("Report successfully deleted");
                            location.reload();
                        } else {
                            alert("error");
                            location.reload();
                        }
                    }
                });
            }
        }
    }
</script>
@include('include/footer_lte')