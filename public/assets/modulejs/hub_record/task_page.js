var globalTaskId;
function getTaskChangeStatus() {
  var status = $("#status_id").val();
  var cnt = 0;
  $("#task_status_error").html("");
  if (status == "") {
    $("#task_status_error").html("Required");
    cnt = 1;
  }

  if (cnt == 1) {
    return false;
  } else {
    var form = $("#task_form").serialize();

    $.ajax({
      type: "GET",
      url: change_status_url,
      data: form,
      success: function (res) {
        var record_id = $("#edit_id").val();
        if (res.status == 1) {
          toastr.success(res.error_msg);

          var response = "";
          if (res.data.status == "Pending") {
            response = '<span class="badge badge-primary">Pending</span>';
          }
          if (res.data.status == "Urgent") {
            response = '<span class="badge badge-danger">Urgent</span>';
          }
          if (res.data.status == "Outstanding") {
            response = '<span class="badge badge-success">Outstanding</span>';
          }
          if (res.data.status == "Completed") {
            response = '<span class="badge badge-info">Completed</span>';
          }
          $("#status" + record_id).html(response);
          $("#task_description").val("");
          $("#exampleModal-change-task-staus").modal("hide");
        } else {
          toastr.error(res.error_msg);
        }
      },
    });
  }
}
function openSpan(response_id) {
  $.ajax({
    url: task_list_ajax,
    dataType: "json",
    type: "GET",
    data: { id: response_id },
    success: function (datalist) {
      $("#project_title").text(datalist.data.task_name);
      $("#task_discription").val(datalist.data.task_description);
      $("#hdn_task_id").val(response_id);
      $("#disc").html(datalist.data.task_description);
      $("#daterange").val(datalist.data.due_date);
      $("#task_status_select").val(datalist.data.task_status);
      $("#assign_to_user_select").val(datalist.data.assign_id);
      $("#hdn_priority").val(datalist.data.priority);
      $("#created_date").text(datalist.data.created_date);
      $("#created_by").html(
        '<p class="clearfix"><span class="float-left left-text-sapn"><strong>' +
          datalist.data.first_name +
          " " +
          datalist.data.last_name +
          '</strong></span>&nbsp; &nbsp;<span class="float-right text-muted right-text-sapn"><i class="fa fa-clock-o"></i>  ' +
          datalist.data.created_date +
          "</span></p>"
      );

      $(".clock_in").addClass("hide");
      $(".clock_out").addClass("hide");

      if (datalist.data.clockInFlag == 1) {
        if (datalist.data.clockinOut == 1) {
          $(".clock_out").removeClass("hide");
        } else {
          $(".clock_in").removeClass("hide");
        }
      }
      $("#right-sidebar").toggleClass("open");
      globalTaskId = datalist.data.id;
      commentList();
    },
  });
}
/*****************comment****************/
function commentList() {
  var task_id = $("#hdn_task_id").val();
  $.ajax({
    method: "GET",
    url: comment_list,
    data: {
      task_id: task_id,
    },

    success: function success(res) {
      var data = res.data;
      var response = "";
      $("#commentList").html("");
      if (data.length != 0) {
        var cnt = 1;
        $.each(data, function (i, v) {
          response += `<div class="align-items-start profile-feed-item"><div class=" ml-1">
                    <p class="clearfix">
                    <span class="float-left">
                    <strong>${v.user_details.first_name}  ${
            v.user_details.last_name
          }</strong>
                    </span>
                    <span class="float-right text-muted">
                    <i class="fa fa-clock-o"></i>&nbsp;&nbsp;${moment(
                      v.created_date
                    ).format("MM/DD/YYYY HH:mm A")}
                    </span>
                    </p>
                    <p class="clearfix"></p>	
                    <p style="white-space:pre-line;margin-top:-10px">
                    ${v.comment}
                    </p>
                </div></div>`;
        });
      }
      $("#commentList").append(response);
      $(".chat").animate(
        {
          scrollTop: $(".chat")[0].scrollHeight,
        },
        2000
      );
    },
  });
}
function commentSave() {
  var comment = $("#task_comment").val();
  var task_id = $("#hdn_task_id").val();
  $("#comment_error").html("");
  if (comment.trim() == "") {
    $("#comment_error").html("Please enter Message");
    return false;
  } else {
    $.ajax({
      method: "post",
      url: save_comment,
      data: {
        task_id: task_id,
        comment: comment,
        _token: _CSRF_TOKEN,
      },

      success: function success(response) {
        var htmlResponse = `<div class="align-items-start profile-feed-item"><div class=" ml-1">
                    <p class="clearfix">
                    <span class="float-left">
                    <strong>${response.data.user_details.first_name}  ${
          response.data.user_details.last_name
        }</strong>
                    </span>
                    <span class="float-right text-muted">
                    <i class="fa fa-clock-o"></i>&nbsp;&nbsp;${moment(
                      response.data.created_date
                    ).format("MM/DD/YYYY HH:mm A")}
                    </span>
                    </p>
                    <p class="clearfix"></p>	
                    <p style="white-space:pre-line;margin-top:-10px">
                    ${response.data.comment}
                    </p>
                </div></div>`;

        $("#task_comment").val("");
        $("#commentList").append(htmlResponse);
        $(".chat").animate(
          {
            scrollTop: $(".chat")[0].scrollHeight,
          },
          2000
        );
      },
      error: function error(_error) {
        toastr.error(_error.responseJSON.message);
      },
    });
  }
}

