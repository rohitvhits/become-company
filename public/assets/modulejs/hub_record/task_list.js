$(function () {
  $(".wmd-view-topscroll").scroll(function () {
    $(".wmd-view").scrollLeft($(".wmd-view-topscroll").scrollLeft());
  });
  $(".wmd-view").scroll(function () {
    $(".wmd-view-topscroll").scrollLeft($(".wmd-view").scrollLeft());
  });

  $(".clock_in").addClass("hide");
  $(".clock_out").addClass("hide");
  $(".clock_in_div").addClass("hide");
  getTaskListView();
});
$("#main_checkBox1").click(function () {
  var names = $("#main_checkBox1").is(":checked");

  if (names == true) {
    $(".cbox_id").prop("checked", true);
  } else {
    $(".cbox_id").prop("checked", false);
  }
});

function getDelete(id) {
  swal(
    {
      title: "Are you remove this task ?",
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
          url: TASK_LIST + id,
          data: {
            _token: CSRF_TOKEN,
            _method: "DELETE",
            id: id,
          },
          success: function (res) {
            if (res == 1) {
              toastr.success("Task successfully deleted");
              $("#" + id).remove();
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

function getModal(id) {
  $("#edit_id").val(id);
}

$(document).on("click", ".searchTask", function () {
  getTaskListView();
});

/*********************************************/
function openSpan(response_id) {
  $("#hdn_task_id").val(response_id);

  $.ajax({
    url: TASK_AJAX,
    dataType: "json",
    type: "GET",
    data: {
      id: response_id,
    },
    success: function (datalist) {
      $("#project_title").text(datalist.data.task_name);
      $("#task_discription").val(datalist.data.task_description);
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
      $("#right-sidebar").toggleClass("open");
      commentList();
    },
  });
}
/*****************comment****************/
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
      url: COMMENT_SAVE,
      data: {
        task_id: task_id,
        comment: comment,
        _token: CSRF_TOKEN,
      },
      success: function success(response) {
        toastr.success(response.error_msg);
        $("#task_comment").val("");
        $("#comment-card").animate(
          {
            scrollTop: $("#comment-card")[0].scrollHeight,
          },
          1000
        ); // 600ms for smooth scroll
        commentList();
        getLogList();
      },
      error: function error(_error) {
        toastr.error(_error.responseJSON.message);
      },
    });
  }
}

/***************************************/
function getChangeStatusById(status) {
  var response_id = $("#hdn_task_id").val();
  $.ajax({
    type: "GET",
    url: TASK_STATUS_CHANGE,
    data: {
      id: response_id,
      status: status,
    },
    success: function (res) {
      $("#task_status_select").val(status);
      if (res.status == 1) {
        toastr.success(res.error_msg);
        var response = "";
        $(" #select2-task_status_select-container").text(res.data.status);
        if (res.data.status == "Pending") {
          $("#status-name").html("Pending");
          $("#status" + response_id + "").html(
            '<span class="badge badge-warning">Pending</span>'
          );
          $(".statusBtn")
            .addClass("badge-warning")
            .removeClass("badge-danger badge-success badge-info");
        }
        if (res.data.status == "Urgent") {
          $("#status-name").html("Urgent");
          $("#status" + response_id + "").html(
            '<span class="badge badge-danger">Urgent</span>'
          );
          $(".statusBtn")
            .addClass("badge-danger")
            .removeClass("badge-warning badge-success badge-info");
        }
        if (res.data.status == "Outstanding") {
          $("#status-name").html("Outstanding");
          $("#status" + response_id + "").html(
            '<span class="badge badge-success">Outstanding</span>'
          );
          $(".statusBtn")
            .addClass("badge-success")
            .removeClass("badge-warning badge-danger badge-info");
        }
        if (res.data.status == "Completed") {
          $("#status-name").html("Completed");
          $("#status" + response_id + "").html(
            '<span class="badge badge-info">Completed</span>'
          );
          $(".statusBtn")
            .addClass("badge-info")
            .removeClass("badge-warning badge-danger badge-success");
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
$(document).on("click", ".time-log-pegination .pagination a", function (event) {
  $("li").removeClass("active");
  $(this).parent("li").addClass("active");
  event.preventDefault();
  var myurl = $(this).attr("href");
  var page = $(this).attr("href").split("page=")[1];
  getTimeLogList(page);
});

function getTimeLogList(page) {
  var response_id = $("#hdn_task_id").val();
  var page =
    arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  $.ajax({
    method: "GET",
    url: TASK_TIME_LOG_LIST + "?page=" + page,
    data: {
      id: response_id,
      _token: CSRF_TOKEN,
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

function commentList() {
  var task_id = $("#hdn_task_id").val();
  $.ajax({
    method: "GET",
    url: TASK_COMMENT_LIST,
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
          response += `<div class="comment-entry">
                                            <div class=""></div>
                                            <div class="comment-body">
                                            <div class="comment-meta">
                                                <strong>${
                                                  v.user_details.first_name
                                                }  ${
            v.user_details.last_name
          }</strong> 
                                                <span class="comment-time"><i class="fa fa-clock-o"></i> ${moment(
                                                  v.created_date
                                                ).format(
                                                  "MM/DD/YYYY HH:mm A"
                                                )}</span>
                                            </div>
                                            <p class="comment-text">
                                                ${v.comment}
                                            </p>
                                            </div>
                                        </div>`;
        });
      }
      if (response == "") {
        response = `<div class="comment-entry"><div class="comment-body"><p class="comment-text">No comments yet.</p></div></div>`;
      }
      $("#commentList").html(response);
    },
  });
}

function getLogList(page) {
  var page =
    arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  var response_id = $("#hdn_task_id").val();
  $.ajax({
    method: "GET",
    url: ACTIVITY_LOG + "?page=" + page,
    data: {
      id: response_id,
      _token: CSRF_TOKEN,
    },
    beforeSend: function () {
      $("#loadertag").show();
    },
    success: function success(res) {
      var data = res.tasklogdata;
      var response = "";
      $("#activity-loader-wrapper").html("");
      if (data.length != 0) {
        var cnt = 1;
        $.each(data, function (i, v) {
          response += `<div class="activity-wrapper" id="activity-wrapper"><div class="active-log-wrapper d-flex align-items-start my-2 py-1">
                                <div class="check-with-border mr-1">
                                    <svg class="check-blue " width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" rx="12" fill="#1068EB"></rect>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.2528 8.86721C18.4091 9.02346 18.4872 9.21319 18.4872 9.43641C18.4872 9.65962 18.4091 9.84935 18.2528 10.0056L12.1926 16.0659L11.0542 17.2042C10.8979 17.3605 10.7082 17.4386 10.485 17.4386C10.2618 17.4386 10.072 17.3605 9.91578 17.2042L8.77739 16.0659L5.74725 13.0357C5.591 12.8795 5.51288 12.6897 5.51288 12.4665C5.51288 12.2433 5.591 12.0536 5.74725 11.8973L6.88565 10.7589C7.0419 10.6027 7.23163 10.5246 7.45484 10.5246C7.67806 10.5246 7.86779 10.6027 8.02404 10.7589L10.485 13.2282L15.976 7.72879C16.1323 7.57254 16.322 7.49442 16.5452 7.49442C16.7684 7.49442 16.9582 7.57254 17.1144 7.72879L18.2528 8.86721Z" fill="white"></path>
                                    </svg>
                                    <hr>
                                </div>
                                <div class="log-wrapper ml-2">
                                <span class="log-info"><span class="action-by bold"> ${
                                  v.users.first_name
                                }  ${
            v.users.last_name
          } </span><span class="comment-time"><i class="fa fa-clock-o mr-1"></i>${moment(
            v.created_at
          ).format(
            "MM/DD/YYYY HH:mm A"
          )}</span></span><p class="tx-13 text-muted my-1 bold">${
            v.description
          }</p>
                                </div>
                            </div></div>`;
        });
      }
      $("#activity-loader-wrapper").html(response);
    },
    error: function error(_error) {
      toastr.error("Something happened. Try again");
    },
  });
}
$(document).on("click", ".aclog-pegination .pagination a", function (event) {
  // $('li').removeClass('active');
  // $(this).parent('li').addClass('active');
  // event.preventDefault();
  // var myurl = $(this).attr('href');
  // var page = $(this).attr('href').split('page=')[1];
  // getLogList(page);
});

function assignUserById() {
  count = 0;
  var task_id = $("#hdn_task_id").val();
  var assignUserId = $("#assign_to_user_select").val();
  var selectedText = $("#assign_to_user_select option:selected").text();
  if (assignUserId == "") {
    count++;
    $("#assign_user_id_error").html("Please select assign user");
    return false;
  }
  if (count == 0) {
    $.ajax({
      method: "GET",
      url: TASK_ASSIGN_USER,
      data: {
        task_id: task_id,
        assignUserId: assignUserId,
        selectedText: selectedText,
      },
      success: function success(response) {
        $("#task-assign").text(response.data);
        $("#assignee" + task_id + "").text(response.data);
        $("#task_assignee_modal").modal("hide");
        $("#assign_to_user_select").val(assignUserId);
        toastr.success(response.error_msg);
        $("#task_view").modal("hide");
        getTaskListView();
        getLogList();
      },
      error: function error(_error) {
        toastr.error(_error.responseJSON.error_msg);
      },
    });
  }
}
$(document).ready(function () {
  var dateToday = new Date();
  $(".datepicker").datepicker({
    minDate: dateToday,
    dateFormat: "mm/dd/yy",
  });

  $("#task_due_date_id_date").datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "MM/DD/YYYY hh:mm:ss A",
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

function changeDate() {
  count = 0;
  $("#task_due_date_id_error").html("");
  var task_id = $("#hdn_task_id").val();
  var due_date = $("#task_due_date_id").val();
  var startDate = $("#start_date" + task_id).text();

  var startDateCon = moment(startDate, "MM/DD/YYYY hh:mm:ss A");
  var dueDateConv = moment(due_date, "MM/DD/YYYY hh:mm:ss A");

  if (due_date == "") {
    count++;
    $("#task_due_date_id_error").html("Please select due date");
    return false;
  } else if (startDateCon > dueDateConv) {
    $("#task_due_date_id_error").html(
      "Start Date cannot be later than Due Date."
    );
    count++;
    return false;
  }
  if (count == 0) {
    $.ajax({
      url: TASK_DUE_DATE,
      dataType: "json",
      type: "POST",
      data: {
        id: task_id,
        due_date: due_date,
        _token: CSRF_TOKEN,
      },
      success: function (datalist) {
        $("#due-date").text(datalist.TaskDetails.due_date);
        $("#due_date" + task_id).text(datalist.TaskDetails.due_date);
        toastr.success(datalist.error_msg);
        $("#task_due_date_modal").modal("hide");
      },
    });
  }
}

function flagTaskChange(id) {
  $.confirm({
    title: "Flag",
    columnClass: "col-md-6",
    content: function () {
      var html =
        '<b><label for="reason">Reason:</label><b>' +
        '<textarea style="margin-bottom: 0 !important; width: 100%;" name="reason" id="reason_task" spellcheck="false"></textarea></div>';
      return html;
    },
    buttons: {
      confirm: {
        text: "Confirm",
        btnClass: "btn-primary",
        action: function () {
          var reason_task = this.$content.find("#reason_task").val();
          $.ajax({
            global: false,
            url: FLAG_TASK,
            type: "GET",
            data: {
              _token: CSRF_TOKEN,
              id: id,
              reason: reason_task,
            },
            success: function (response) {
              toastr.success(response.error_msg);
              const path = window.location.pathname;
              if (path == "/hub-record/task-record") {
                getTaskListView();
              } else {
                getTask();
              }
            },
            error: function (xhr, status, error) {
              toastr.error(xhr.responseJSON.error_msg);
            },
          });
        },
      },
      cancel: function () {},
    },
  });
}
function getTask() {
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
$("#searchbtns").click(function () {
  $("#search-filter-btn").slideToggle(600);
});

function openModal(response_id) {
  $("#task_view").modal("show");
  $("#hdn_task_id").val(response_id);

  $.ajax({
    url: TASK_AJAX,
    dataType: "json",
    type: "GET",
    data: {
      id: response_id,
    },
    success: function (datalist) {
      $("#task_id").text("#" + response_id);
      $("#created-by").text(
        datalist.data.first_name + " " + datalist.data.last_name
      );
      $("#due-date").text(datalist.data.due_date);
      $("#created-at").text(", " + datalist.data.created_date);
      $("#task-title").html("<b>" + datalist.data.task_name + "</b>");
      $("#task_description_model").text(datalist.data.task_description);
      $("#task-assign").text(
        datalist.data.assignFname + " " + datalist.data.assignLnamae
      );
      $("#status-name").text(datalist.data.task_status);
      $("#assign_to_user_select").val(datalist.data.assign_id);
      $("#task_title_id").val(datalist.data.task_name);
      $("#priority-name").text(datalist.data.priority);
      $("#task_due_date_id").val(
        moment(datalist.data.due_date).format("MM/DD/YYYY")
      );
      $("#task-description-text").html(datalist.data.task_description);
      if (datalist.data.task_status == "Pending") {
        $(".statusBtn").addClass("badge-warning");
      } else if (datalist.data.task_status == "Urgent") {
        $(".statusBtn").addClass("badge-danger");
      } else if (datalist.data.task_status == "Outstanding") {
        $(".statusBtn").addClass("badge-success");
      } else if (datalist.data.task_status == "Completed") {
        $(".statusBtn").addClass("badge-info");
      }

      if (datalist.data.priority == "High") {
        $(".prioBtn").addClass("badge-danger");
      } else if (datalist.data.priority == "Medium") {
        $(".prioBtn").addClass("badge-info");
      } else if (datalist.data.priority == "Low") {
        $(".prioBtn").addClass("badge-success");
      }
      $("#task-assign-id").val(datalist.data.assign_id);
      $("#comment_error").html("");
      $("#task_comment").val("");
      getLogList();
      commentList();
      getTimeLogList();
      $(".loader").attr("style", "display:none;");
      $(".showResult").attr("style", "display:block;");

      $(".clock_in").addClass("hide");
      $(".clock_out").addClass("hide");
      $(".clock_in_div").addClass("hide");

      if (datalist.data.clockInFlag == 1) {
        $(".clock_in_div").removeClass("hide");
        if (datalist.data.clockinOut == 1) {
          $(".clock_out").removeClass("hide");
        } else {
          $(".clock_in").removeClass("hide");
        }
      }
    },
  });
}

function openDueDateModal() {
  $("#task_due_date_modal").modal("show");
  $("#task_due_date_id_error").html("");
  $("#task_due_date_id").val($("#due-date").text());
}

function openAssigneeModal() {
  $("#task_assignee_modal").modal("show");
  $("#assign_user_id_error").html("");
  $("#assign_to_user_select").val($("#task-assign-id").val()).trigger("change");
}

function openDescriptionModal() {
  $("#task_description_modal_view").modal("show");
  $("#task_description_model_err").html("");
  $("#task_description_model").val($("#task-description-text").text());
}

$("#task_due_date_modal").on("show.bs.modal", function () {
  $("#task_view").css("z-index", 1039);
});

$("#task_due_date_modal").on("hidden.bs.modal", function () {
  $("#task_view").css("z-index", 1041);
});

$("#task_assignee_modal").on("show.bs.modal", function () {
  $("#task_view").css("z-index", 1039);
});

$("#task_assignee_modal").on("hidden.bs.modal", function () {
  $("#task_view").css("z-index", 1041);
});

$("#task_description_modal_view").on("show.bs.modal", function () {
  $("#task_view").css("z-index", 1039);
});

$("#task_description_modal_view").on("hidden.bs.modal", function () {
  $("#task_view").css("z-index", 1041);
});

$("#task_title_modal").on("show.bs.modal", function () {
  $("#task_view").css("z-index", 1039);
});

$("#task_title_modal").on("hidden.bs.modal", function () {
  $("#task_view").css("z-index", 1041);
});

function saveDescription() {
  var task_id = $("#hdn_task_id").val();
  var description = $("#task_description_model").val();
  count = 0;
  if (description.trim() == "") {
    count++;
    $("#task_description_model_err").html("Please enter description");
    return false;
  }
  if (count == 0) {
    $.ajax({
      url: TASK_DESCRIPTION_UPDATE,
      dataType: "json",
      type: "POST",
      data: {
        id: task_id,
        task_description: description,
        _token: CSRF_TOKEN,
      },
      success: function (datalist) {
        toastr.success(datalist.error_msg);
        getLogList();
        $("#task-description-text").html(description);
        $("#task_description_modal_view").modal("hide");
      },
    });
  }
}

function openTitleModal() {
  $("#task_title_modal").modal("show");
  $("#task_title_id_error").html("");
  $("#task_title_id").val($("#task-title").text());
}

function saveTaskTitle() {
  var task_id = $("#hdn_task_id").val();
  var title = $("#task_title_id").val();
  var count = 0;
  $("#task_title_id_error").html("");
  if (title.trim() == "") {
    count++;
    $("#task_title_id_error").html("Please enter title");
    return false;
  } else if (title.length > 70) {
    count++;
    $("#task_title_id_error").html("Title should be less than 70 characters.");
  }
  if (count == 0) {
    $.ajax({
      url: TASK_TITLE_UPDATE,
      dataType: "json",
      type: "POST",
      data: {
        id: task_id,
        task_name: title,
        _token: CSRF_TOKEN,
      },
      success: function (datalist) {
        toastr.success(datalist.error_msg);
        $("#task-title").html("<b>" + title + "</b>");
        $("#task" + task_id).html(title);
        $("#task_title_modal").modal("hide");
        getLogList();
      },
    });
  }
}

function getChangePriorityById(priority) {
  var response_id = $("#hdn_task_id").val();
  $.ajax({
    type: "POST",
    url: TASK_PRIORITY_CHANGE,
    data: {
      id: response_id,
      priority: priority,
      _token: CSRF_TOKEN,
    },
    success: function (res) {
      if (res.status == 1) {
        toastr.success(res.error_msg);
        if (res.TaskDetails.priority == "High") {
          $("#priority-name").html("High");
          $("#priority" + response_id + "").html(
            '<span class="badge badge-outline-warning">High</span>'
          );
          $(".prioBtn")
            .addClass("badge-warning")
            .removeClass("badge-danger badge-success badge-info");
        }
        if (res.TaskDetails.priority == "Medium") {
          $("#priority-name").html("Medium");
          $("#priority" + response_id + "").html(
            '<span class="badge badge-outline-danger">Medium</span>'
          );
          $(".prioBtn")
            .addClass("badge-danger")
            .removeClass("badge-warning badge-success badge-info");
        }
        if (res.TaskDetails.priority == "Low") {
          $("#priority-name").html("Low");
          $("#priority" + response_id + "").html(
            '<span class="badge badge-outline-success">Low</span>'
          );
          $(".prioBtn")
            .addClass("badge-success")
            .removeClass("badge-warning badge-danger badge-info");
        }
        getLogList();
      } else {
        toastr.error(res.error_msg);
      }
    },
    error: function error(_error) {
      toastr.error(_error.responseJSON.message);
    },
  });
}

$(".modal").on("shown.bs.modal", function () {
  $(".bottom-navbar").css("position", "absolute");
});

$(".modal").on("hidden.bs.modal", function () {
  $(".bottom-navbar").attr("style", ""); // or whatever the original was
});

function clockIn(type) {
  var id = $("#hdn_task_id").val();
  $.ajax({
    method: "GET",
    url: CLOCK_IN_OUT,
    data: {
      id: id,
      type: type,
      auth_id: AUTH,
      _token: CSRF_TOKEN,
    },

    success: function success(response) {
      if (response.data.type == "clock_in") {
        $("#clock_in").addClass("hide");
        $("#clock_out").removeClass("hide");
      } else {
        $("#task_hour_id").html("");
        $("#task_hour_id").html(response.data.task_hour);
        $("#clock_in").removeClass("hide");
        $("#clock_out").addClass("hide");
      }
      getLogList();
      getTimeLogList();
      toastr.success(response.error_msg);
    },
    error: function error(_error) {
      toastr.error(_error.responseJSON.message);
    },
  });
}

function getTaskListView(page = "") {
  $("#task_list").html("");
  var taskName = $("#task_name").val();
  var userId = $("#user_id").val();
  var status = $("#status").val();
  var task_due_date = $("#task_due_date").val();
  var created_user_id = $("#created_user_id").val();
  var created_task_date = $("#created_task_date").val();
  var priority = $("#priority").val();
  $(".location-wise-data-loader").attr("style", "display:block;");
  var page = page;
  const params = new URLSearchParams(window.location.search);
  const pending_task = params.get("pending-task") ?? "";
  const path = window.location.pathname;
  if (path == "/tasks/task-list" || path == "/hub-record/task-record") {
    $.ajax({
      type: "POST",
      url: TASK_AJAX_LIST,
      data: {
        task_name: taskName,
        user_id: userId,
        status: status,
        task_due_date: task_due_date,
        created_user_id: created_user_id,
        created_task_date: created_task_date,
        priority: priority,
        _token: CSRF_TOKEN,
        page: page,
        pending_task: pending_task,
      },
      success: function (res) {
        $("#task_list").html(res);
        $(".location-wise-data-loader").attr("style", "display:none;");
      },
    });
  }
}

function refresh() {
  document.getElementById("formsubmit").reset();
  getTaskListView();
}

function getExport() {
  var taskName = $("#task_name").val();
  var userId = $("#user_id").val();
  var status = $("#status").val();
  var task_due_date = $("#task_due_date").val();
  var created_user_id = $("#created_user_id").val();
  var created_task_date = $("#created_task_date").val();
  var priority = $("#priority").val();
  $.ajax({
    url: TASK_EXPORT,
    type: "get",
    data: {
      task_name: taskName,
      user_id: userId,
      status: status,
      task_due_date: task_due_date,
      created_user_id: created_user_id,
      created_task_date: created_task_date,
      priority: priority,
    },
    success: function (response) {
      var blob = new Blob([response]);
      if (response.status == 0) {
        toastr.error("Please check there is no data to export.");
        return false;
      } else {
        var link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        var form_name = "task_" + _DATE_TIME;
        link.download = form_name + ".csv";
        link.click();
      }
    },
  });
}

$("#view_task_modal_close").click(function () {
  getTaskListView();
});

var dateToday = new Date();
$("#start_date").datepicker({
  minDate: dateToday,
  buttonImage: "assets/css/vertical-layout-light/imagesui-icons_222222_256x240",
});

$("#due_date_task_model_id_date").datetimepicker({
  allowInputToggle: true,
  showClose: true,
  showClear: true,
  showTodayButton: true,
  format: "MM/DD/YYYY hh:mm:ss A",
});

function taskAdd() {
  var task_name_id = $("#task_name_id").val();
  var assign_to_id = $("#assign_to_id").val();
  var dueDate = $("#due_date_task_model_id").val();
  var startDate = $("#start_date").val();
  var priority = $("#task_priority").val();
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

  var startDateCon = moment(startDate, "MM/DD/YYYY hh:mm:ss A");
  var dueDateConv = moment(dueDate, "MM/DD/YYYY hh:mm:ss A");
  if (startDateCon > dueDateConv) {
    $("#start_date_error").html("Start Date cannot be later than Due Date.");
    cnt = 1;
  }
  if (cnt == 1) {
    return false;
  } else {
    $("#submitTaskAdd").attr("disabled", true);
    var forn = $("#hub_task_patient_id")[0];
    var formData = new FormData(forn);
    formData.append("_token", CSRF_TOKEN);

    $.ajax({
      url: _TASK,
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
          getTaskListView();
          $("#submitTaskAdd").attr("disabled", false);
        } else {
          toastr.error(res.error_msg);
          $("#submitTaskAdd").attr("disabled", false);
        }
      },
    });
  }
}

function deleteTask(id, hub_record_id='') {
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
              _token: CSRF_TOKEN,
              _method: "DELETE",
              id: id,
              hub_record_id: hub_record_id,
            },
            success: function (res) {
              if (res == 1) {
                toastr.success("Task successfully deleted");
                getTaskListView();
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

$(document).on(
  "click",
  ".task-list-pagination .pagination a",
  function (event) {
    $("li").removeClass("active");
    $(this).parent("li").addClass("active");
    event.preventDefault();
    var myurl = $(this).attr("href");
    var page = $(this).attr("href").split("page=")[1];
    getTaskListView(page);
  }
);
