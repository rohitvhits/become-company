<style>
  .rightCorner {

    display: flex;
    justify-content: center;

  }
  /* Responsive Announcement Modal Styles */
  #announcementPopupModal .modal-dialog {
    max-width: 800px;
    margin: 10px auto;
  }
  #announcementPopupModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
  }
  #announcementPopupModal .media-gallery img,
  #announcementPopupModal .media-gallery video {
    width: 100%;
    max-width: 350px;
    height: auto;
  }
  @media (max-width: 768px) {
    #announcementPopupModal .modal-dialog {
      max-width: 95%;
      margin: 10px auto;
    }
    #announcementPopupModal .modal-body {
      max-height: 60vh;
      padding: 10px 15px !important;
    }
    #announcementPopupModal .modal-header {
      padding: 15px !important;
    }
    #announcementPopupModal .modal-header h5 {
      font-size: 16px !important;
      flex-wrap: wrap;
    }
    #announcementPopupModal .modal-footer {
      padding: 10px 15px !important;
      flex-direction: column;
      gap: 10px;
    }
    #announcementPopupModal .modal-footer>div {
      width: 100%;
      text-align: center;
    }
    #announcementPopupModal .media-gallery {
      flex-direction: column;
      align-items: center;
    }
    #announcementPopupModal .media-gallery img,
    #announcementPopupModal .media-gallery video {
      max-width: 100%;
    }
    #announcementPopupModal .btn {
      width: 100%;
      margin-bottom: 5px;
    }
  }
  @media (max-width: 480px) {
    #announcementPopupModal .modal-body {
      max-height: 50vh;
    }
    #announcementPopupModal .badge {
      display: block;
      margin-left: 0 !important;
      margin-top: 5px;
    }
  }
</style>
<footer class="footer">
  <div class="d-sm-flex justify-content-center justify-content-sm-between">
    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"> 2019 - {{ date('Y')}} &copy; NY BEST MEDICAL. <a target="_blank" href="{{ url('/term-condition')}}">Terms And Condition</a> And <a target="_blank" href="{{ url('/privacy-policy')}}">Privacy Policy</a></span>
  </div>
</footer>
<!-- partial -->
</div>
<!-- main-panel ends -->
</div>
<!-- page-body-wrapper ends -->
</div>
<a data-toggle="modal" data-target="#myDueTaskModal" data-whatever="@mdo" id="due_task_ids" title=""></a>

<div class="modal fade" id="myDueTaskModal" tabindex="-1" aria-labelledby="ModalLabel" aria-modal="true" style="padding-right: 17px;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalLabel"> Due Task List </h5>

      </div>

      <div class="modal-body">
        <div class="row">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <th>Record Id</th>
                <th>Task Name</th>
                <th>Start Date</th>
                <th>Due Date</th>
                <th>Status</th>

              </thead>
              <tbody id="my-due-task-table">

              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- container-scroller -->
