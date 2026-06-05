$(function () {
  $(".wmd-view-topscroll").scroll(function () {
    $(".wmd-view").scrollLeft($(".wmd-view-topscroll").scrollLeft());
  });
  $(".wmd-view").scroll(function () {
    $(".wmd-view-topscroll").scrollLeft($(".wmd-view").scrollLeft());
  });
  loadHubFlagList(1);
});

$(".searchAppoinment").on("click", function () {
  loadHubFlagList(1);
});

$(function () {
  var start = moment().subtract(0, "days");
  var end = moment();
  $(".datepickernn").daterangepicker(
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
      $(".datepickernn").val(
        chosen_date.format("MM/DD/YYYY") + " - " + end_date.format("MM/DD/YYYY")
      );
    }
  );

  $(".dob").daterangepicker(
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
      $(".dob").val(
        chosen_date.format("MM/DD/YYYY") + " - " + end_date.format("MM/DD/YYYY")
      );
    }
  );
});

function loadHubFlagList(page) {
  var formsubmit = $("#formsubmit").serialize();
  $("#loaderDashboardGraph").attr("style", "display:flex");
  $("#resp").html("");
  $.ajax({
    url: _FLAG_HUB_LIST + "?page=" + page,
    type: "get",
    data: formsubmit,
    success: function (response) {
      $("#resp").html(response);
      $("#loaderDashboardGraph").attr("style", "display:none");
    },
  });
}

$("body").on("click", ".pagination a", function (event) {
  $("li").removeClass("active");
  $(this).parent("li").addClass("active");
  event.preventDefault();
  var page = $(this).attr("href").split("page=")[1];
  loadHubFlagList(page);
});

var urlToken = _SEARCH_PATIENT_LIST;
var empId = "";
var empName = "";
$("#created_by_ny").tokenInput(_SEARCH_PATIENT_LIST, {
  tokenLimit: 1,
  zindex: 9999,
  prePopulate:
    empId !== "" && empName !== "" ? [{ id: empId, name: empName }] : [],
  onAdd: function (item) {
    $("#created_by_ny_id").val(item.id);
    $("#created_by_ny_name").val(item.name);
  },
  onDelete: function (item) {
    $("#created_by_ny_id").val("");
    $("#created_by_ny_name").val("");
  },
});

function loadDocFlagList(page) {
  $("#loaderDocGraph").attr("style", "display:flex");
  $("#doc_resp").html("");
  $.ajax({
    url: _FLAG_DOC_LIST + "?page=" + page,
    type: "get",
    success: function (response) {
      $("#doc_resp").html("");
      $("#doc_resp").html(response);
      $("#loaderDocGraph").attr("style", "display:none");
    },
  });
}

function loadTaskFlagList(page) {
  console.log("sdsad");
  $("#loaderTaskGraph").attr("style", "display:flex");
  $("#task_resp").html("");
  $.ajax({
    async: false,
    global: false,
    url: _FLAG_TASK_LIST + "?page=" + page,
    type: "get",
    success: function (response) {
      $("#task_resp").html("");
      $("#task_resp").html(response);
      $("#loaderTaskGraph").attr("style", "display:none");
    },
  });
}

function loadNotesFlagList(page) {
  $("#loaderNotesGraph").attr("style", "display:flex");
  $("#notes_resp").html("");
  $.ajax({
    url: _FLAG_NOTES_LIST + "?page=" + page,
    type: "get",
    success: function (response) {
      $("#notes_resp").html(response);
      $("#loaderNotesGraph").attr("style", "display:none");
    },
  });
}

var $container = $(".js-tabs").parent();
var $tabsToggleGroup = $(".tabs__toggle-group");
var $tabs = $(".tabs__toggle");
var $activeTab = $(".tabs__toggle--active");
var $tabContents = $(".tabs__tab");

var $scrollLeft = $(".js-action--scroll-left");
var $scrollRight = $(".js-action--scroll-right");

var btnScrollLeft = document.querySelector(".js-action--scroll-left");
var btnScrollRight = document.querySelector(".js-action--scroll-right");
var tabsContainer = document.querySelector(".tabs__toggle-group");
var tabs = document.querySelectorAll(".tabs__toggle");
var selectedTabIndex = 0;
var scrollIndex = 0;
var scrollWidth = tabs[0].clientWidth + 5;
var scrollLeft = 0;