function getChangeStatusById(status) {
  var response_id = $("#hdn_task_id").val();
  $.ajax({
    type: "GET",
    url: save_status_url,
    data: {
      id: response_id,
      status: status,
      patient_id: _RECORD_ID,
    },
    success: function (res) {
      $("#task_status_select").val(status);
      if (res.status == 1) {
        toastr.success(res.error_msg);
        var response = "";
        $(" #select2-task_status_select-container").text(res.data.status);
        if (res.data.status == "Pending") {
          $("#task_status").html(
            '<span class="badge badge-primary">Pending</span>'
          );
          $("#status" + response_id + "").html(
            '<span class="badge badge-primary">Pending</span>'
          );
        }
        if (res.data.status == "Urgent") {
          $("#task_status").html(
            '<span class="badge badge-danger">Urgent</span>'
          );
          $("#status" + response_id + "").html(
            '<span class="badge badge-danger">Urgent</span>'
          );
        }
        if (res.data.status == "Outstanding") {
          $("#task_status").html(
            '<span class="badge badge-success">Outstanding</span>'
          );
          $("#status" + response_id + "").html(
            '<span class="badge badge-success">Outstanding</span>'
          );
        }
        if (res.data.status == "Completed") {
          $("#task_status").html(
            '<span class="badge badge-info">Completed</span>'
          );
          $("#status" + response_id + "").html(
            '<span class="badge badge-info">Completed</span>'
          );
        }
      } else {
        toastr.error(res.error_msg);
      }
    },
    error: function error(_error) {
      toastr.error(_error.responseJSON.message);
    },
  });
}

function getTimeLogList(page) {
  var page =
    arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  $.ajax({
    method: "GET",
    url: page_url,
    data: {
      id: globalTaskId,
      _token: _CSRF_TOKEN,
    },
    beforeSend: function () {
      $("#loadertag1").show();
    },
    success: function success(response) {
      $("#loadertag1").hide();
      $("#task-time-log-list").html("");
      $("#task-time-log-list").html(response);
    },
    error: function error(_error) {
      toastr.error("Something happened. Try again");
    },
  });
}