<div class="modal fade" id="pendingTaskModel" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Overdue Task</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <h5>Your Overdue Tasks : <b>{{$taskList}}</b></h5>
          </div>
        </div>
        <div class="modal-footer">
          <a href="{{url('tasks/task-list?pending-task=Pending')}}" target="_blank" class="btn btn-success">Go To Task List</a>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="l1oadModalPopupNotification1" tabindex="-1" aria-labelledby="ModalLabel" aria-modal="true" style="padding-right: 17px;">
  <div class="modal-dialog modal-dd">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Important </h5>
        <div class="rightCorner"></div>

      </div>

      <div class="modal-body">
        <p><img src="<?= URL::to('Skype_Picture_2024_10_18T18_03_32_440Z.jpeg') ?>" style="width: 100%;" alt=""/></p>
        
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="loadModalPopupNotification" tabindex="-1" aria-labelledby="ModalLabel" aria-modal="true" style="padding-right: 17px;">
  <div class="modal-dialog modal-dd">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          NY Best Medical is Opening Our Most Advanced Location Yet in the Bronx! 🚨

        </h5>
        <div class="rightCorner"></div>

      </div>

      <div class="modal-body">
        <p><img alt="" src="<?= URL::to('Skype_Picture_2024_11_15T20_16_39_758Z.jpeg') ?>" style="width: 100%;" /></p>
        <p>On November 18, 2024, we’re thrilled to open our largest, most innovative location in Bronx, New York!
          This new hub is designed to elevate the Home Care industry by offering advanced occupational health, primary care, caregiver training, and onboarding services—all under one roof.</p>

        <p>Supporting and Elevating the Home Care Industry</p>

        <p>This new Bronx location is our commitment to raising healthcare standards and empowering caregivers with top-tier resources. We’re grateful for the trust and partnership that the Home Care industry has placed in us, making this expansion possible. Here’s what this facility will bring to the community:</p>
        <p>Occupational Health Services

          • Comprehensive health assessments for a safe and productive workforce
          • Injury prevention and recovery programs tailored to caregivers
          • Onsite vaccinations and screenings to promote wellness and compliance</p>

        <p>✨ Primary Care for All

          • Accessible primary care focused on preventive measures for patients and caregivers alike
          • Holistic, patient-centered care that supports well-being for all
          • Dedicated providers who understand the unique needs of caregivers</p>

        <p>✨ Advanced Caregiver Training

          • Hands-on workshops to build essential skills and confidence
          • Training on the latest techniques, best practices, and industry standards
          • Certifications and ongoing education, keeping caregivers prepared and qualified</p>

        <p>✨ Efficient Onboarding for New Caregivers

          • Streamlined, compliant onboarding to support a smooth start
          • In-depth orientations on healthcare regulations and safety practices
          • Continuous support to ensure caregivers feel confident and ready to serve</p>

        <p>Grateful for the Industry’s Trust and Our Team’s Dedication

          Our growth would not be possible without the incredible opportunity the Home Care industry has given us to support caregivers and agencies across New York State. This Bronx expansion reflects the trust and support of an industry that improves lives every day, and we’re honored to serve the dedicated caregivers who make such a powerful difference.

          We’re also deeply grateful to our NY Best Medical management and staff for their tireless commitment to this project. Their vision and hard work have made this new location a reality, and it stands as a testament to their dedication to quality care. Thank you to our team, the Home Care industry, and everyone who has contributed to this milestone.

          Get ready, Bronx! A new era of healthcare excellence is coming on November 18, 2024!</p>

      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="loadModalEventPopupNotification" tabindex="-1" aria-labelledby="ModalLabel" aria-modal="true" style="padding-right: 17px;">
  <div class="modal-dialog modal-dd">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title event_popup_title">Notification</h5>
        <div class="rightCorner">
        </div>
      </div>
      <div class="modal-body">
        <p><img alt="" src="" id="event_popup_image" style="width: 100%;" /></p>
        <div class="content" id="event_popup_content"></div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="exampleModal-show-images" tabindex="-1" aria-labelledby="ModalLabel" aria-modal="true" style="padding-right: 17px;">
  <div class="modal-dialog" style="max-width:800px !important">
    <div class="modal-content">
      <div class="modal-header">
       

      </div>

      <div class="modal-body">
        <img alt="" src="{{ asset('view_all_images.jpeg')}}" style="width:700px">
      </div>

    </div>
  </div>
</div>

<!-- base:js -->
<script src="<?= URL::to('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
<!-- endinject -->
<!-- Plugin js for this page-->
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.pie.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jquery.flot/jquery.flot.resize.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.world.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/peity/jquery.peity.min.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery.flot.dashes.js') ?>"></script>
<script src="<?= URL::to('assets/js/jquery-ui.min.js') ?>"></script>
<!-- End plugin js for this page-->
<!-- inject:js -->
<script src="<?= URL::to('assets/js/off-canvas.js') ?>"></script>
<script src="<?= URL::to('assets/js/hoverable-collapse.js') ?>"></script>
<script src="<?= URL::to('assets/js/template.js') ?>"></script>
<script src="<?= URL::to('assets/js/settings.js') ?>"></script>
<script src="<?= URL::to('assets/js/todolist.js') ?>"></script>
<!-- endinject -->
<!-- endinject -->

<!-- plugin js for this page -->
<script src="<?= URL::to('assets/vendors/datatables.net/jquery.dataTables.js') ?>"></script>
<script src="<?= URL::to('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') ?>"></script>
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="<?= URL::to('assets/js/data-table.js') ?>"></script>
<!-- plugin js for this page -->
<!-- End plugin js for this page -->
<!-- Custom js for this page-->
<script src="<?= URL::to('assets/js/dashboard.js') ?>"></script>
<!-- End custom js for this page-->

<script src="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.js"></script>
<script src="<?php echo URL::to('/'); ?>/assets/jquery-confirmation/js/jquery-confirm.min.js"></script>
@php
    $auth = auth()->user();
    $cookieValue = Cookie::get('userLogin' . $auth->id);
    $cookieDate = $cookieValue ? date('Y-m-d', strtotime($cookieValue)) : null;
@endphp

<script>
    var globaCookiesData = null;

    @if($cookieDate)
        globaCookiesData = "{{ $cookieDate }}";
    @endif

    @if($cookieDate && $cookieDate <= '2024-11-10')
        // loadModalPopupNotification();
    @endif