// var tabsContainer = document.querySelector('.tabs__toggle-group');
var tabsContainerWidth = tabsContainer.clientWidth;
var tabsScrollableWidth = tabsContainer.scrollWidth;

if (tabsScrollableWidth > tabsContainerWidth) {
}

$tabContents.hide().eq($tabs.index($activeTab)).show();

function addTab(tabName) {
  var tabToggleGroup = document.querySelector(".tabs__toggle-group");
  var tabsGroup = document.querySelector(".tabs__tabs-group");
  var newTabToggle = document.createElement("div");
  var newTab = document.createElement("div");

  newTabToggle.classList.add("tabs__toggle");
  newTabToggle.innerText = tabName;
  tabToggleGroup.appendChild(newTabToggle);

  newTab.classList.add("tabs__tab");
  newTab.innerText = tabName + " Content";
  newTab.style.display = "none";
  tabsGroup.appendChild(newTab);
}

$tabs.on("click", function () {
  var $tabs = $(".tabs__toggle");
  var $activeTab = $(".tabs__toggle--active");
  var $tabContents = $(".tabs__tab");
  var $tab = $(this);
  var tabIndex = $tabs.index($tab);

  selectedTabIndex = tabIndex;

  $tab.addClass("tabs__toggle--active");
  $activeTab.removeClass("tabs__toggle--active");
  $activeTab.children("div").addClass("active");

  $tabContents.hide().eq(tabIndex).show();

  var tab = $tab[0];
  var tabWidth = tab.clientWidth;
  // var tabLeft = tab.offsetLeft;
  var tabLeft = tabsContainer.scrollLeft;
  var tabRight = tabLeft + tabWidth;
  // var

  if (tabLeft < tabsContainer.scrollLeft) {
    smoothScroll(tabsContainer, {
      to: tabLeft,
      callback: checkScrollButtonState,
    });
  }

  // if (tabRight > (tabsContainerWidth - tabsContainer.scrollLeft)) {
  if (tabRight > tabsContainer.scrollLeft + tabsContainerWidth) {
    smoothScroll(tabsContainer, {
      to: tabRight - tabsContainerWidth,
      callback: checkScrollButtonState,
    });
  }
});

if (tabsContainer.scrollLeft === 0) {
  //	btnScrollLeft.setAttribute("disabled", true);
}

// btnScrollLeft.addEventListener('click', function () {

// 	scrollIndex--;
// 	if (tabs[scrollIndex] != undefined) {
// 		scrollLeft -= tabs[scrollIndex].clientWidth + 50;
// 	}else{
// 		scrollLeft -= 50
// 	}

// 	smoothScroll(tabsContainer, { to: scrollLeft, callback: checkScrollButtonState });
// });

// btnScrollRight.addEventListener('click', function () {
// 	if (tabs[scrollIndex] != undefined) {
// 		scrollLeft += tabs[scrollIndex].clientWidth + 50;
// 	}else{
// 		scrollLeft += 50
// 	}

// 	scrollIndex++;

// 	smoothScroll(tabsContainer, { to: scrollLeft, callback: checkScrollButtonState });

// });

function checkScrollButtonState() {
  if (tabsContainer.scrollLeft <= 0) {
    //btnScrollLeft.setAttribute("disabled", true);
  } else {
    //btnScrollLeft.removeAttribute("disabled");
  }

  if (
    tabsContainer.scrollLeft + tabsContainer.clientWidth + 50 >=
    tabsContainer.scrollWidth
  ) {
    //btnScrollRight.setAttribute("disabled", true);
  } else {
    //btnScrollRight.removeAttribute("disabled");
  }
}