function getLogList(page) {
  var response_id = $("#hdn_task_id").val();
  $.ajax({
    method: "GET",
    url: activity_log,
    data: {
      id: response_id,
      _token: _CSRF_TOKEN,
    },
    beforeSend: function () {
      $("#loadertag").show();
    },
    success: function success(res) {
      var data = res.tasklogdata;
      var response = "";
      $("#logList").html("");
      if (data.length != 0) {
        var cnt = 1;
        $.each(data, function (i, v) {
          response += `<div class="align-items-start profile-feed-item"><div class=" ml-1">
                    <p class="clearfix">
                    <span class="float-left">
                    <strong>${v.users.first_name}  ${v.users.last_name}</strong>
                    </span>
                    <span class="float-right text-muted">
                    <i class="fa fa-clock-o"></i>&nbsp;&nbsp;${moment(
                      v.created_at
                    ).format("MM/DD/YYYY HH:mm A")}
                    </span>
                    </p>
                    <p class="clearfix"></p>	
                    <p style="white-space:pre-line;margin-top:-10px">
                    ${v.description}
                    </p>
                </div></div>`;
        });
      }
      $("#logList").append(response);
    },
    error: function error(_error) {
      console.error(_error);
      toastr.error("Something happened. Try again");
    },
  });
}

function assignUserById(assignUserId) {
  var task_id = $("#hdn_task_id").val();
  var selectedText = $("#assign_to_user_select option:selected").text();
  $.ajax({
    method: "GET",
    url: assign_user_url,
    data: {
      task_id: task_id,
      assignUserId: assignUserId,
      selectedText: selectedText,
      patient_id: _RECORD_ID,
    },

    success: function success(response) {
      $("#assignee").text(response.data);
      $("#assignee" + task_id + "").text(response.data);
      toastr.success(response.error_msg);
    },
    error: function error(_error) {
      toastr.error(_error.responseJSON.message);
    },
  });
}

$(document).ready(function () {
  var dateToday = new Date();
  $(".datepicker").datepicker({
    minDate: dateToday,
    dateFormat: "mm/dd/yy",
  });

  $("#id_0").datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "MM/DD/YYYY hh:mm:ss A",
    widgetPositioning: {
      vertical: "bottom",
    },
  });
  $("#task_discription").on("blur", function (e) {
    var task_id = $("#hdn_task_id").val();
    var discription = $("#task_discription").val();
    var assign_to = $("#assign_to_user_select").val();
    $.ajax({
      async: false,
      global: false,
      url: task_discription_url,
      dataType: "json",
      type: "POST",
      data: {
        id: task_id,
        assign_to: assign_to,
        task_description: discription,
        _token: _CSRF_TOKEN,
      },
      success: function (datalist) {},
    });
  });
  $("#daterange").blur(function () {
    var task_id = $("#hdn_task_id").val();
    var due_date = $(this).val();
    //console.log(due_date);
    var assign_to = $("#assign_to_user_select").val();
    $.ajax({
      async: false,
      global: false,
      url: task_discription_url,

      type: "POST",
      data: {
        id: task_id,
        assign_to: assign_to,
        due_date: due_date,
      },
      success: function (datalist) {
        $("#due_date" + task_id + "").text(datalist.TaskDetails.due_date);
      },
    });
  });

  var start = moment().subtract(0, "days");
  var end = moment();
  $("#task_due_date").daterangepicker(
    {
      startDate: start,
      endDate: end,
      autoUpdateInput: false,
      startOfWeek: "sunday",
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
        "Next Month": [
          moment().add(1, "month").startOf("month"),
          moment().add(1, "month").endOf("month"),
        ],
        "Next Week": [
          moment().add(1, "weeks").startOf("isoWeek"),
          moment().add(1, "weeks").endOf("isoWeek"),
        ],
        "Last Week": [
          moment().subtract(1, "weeks").startOf("isoWeek"),
          moment().subtract(1, "weeks").endOf("isoWeek"),
        ],
      },
    },
    function (chosen_date, end_date) {
      $("#task_due_date").val(
        chosen_date.format("MM/DD/YYYY") + " - " + end_date.format("MM/DD/YYYY")
      );
    }
  );

  $("#created_task_date").daterangepicker(
    {
      startDate: start,
      endDate: end,
      autoUpdateInput: false,
      startOfWeek: "sunday",
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
        "Next Month": [
          moment().add(1, "month").startOf("month"),
          moment().add(1, "month").endOf("month"),
        ],
        "Next Week": [
          moment().add(1, "weeks").startOf("isoWeek"),
          moment().add(1, "weeks").endOf("isoWeek"),
        ],
        "Last Week": [
          moment().subtract(1, "weeks").startOf("isoWeek"),
          moment().subtract(1, "weeks").endOf("isoWeek"),
        ],
      },
    },
    function (chosen_date, end_date) {
      $("#created_task_date").val(
        chosen_date.format("MM/DD/YYYY") + " - " + end_date.format("MM/DD/YYYY")
      );
    }
  );
});
/*************ajax-tabble**************/
function getTableList(page) {
  var page =
    arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  $.ajax({
    method: "GET",
    url: task_list_ajax_url + page,
    data: {
      id: globalTaskId,
      _token: _CSRF_TOKEN,
    },
    beforeSend: function () {
      $("#loadertagDiv").css("display", "block");
      $("#loadertable").show();
    },
    success: function success(response) {
      $("#loadertagDiv").css("display", "none");
      $("#loadertable").hide();
      $("#task_list_div").html("");
      $("#task_list_div").html(response);
    },
    error: function error(_error) {
      console.error(_error);
      toastr.error("Something happened. Try again");
    },
  });
}