</script>
<script>
  toastr.options.closeButton = true;
  toastr.options.tapToDismiss = false;
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "1000",
    "hideDuration": "500",
    "timeOut": "3000",
    "extendedTimeOut": 0,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut",
    "tapToDismiss": false
  };
  /*vishal d patel code end chat message listing*/

  function closeAdvertisement() {


    <?php
    $auth = auth()->user();


    Cookie::queue('userLogin' . $auth->id, '', 0);
    ?>
    var ctestst = "{{ Cookie::get('userLogin'.$auth->id)}}"

  }
</script>
<script type="text/javascript">
  function goPage(newURL) {

    if (newURL != "") {
      // if url is "-", it is this page -- reset the menu:
      if (newURL == "-") {
        resetMenu();
      }
      // else, send page to designated URL
      else {
        document.location.href = newURL;
      }
    }
  }
  // resets the menu selection upon entry to this page:
  function resetMenu() {
    document.gomenu.selector.selectedIndex = 2;
  }

  function getNotificationsUnread() {
    $.ajax({
      async: false,
      global: false,
      type: "GET",
      url: "<?php echo URL::to('/'); ?>/notification/unread-notification0",
      success: function(res) {
        if (res != 0) {
          // $('.countidsnewNo').addClass('count');
          // $('.countidsnewNo').html(res);
        } else {
          // $('.countidsnewNo').html('');
          // $('.countidsnewNo').removeClass('count');
        }
      }
    })
  }
  //insert view page log
  $(document).ready(function() {
    var CSRF_TOKEN = "{{ CSRF_TOKEN() }}";
    var id = "{{$id ??''}}";
    var menu = "{{$menu ?? ''}}";
    if (menu != "User Log" ) {
      $.ajax({
       
        type: "post",
        url: "{{URL::route('insert-view-logs') }}",
        data: {
          '_token': CSRF_TOKEN,
          "id": id,
          'module': menu,
          'link': window.location.href,

        },
        success: function(res) {
          if (res.status == "success") {

          }
        }
      })
    }
  });

  @can('task-list')
  var getCurrentUrl = "{{request()->segment(1)}}";
  var getTaskListData = "{{$taskList}}";
  if (getCurrentUrl != "task-list" && getTaskListData > 0) {
    $("#pendingTaskModel").modal("show");
  }
  setTimeout(function() {
    $('.alert').fadeOut('fast');
  }, 2000);
  @endcan

  $(".charCls").keypress(function(event) {
    var regex = new RegExp("^[a-zA-Z ]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
      event.preventDefault();
      return false;
    }
  });

  function notStartWithZero(phone) {
    var expr = /^[1-9]\d*$/;
    return expr.test(phone);
  };
  $(document).keydown(function(event) {
    if (event.ctrlKey == true && (event.which == '118' || event.which == '86')) {
      //  event.preventDefault();
    }
  });
</script>
@yield('js')
<!-- Session Flash Message -->
@if(Session::has('success'))
<script>
  Command: toastr["success"]('<?php echo Session::get('success') ?>')
</script>
@endif
@if(Session::has('error'))
<script>
  Command: toastr["error"]('<?php echo Session::get('error') ?>')
</script>
@endif
@if(Session::has('warning'))
<script>
  Command: toastr["warning"]('<?php echo Session::get('warning') ?>')
</script>
@endif
@if(Session::has('info'))
<script>
  Command: toastr["info"]('<?php echo Session::get('info') ?>')
</script>



@endif

<script> var showNotificationNew = ''; </script>
@if (Cookie::get('showNotification' . $auth->id) != "")
@if (date('Y-m-d', strtotime(Cookie::get('showNotification' . $auth->id))) <= date('Y-m-d'))
    <script>
      var newUserId = "{{ $auth->id}}";
      localStorage.setItem('notification_show'+newUserId,'1');
    </script>
@endif
@endif
<script>
  function dueTask() {

    $.ajax({
      async: false,
      global: false,
      type: "get",
      url: "{{url('tasks/my-due-task') }}",

      success: function(res) {
        var htmlResponse = "";
        var json = res.data;

        if (res.data.length != 0) {
          $.each(json, function(i, v) {
            htmlResponse += `<tr>
              <td><a href="{{ url('/patient/view')}}/${v.record_id}">${v.record_id}</a></td>
                <td>${v.task_name}</td>
                <td>${moment(v.start_date).format('MM/DD/YYYY')}</td>
                <td>${moment(v.due_date).format('MM/DD/YYYY hh:mm A')}</td>
                <td>${v.task_status}</td>
              </tr>`
          });

          $("#due_task_ids").click();;
          $('#my-due-task-table').html("");
          $('#my-due-task-table').html(htmlResponse)
        }

      }
    })
  }

  //setInterval(dueTask, 20000);
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-9EPHQQ3SF5"></script>


<script>
  @if(env('FILE_UPLOAD_PERMISSION') != 'development')
  var ISAWS = '1';
  @else
  var ISAWS = '0';
  @endif