function smoothScroll(element, options) {
  var requestAnimFrame = (function () {
    return (
      window.requestAnimationFrame ||
      window.webkitRequestAnimationFrame ||
      window.mozRequestAnimationFrame ||
      function (callback) {
        window.setTimeout(callback, 1000 / 60);
      }
    );
  })();

  var defaults = {
    to: 0,
    duration: 250,
    axis: "horizontal",
    easing: "easeInOutQuad",
  };

  var settings = Object.assign({}, defaults, options);

  var direction = settings.axis === "horizontal" ? "scrollLeft" : "scrollTop";

  var start = element[direction],
    change = settings.to - start,
    animationStart = +new Date();
  var animating = true;

  // Check if already at target position
  if (start === settings.to) {
    if (settings.callback) settings.callback();
    return;
  }

  var animateScroll = function () {
    if (!animating) {
      if (settings.callback) settings.callback();
      return;
    }
    requestAnimFrame(animateScroll);

    var now = +new Date();
    var elapsed = now - animationStart;

    // Apply easing
    var val = Math.floor(
      Easing[settings.easing](elapsed, start, change, settings.duration)
    );

    // Ensure element scroll position is updated
    element[direction] = val;

    // Stop if the animation duration is reached
    if (
      elapsed >= settings.duration ||
      Math.abs(element[direction] - settings.to) < 1
    ) {
      element[direction] = settings.to; // Correct final position
      animating = false;
    }
  };
  requestAnimFrame(animateScroll);
}

/**
 * Easing Functions - inspired from http://gizma.com/easing/
 */
Easing = {
  // no easing, no acceleration
  linear: function (t, b, c, d) {
    return (c * t) / d + b;
  },

  // accelerating from zero velocity
  easeInQuad: function (t, b, c, d) {
    t /= d;
    return c * t * t + b;
  },

  // decelerating to zero velocity
  easeOutQuad: function (t, b, c, d) {
    t /= d;
    return -c * t * (t - 2) + b;
  },

  // acceleration until halfway, then deceleration
  easeInOutQuad: function (t, b, c, d) {
    t /= d / 2;
    if (t < 1) return (c / 2) * t * t + b;
    t--;
    return (-c / 2) * (t * (t - 2) - 1) + b;
  },

  // accelerating from zero velocity
  easeInCubic: function (t, b, c, d) {
    t /= d;
    return c * t * t * t + b;
  },

  // decelerating to zero velocity
  easeOutCubic: function (t, b, c, d) {
    t /= d;
    t--;
    return c * (t * t * t + 1) + b;
  },

  // acceleration until halfway, then deceleration
  easeInOutCubic: function (t, b, c, d) {
    t /= d / 2;
    if (t < 1) return (c / 2) * t * t * t + b;
    t -= 2;
    return (c / 2) * (t * t * t + 2) + b;
  },

  // accelerating from zero velocity
  easeInQuart: function (t, b, c, d) {
    t /= d;
    return c * t * t * t * t + b;
  },

  // decelerating to zero velocity
  easeOutQuart: function (t, b, c, d) {
    t /= d;
    t--;
    return -c * (t * t * t * t - 1) + b;
  },

  // acceleration until halfway, then deceleration
  easeInOutQuart: function (t, b, c, d) {
    t /= d / 2;
    if (t < 1) return (c / 2) * t * t * t * t + b;
    t -= 2;
    return (-c / 2) * (t * t * t * t - 2) + b;
  },

  // accelerating from zero velocity
  easeInQuint: function (t, b, c, d) {
    t /= d;
    return c * t * t * t * t * t + b;
  },

  // decelerating to zero velocity
  easeOutQuint: function (t, b, c, d) {
    t /= d;
    t--;
    return c * (t * t * t * t * t + 1) + b;
  },

  // acceleration until halfway, then deceleration
  easeInOutQuint: function (t, b, c, d) {
    t /= d / 2;
    if (t < 1) return (c / 2) * t * t * t * t * t + b;
    t -= 2;
    return (c / 2) * (t * t * t * t * t + 2) + b;
  },
};

function makeFlagRead(id, record_id, type) {
  $.ajax({
    url: _FLAG_MARK_LIST,
    type: "post",
    data: {
      id: id,
      _token: _CSRF_TOKEN,
    },
    success: function (response) {
      var urlRedirection = "/hub-record/view/" + record_id;
      if (type == "Task") {
        urlRedirection = "/hub-record/view/" + record_id;
      }
      window.location.href = urlRedirection;
    },
  });
}

function resetHubRecords() {
  $("#formsubmit")[0].reset();
  $("#agency_fk").val("").trigger("change");
  $("#created_by_ny").tokenInput("clear");
  loadHubFlagList(1);
}