$(document).on(
  "click",
  ".task-list-pegination .pagination a",
  function (event) {
    $("li").removeClass("active");
    $(this).parent("li").addClass("active");
    event.preventDefault();
    var myurl = $(this).attr("href");
    var page = $(this).attr("href").split("page=")[1];
    getTableList(page);
  }
);

$(document).on("click", ".searchTask", function () {
  var taskName = $("#task_name").val();
  var userId = $("#user_id").val();
  var status = $("#status").val();
  var task_due_date = $("#task_due_date").val();
  var created_user_id = $("#created_user_id").val();
  var created_task_date = $("#created_task_date").val();
  var priority = $("#priority").val();
  console.log(userId);
  if (taskName == "" && userId == "" && status == "") {
    alert("Please enter any one search text");
    return false;
  } else {
    taskName = taskName != null ? taskName : "";
    userId = userId != null ? userId : "";
    status = status != null ? status : "";
    task_due_date = task_due_date != null ? task_due_date : "";
    created_user_id = created_user_id != null ? created_user_id : "";
    created_task_date = created_task_date != null ? created_task_date : "";
    priority = priority != null ? priority : "";
    $.ajax({
      method: "GET",
      url:
        search_link +
        "&task_name=" +
        taskName +
        "&user_id=" +
        userId +
        "&status=" +
        status +
        "&task_due_date=" +
        task_due_date +
        "&created_user_id=" +
        created_user_id +
        "&created_task_date=" +
        created_task_date +
        "&priority=" +
        priority +
        "",
      data: {
        id: globalTaskId,
        _token: _CSRF_TOKEN,
      },
      beforeSend: function () {
        $("#loadertag").show();
      },
      success: function success(response) {
        console.log(userId);
        $("#loadertag").hide();
        $("#task_list_div").html("");
        $("#task_list_div").html(response);
      },
      error: function error(_error) {
        console.error(_error);
        toastr.error("Something happened. Try again");
      },
    });
  }
});

function getDelete(id) {
  swal(
    {
      title: "Are you delete this task ?",
      text: "",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      closeOnConfirm: false,
      closeOnCancel: false,
    },
    function (isConfirm) {
      if (isConfirm) {
        $.ajax({
          type: "POST",
          url: task_list_page_url + id,
          data: {
            _token: _CSRF_TOKEN,
            _method: "DELETE",
            id: id,
          },
          success: function (res) {
            if (res == 1) {
              toastr.success("Task successfully deleted");
              window.location.href = task_list_page_url;
              swal.close();
            } else {
              toastr.error("Sorry, something went wrong. Please try again.");
              swal.close();
            }
          },
        });
      } else {
        swal.close();
      }
    }
  );
}

function getTaskList() {
  $.ajax({
    url: _HUB_TASK_LIST + "?id=" + _RECORD_ID,
    type: "GET",
    success: function (res) {
      $("#task_resp_id").html("");
      $("#task_resp_id").html(res);
    },
  });
  return false;
}