</script>

<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());

  gtag('config', 'G-9EPHQQ3SF5');

  loadModalEventPopupNotification();

  function loadModalEventPopupNotification() {

    $.ajax({
      async: false,
      global: false,
      type: "GET",
      url: "{{ url('/get-active-event') }}",
      success: function(res) {
        popUpData = res.data;
        if (popUpData.length != 0) {
          $.each(popUpData, function(i, v) {
            if (ISAWS == 1) {
              var imageUrl = "{{ url('/event-image-show-aws') }}/" + v.id + "?type=event"
            } else {
              var imageUrl = "{{ asset('event-image') }}/" + v.image;
            }
            $(".event_popup_title").html(v.title);
            $("#event_popup_image").attr('src', imageUrl);
            $("#event_popup_content").html(v.content);


            let modalId = `eventModal_${i}`;
            let modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-labelledby="${modalId}Label" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="${modalId}Label">${v.title}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <img src="${imageUrl}" alt="${v.title}" class="img-fluid mb-3" />
                    <div>${v.content}</div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>`;

            // Append modal to body
            $("body").append(modalHtml);

            if (globaCookiesData != "") {
              if (globaCookiesData <= v.end_date) {
                $(`#${modalId}`).modal("show");

              }
            }
          })

        } else {

        }
      }
    })
  }

  let notification = 1;
  let isLoadingNoti = false;

  $('.notification_div').on('scroll', function() {
    let div = $(this);
    if ((div.scrollTop() + div.innerHeight() >= div[0].scrollHeight)) {
      notification++;
      isLoading = true;
      $('.noti-listing-loader1').attr('style', 'display:flex');
      getUnreadNotification();
    }
  });

  function getUnreadNotification(){
    $.ajax({
      async: false,
      global: false,
      type: "GET",
      url: "{{ url('/get-all-unread-user-notification') }}",
      data: {
        'page': notification
      },
      success: function(res) {
        if(res == '' && notification != 1){
          isLoadingNoti = false;
          notification = 0;
        }
        if(res == '' && notification == 1){
          html = `<div class="dropdown-item preview-item" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;" id="">
                <div class="card" style="width: 423px;border-radius: 10px;">
                    <div class="card-body row">
                        <div class="col-md-2" style="margin-left: -10px;">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-success">
                                <i class="mdi mdi-check-circle mx-0"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="border-bg-div">
                                <h6 class="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">You're all caught up! No notifications at the moment.</h6>
                            </div>
                        </div>
                    </div>
                </div>
                </div>`;
          $('.notification_div').html(html);
        }else{
          if (notification == 1) {
                $('.notification_div').html(res);
            } else {
                $('.notification_div').append(res);
                isLoadingNoti = false;
            }
        }
        
        $('.noti-listing-loader1').attr('style', 'display:none');
      }
    });
   
   
}

  function markAsRead(id,url)
  {
    $('.noti-listing-loader1').attr('style', 'display:flex');
    $.ajax({
      async: false,
      global: false,
      type: "post",
      data: {
        'id' : id,
        '_token': "{{csrf_token() }}",
      },
      url: "{{ url('/mark-as-read-notification') }}",
      success: function(res) {
        $('.noti-listing-loader1').attr('style', 'display:none');
        if (res != null) {
            if (typeof loadENotificationList === 'function') {
              toastr.success(res.error_msg);
              loadENotificationList(1);
            }else{
              window.location.replace(url);
            }
        }
        
      }
    })
  }

  countNotification();

function countNotification(){
  $.ajax({
    type: "post",
    data: {
      '_token': "{{csrf_token() }}",
    },
    url: "{{ url('/notification-count') }}",
    success: function(res) {
      if (res.data != null) {
        if(res.data == 1){
          let countNotifications = res.data;
          var userId = "{{ auth()->user()->id}}";
          var gsetNotification = localStorage.getItem('notification_show'+userId);
          if(gsetNotification == 1){
           
              $('.navbar-nav.navbar-nav-right li.notifications').addClass('show');
              $('.navbar-nav.navbar-nav-right li.notifications div').addClass('show');
              getUnreadNotification();
              showNotificationNew == 0;
              localStorage.setItem('notification_show'+userId,'0');
             
          }
          $('#count_notification').attr('style','');
          $('#count_notification').html('');
          $('#count_notification').addClass('count');
        }else{
          $('#count_notification').removeClass('count');
        }
      }
    }
});
}

//setInterval(countNotification, 15000);
</script>

<script>
  var _SEARCH_LOCATION_AJAX = "{{ url('/location-search-ajax-list')}}";
</script>
<script src="{{asset('assets/modulejs/location/location_search_list.js')}}?time={{ time()}}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY')}}&libraries=places,geometry,drawing,marker&callback=initAutocompleted&v=weekly" async defer></script>

<script>
  function initAutocompleted() {

    // For the right panel
    var inputRightPanel = document.getElementById('right-panel-ship-address');
    inputRightPanel.focus(); // Programmatically focus on the input

    if (!inputRightPanel) {
      
      return;
    }
    var autocompleteRightPanel = new google.maps.places.Autocomplete(inputRightPanel);
    google.maps.event.addListener(autocompleteRightPanel, 'place_changed', function() {
      var place = autocompleteRightPanel.getPlace();
      if (!place.geometry) {

        return;
      }

      var latitude = place.geometry.location.lat();
      var longitude = place.geometry.location.lng();

      searchData(latitude, longitude);
    });
  }

  function searchData(latitudeData, longitudeData) {
    $('.list-wrapper').html("");
    $('#loaderDashboardGraph').attr('style', 'display:block;margin-top: 31px;');
    $.ajax({
      async: false,
      global: false,
      type: "GET",
      url: _SEARCH_LOCATION_AJAX,
      data: {
        'latitude': latitudeData,
        'longitude': longitudeData,
        'appointment_type': $('#appointment_type').val()
      },
      success: function(res) {
        $('.list-wrapper').html("")
        $('.list-wrapper').html(res)
        setTimeout(() => {
          $('#loaderDashboardGraph').attr('style', 'display:none;margin-top: 31px;');
        }, 1000);

      }
    })
  }
 
</script>

@if(Cookie::get('userLogin' . $auth->id) != "")
    @if($auth->term_condition == 0 || $auth->privacy_policy == 0)
        <script>
            loadTermAndCondition();
        </script>
    @endif
@endif

  <script>