function getDeleteTask(id) {
  $.confirm({
    title: "Delete",
    columnClass: "col-md-6",
    content: "Are you sure you want to delete this task?",
    buttons: {
      formSubmit: {
        text: "Delete",
        btnClass: "btn-danger",
        action: function () {
          $.ajax({
            type: "POST",
            url: _TASK + "/" + id,
            data: {
              _token: _CSRF_TOKEN,
              _method: "DELETE",
              id: id,
              hub_record_id: _RECORD_ID
            },
            success: function (res) {
              if (res == 1) {
                toastr.success("Task successfully deleted");
                getTaskList();
              } else {
                toastr.error("Sorry, something went wrong. Please try again.");
              }
            },
          });
        },
      },
      cancel: function () {
        //close
      },
    },
  });
}

function TaskSubmit() {
  event.preventDefault();
  var task_name_id = $("#task_name_id").val();
  var assign_to_id = $("#assign_to_id").val();
  var dueDate = $("#due_date_task_model_id").val();
  var startDate = $("#start_date").val();
  var priority = $("#priority").val();
  var task_description = $("#task_description_modal").val();
  var cnt = 0;

  $("#task_name_id_error").html("");
  $("#assign_to_error").html("");
  $("#start_date_error").html("");
  $("#priority_error").html("");
  $("#task_description_task_error").html("");
  $("#due_date_error").html("");

  if (task_name_id.trim() == "") {
    $("#task_name_id_error").html("Please enter Task Name");
    cnt = 1;
  } else if (task_name_id.length > 70) {
    cnt = 1;
    $("#task_name_id_error").html(
      "Task title should be less than 70 characters."
    );
  }
  if (assign_to_id == "") {
    $("#assign_to_error").html("Please select Assign To User");
    cnt = 1;
  }
  if (dueDate == "") {
    $("#due_date_error").html("Please select Due Date");
    cnt = 1;
  }
  if (startDate == "") {
    $("#start_date_error").html("Please select Start Date");
    cnt = 1;
  }
  if (priority == "") {
    $("#priority_error").html("Please select Priority");
    cnt = 1;
  }
  if (task_description.trim() == "") {
    $("#task_description_task_error").html("Please enter Description");
    cnt = 1;
  }

  if (startDate > dueDate) {
    $("#start_date_error").html("Start Date cannot be later than Due Date.");
    cnt = 1;
  }
  if (cnt == 1) {
    return false;
  } else {
    $("#submitTaskAdd").attr("disabled", true);
    var forn = $("#hub_task_patient_id")[0];
    var formData = new FormData(forn);
    formData.append("_token", _CSRF_TOKEN);

    $.ajax({
      url: _HUB_TASK_ADD,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (res) {
        if (res.status == 1) {
          toastr.success(res.error_msg);
          $("#assign_to_id").val(null).trigger("change");
          $("#hub_task_patient_id")[0].reset();
          $("#closed_id_task").click();
          getTaskList();
          $("#submitTaskAdd").attr("disabled", false);
        } else {
          toastr.error(res.error_msg);
          $("#submitTaskAdd").attr("disabled", false);
        }
      },
    });
  }
}

function getModal(id) {
  $(".error").html("");
  $("#task_description").val("");
  $("#edit_id").val(id);
}

function clockIn(id, type) {
  $.ajax({
    method: "GET",
    url: _TASK_CLOCK_IN_OUT,
    data: {
      id: globalTaskId,
      type: type,
      auth_id: auth_id,
      _token: _CSRF_TOKEN,
    },

    success: function success(response) {
      if (response.data.type == "clock_in") {
        $(".clock_in").addClass("hide");
        $(".clock_out").removeClass("hide");
      } else {
        $("#task_hour_id").html("");
        $("#task_hour_id").html(response.data.task_hour);
        $(".clock_in").removeClass("hide");
        $(".clock_out").addClass("hide");
      }
      getLogList(1);

      toastr.success(response.error_msg);
    },
    error: function error(_error) {
      toastr.error(_error.responseJSON.message);
      // location.reload();
    },
  });
}