document.addEventListener('click', function(event) {
        // Get the target element and check if the click was outside it
        const targetElement = document.querySelector('.notifications');
        
        // If the click was outside the target element, call the function
        if (!targetElement.contains(event.target)) {
          $('.navbar-nav.navbar-nav-right li.notifications').removeClass('hide');
          $('.navbar-nav.navbar-nav-right li.notifications div').removeClass('show');
          $('.notification_div').html('');
          var userId = "{{ auth()->user()->id}}";
          localStorage.setItem('notification_show'+userId,'0');
          notification = 1;
        }
    });

    function loadTermAndCondition(){
      var termConditions = "{{ $auth->term_condition}}";
      var privacyPolicy = "{{ $auth->privacy_policy}}";
    

      $.confirm({
        title: "Terms & Conditions and Privacy Policy",
        type: 'blue',
        columnClass: 'col-md-9',
        content: function () {
            var self = this;

            return $.ajax({
                url: '{{ url("load-term-condition") }}',
                dataType: 'json',
                method: 'get'
            }).done(function (response) {
                var response = response.data;
                var label ="";
                var labelInput="";
                var privacyLabel="";
                var privacyInput = "";
                if(termConditions ==0){
                  label = "<span style='position: absolute;top: 10px;background: #00BBE0;padding: 1px 5px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;margin-left:10px'>New</span>";
                  labelInput =`<input type='checkbox' value="1"  id='termsCheckbox'>
                    <label for='termsCheckbox' style='font-size: 16px; font-weight: bold;'>I agree to the Terms & Conditions</label><br>`
                }

                if(privacyPolicy ==0){
                  privacyLabel = "<span style='position: absolute;top: 10px;background: #00BBE0;padding: 1px 5px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;margin-left:10px'>New</span>";
                  privacyInput =`<input type='checkbox' value="1" id='privacyCheckbox'>
                    <label for='privacyCheckbox' style='font-size: 16px; font-weight: bold;'>I agree to the Privacy Policy</label>
                    <br>`
                }
                // Create tab structure
                var contentHtml = `
                    <ul class="nav nav-tabs" id="termsTabs">
                        <li class="nav-item" style="width:200px">
                            <a class="nav-link active" id="terms-tab" data-toggle="tab" href="#terms-content">Terms & Conditions ${label}</a>
                        </li>
                        <li class="nav-item"  style="width:200px">
                            <a class="nav-link" id="privacy-tab" data-toggle="tab" href="#privacy-content">Privacy Policy  ${privacyLabel}</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="terms-content">
                        <p class="text-muted">Last Updated :${moment(response.term.updated_at).format('MM/DD/YYYY h:mm A')}</p>
                        ${response.term.message}</div>
                        <div class="tab-pane fade" id="privacy-content">
                        <p class="text-muted">Last Updated :${moment(response.privacy.updated_at).format('MM/DD/YYYY h:mm A')}</p>
                        ${response.privacy.message}</div>
                    </div>
                    <br>
                      ${labelInput}
                      ${privacyInput}

                    <span class='error-message' style='color: red; font-size: 14px;' id='terms_error'></span>
                `;

                self.setContent(contentHtml);
            }).fail(function () {
                self.setContent('Something went wrong.');
            });
        },
        buttons: {
            submit: {
                text: 'Agree',
                btnClass: 'btn-blue',
                action: function () {
                  var termsChecked =true;
                  var privacyChecked = true;
                  if(privacyPolicy ==0){
                    var privacyChecked = this.$content.find('#privacyCheckbox').prop('checked');
                  }
            
                  if(termConditions ==0){
                    var termsChecked = this.$content.find('#termsCheckbox').prop('checked');
                  }

                  if (!termsChecked || !privacyChecked) {
                    if(privacyPolicy ==0 && termConditions ==0){
                      $('#terms_error').html("Please accept both Terms & Conditions and Privacy Policy.");
                    }
                    if(privacyPolicy ==0){
                      $('#terms_error').html("Please accept Privacy Policy.");
                    }
                    if(termConditions ==0){
                      $('#terms_error').html("Please accept Terms & Conditions.");
                    }
                    return false; // Prevent modal from closing
                  }

                    $.ajax({
                        type: "post",
                        url: "{{ url('save-term-condition') }}",
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'termsChecked':$('#termsCheckbox:checked').val(),
                            'privacyChecked':$('#privacyCheckbox:checked').val(),
                        },
                        success: function (res) {
                          toastr.success('Terms & Conditions and Privacy Policy successfully recorded')
                        },
                        error:function(jqr){
                          showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel',
                action: function () {
                    // Do nothing
                }
            }
        }
    });

    }

    function showErrorAndLoginRedirection(xhr) {
      if (xhr.status === 401) {

          let countdown = 10;

          // Force toastr to stay visible (no auto close)
          toastr.options.timeOut = 0;
          toastr.options.extendedTimeOut = 0;
          toastr.options.closeButton = false;

          // Show message
          let $toast = toastr.error(
              `Session expired. Redirecting in <span id="countdown">${countdown}</span> seconds...`
          );

          // Countdown update
          let timer = setInterval(() => {
              countdown--;
              $("#countdown").text(countdown);

              if (countdown <= 0) {
                  clearInterval(timer);

                  // Remove toastr after 10 seconds
                  toastr.clear($toast);

                  window.location.href = "{{ url('/login') }}";
              }
          }, 1000);

      } else {
          // Restore normal toastr behavior
          toastr.options.timeOut = 5000;
          toastr.options.extendedTimeOut = 1000;
          let message = extractErrorMessage(xhr);
         
          toastr.error(message);
      }
  }

  let announcementPage = 1;
  let isLoadingAnnouncement = false;
  var announcementPopupEnabled = "{{ $announcementPopupEnabled }}";
  $(document).ready(function() {
    @if(Auth()->user()->agency_fk == "")
      loadAnnouncementCount();
      if (announcementPopupEnabled == 1) {
          checkForUnreadAnnouncements();
      }
    @endif
  });
  
  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
      const targetElement = document.querySelector('.announcements');
      if (targetElement && !targetElement.contains(event.target)) {
          $('.navbar-nav.navbar-nav-right li.announcements').removeClass('show');
          $('.navbar-nav.navbar-nav-right li.announcements div').removeClass('show');
          $('.announcement_dropdown_div').html('');
          announcementPage = 1;
      }
  });
  function loadAnnouncementCount() {
      $.ajax({
          type: "GET",
          url: "{{ url('get-announcement-count') }}",
          success: function(response) {
              if (response.count > 0) {
                  $('#announcement_count').attr('style', '');
                  $('#announcement_count').html('');
                  $('#announcement_count').addClass('count');
              } else {
                  $('#announcement_count').removeClass('count');
                  $('#announcement_count').hide();
              }
          }
      });
  }
  function loadAnnouncementDropdown(isScrollLoad = false) {
      // Reset to page 1 if not a scroll load (i.e., clicking the icon)
      if (!isScrollLoad) {
          announcementPage = 1;
          $('.announcement_dropdown_div').html('');
          $('.announcement-listing-loader').attr('style', 'display:flex');
      }
      $.ajax({
          async: false,
          global: false,
          type: "GET",
          url: "{{ url('get-unread-announcements-dropdown') }}",
          data: {
              //'page': announcementPage
          },
          success: function(res) {
              if (res == '' && announcementPage != 1) {
                  isLoadingAnnouncement = false;
                  announcementPage = 0;
              }
              if (res == '' && announcementPage == 1) {
                  // Empty state is handled by the blade template
              }
              if (announcementPage == 1) {
                  $('.announcement_dropdown_div').html(res);
              } else {
                  $('.announcement_dropdown_div').append(res);
                  isLoadingAnnouncement = false;
              }
              $('.announcement-listing-loader').attr('style', 'display:none');
          }
      });
  }
  function markAnnouncementReadAndRedirect(id, url) {
      $('.announcement-listing-loader').attr('style', 'display:flex');
      $.ajax({
          async: false,
          global: false,
          type: "POST",
          data: {
              'announcement_id': id,
              '_token': "{{ csrf_token() }}",
          },
          url: "{{ url('mark-announcement-as-read') }}",
          success: function(res) {
              $('.announcement-listing-loader').attr('style', 'display:none');
              if (res != null) {
                  window.location.replace(url);
              }
          }
      });
  }
  var announcementQueue = [];
  var currentAnnouncementIndex = 0;
  // Get unshown announcements (popup shows only once per user)
  function checkForUnreadAnnouncements() {
      $.ajax({
          type: "GET",
          url: "{{ url('get-unshown-announcements') }}",
          success: function(response) {
              if (response.data && response.data.length > 0) {
                  announcementQueue = response.data;
                  currentAnnouncementIndex = 0;
                  // Mark ALL announcements as shown immediately when popup loads
                  // This ensures they won't show again on refresh/re-login
                  var allIds = announcementQueue.map(function(a) { return a.id; });
                  markAllAnnouncementsAsShown(allIds);
                  showAnnouncementPopup();
              }
          }
      });
  }
  function showAnnouncementPopup() {
      if (currentAnnouncementIndex >= announcementQueue.length) {
          return;
      }
      var announcement = announcementQueue[currentAnnouncementIndex];
      var totalCount = announcementQueue.length;
      var currentNum = currentAnnouncementIndex + 1;
      var mediaHtml = '';
      var baseUrl = "{{ url('/') }}";
      if (announcement.media && announcement.media.length > 0) {
          mediaHtml = '<div class="media-gallery mt-3" style="display:flex;flex-wrap:wrap;justify-content:center;gap:10px;">';
          announcement.media.forEach(function(media) {
              var mediaUrl = `${baseUrl}/announcement-media-show/${media.id}`;
              if (media.media_type == 'photo') {
                  mediaHtml += `<img src="${mediaUrl}" style="width:100%;max-width:350px;height:auto;border-radius:5px;object-fit:contain;">`;
              } else {
                  mediaHtml += `<video style="width:100%;max-width:350px;height:auto;border-radius:5px;" controls>
                      <source src="${mediaUrl}" type="video/mp4"></video>`;
              }
          });
          mediaHtml += '</div>';
      }
      var stepsHtml = announcement.steps_summary ?
          `<div class="mt-3"><strong>Summary:</strong><div class="mt-1">${announcement.steps_summary}</div></div>` : '';
      var counterHtml = totalCount > 1 ? `<span class="badge badge-primary" style="font-size:12px;padding:5px 10px;border-radius:10px;margin-left:10px;">${currentNum} of ${totalCount}</span>` : '';
      var nextBtnHtml = currentAnnouncementIndex < totalCount - 1 ?
          `<button type="button" class="btn btn-primary" style="border-radius:5px" onclick="nextAnnouncement(${announcement.id})">Next <i class="fa fa-angle-right"></i></button>` : '';
      var modalHtml = `
          <div class="modal fade" id="announcementPopupModal" data-backdrop="static" data-keyboard="false">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content" style="border-radius:10px;border:none;box-shadow:0 5px 15px rgba(0,0,0,0.2);">
                      <div class="modal-header" style="padding:20px 20px 10px;">
                          <h5 class="modal-title" style="font-weight:600;font-size:18px;display:flex;align-items:center;">
                              ${announcement.title} ${counterHtml}
                          </h5>
                          <button type="button" class="close" onclick="closeAnnouncementWithoutRead(${announcement.id})" style="font-size:24px;opacity:0.5;">
                              <span>&times;</span>
                          </button>
                      </div>
                      <div class="modal-body" style="padding:10px 20px 20px;">
                          <div class="announcement-content" style="font-size:14px;line-height:1.6;color:#333;">
                              ${announcement.description}
                          </div>
                          ${stepsHtml}
                          ${mediaHtml}
                      </div>
                      <div class="modal-footer" style="border-top:1px solid #eee;padding:15px 20px;justify-content:space-between;flex-wrap:wrap;">
                          <div>
                              <button type="button" class="btn btn-success" onclick="markAnnouncementAsReadPopup(${announcement.id})" style="border-radius:5px;">
                                  <i class="fa fa-check"></i> Mark as Read
                              </button><br>
                              <a href="{{ url('announcement-list') }}" class="text-primary" style="font-size:13px;">
                                <i class="fa fa-bell"></i> View All Announcements
                              </a>
                              
                          </div>
                          <div class="mt-2 mt-md-0">
                            ${nextBtnHtml}
                            <button type="button" class="btn btn-light" onclick="closeAnnouncementWithoutRead(${announcement.id})"
                              style="border-radius:5px;border:1px solid #ddd;">Close</button>
                              
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      `;
      $('#announcementPopupModal').remove();
      $('body').append(modalHtml);
      showModalSafe('#announcementPopupModal');
  }
  // Helper function to safely show modal (handles cases where Bootstrap modal is not loaded)
  function showModalSafe(selector) {
      var $modal = $(selector);
      if (typeof $.fn.modal === 'function') {
          $modal.modal('show');
      } else {
          // Fallback: manually show modal
          $modal.addClass('show').css('display', 'block');
          $('body').addClass('modal-open');
          if (!$('.modal-backdrop').length) {
              $('body').append('<div class="modal-backdrop fade show"></div>');
          }
      }
  }
  // Helper function to safely hide modal
  function hideModalSafe(selector) {
      var $modal = $(selector);
      if (typeof $.fn.modal === 'function') {
          $modal.modal('hide');
      } else {
          // Fallback: manually hide modal
          $modal.removeClass('show').css('display', 'none');
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open').css('padding-right', '');
      }
  }
  // Mark ALL announcements as shown at once (when popup first loads)
  function markAllAnnouncementsAsShown(announcementIds) {
      $.ajax({
          type: "POST",
          url: "{{ url('mark-announcement-as-shown') }}",
          data: { _token: "{{ csrf_token() }}", announcement_ids: announcementIds }
      });
  }
  // Mark announcement as read when user clicks "Mark as Read" button
  function markAnnouncementAsReadPopup(announcementId) {
      $.ajax({
          type: "POST",
          url: "{{ url('mark-announcement-as-read') }}",
          data: { _token: "{{ csrf_token() }}", announcement_id: announcementId },
          success: function() {
              toastr.success('Announcement marked as read');
              currentAnnouncementIndex++;
              hideModalSafe('#announcementPopupModal');
              setTimeout(function() {
                  $('#announcementPopupModal').remove();
                  if (currentAnnouncementIndex < announcementQueue.length) {
                      showAnnouncementPopup();
                  } else {
                      $('.modal-backdrop').remove();
                      $('body').removeClass('modal-open');
                      $('body').css('padding-right', '');
                  }
                  loadAnnouncementCount();
              }, 300);
          }
      });
  }
  function nextAnnouncement(currentId) {
      // Move to next without marking as read (already marked as shown)
      currentAnnouncementIndex++;
      hideModalSafe('#announcementPopupModal');
      setTimeout(function() {
          $('#announcementPopupModal').remove();
          showAnnouncementPopup();
      }, 300);
  }
  // Close without marking as read - announcement stays unread but won't show popup again
  function closeAnnouncementWithoutRead(currentId) {
      hideModalSafe('#announcementPopupModal');
      setTimeout(function() {
          $('#announcementPopupModal').remove();
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open');
          $('body').css('padding-right', '');
          announcementQueue = [];
          currentAnnouncementIndex = 0;
      }, 300);
  }
  function closeAllAnnouncements() {
      // Just close without marking as read - they're already marked as shown
      hideModalSafe('#announcementPopupModal');
      setTimeout(function() {
          $('#announcementPopupModal').remove();
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open');
          $('body').css('padding-right', '');
          announcementQueue = [];
          currentAnnouncementIndex = 0;
      }, 300);
  }

  function extractErrorMessage(xhr) {
      // If response is JSON object
      if (xhr.responseJSON) {
          return xhr.responseJSON.message ||
                xhr.responseJSON.error_msg ||
                JSON.stringify(xhr.responseJSON);
      }

      // If response is HTML or plain text
      if (xhr.responseText) {
          try {
              // Try to parse as JSON
              let json = JSON.parse(xhr.responseText);
              return json.message || json.error_msg || xhr.statusText;
          } catch (e) {
              // Fallback: return raw text
              return xhr.responseText;
          }
      }

      return xhr.statusText || "Something went wrong.";
  }
    </script>

    <!-- Pusher & Laravel Echo -->
    <script src="{{ asset('assets/pusher.min.js')}}"></script>
    <script>
      var segment1 = "{{ request()->segment(1) }}";
      var segment2 = "{{ request()->segment(2) }}";
      
      if(segment2 !=""){
        if(segment1+'/'+segment2 =='patient/view'){
          Pusher.logToConsole = true;
          window.Pusher = Pusher;
          window.pusherInstance = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
              cluster: '{{ env("PUSHER_APP_CLUSTER", "ap2") }}',
              forceTLS: true
          });
        }
      }
        
    </script>
  </body>

  </html>