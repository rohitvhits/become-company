@include('include/header')
@include('include/sidebar')

<!--main-container-part-->
<div id="content" >
  <!--breadcrumbs-->
  <div id="content-header">
    <div id="breadcrumb"> <a href="/" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a></div>
  </div>
  <!--End-breadcrumbs-->

  <!--Action boxes-->
  <div class="container-fluid" style="display:none">
    <div class="quick-actions_homepage" style="display:none">
      <ul class="quick-actions">
        <li class="bg_lb"> <a href="index.html"> <i class="icon-dashboard"></i> <span class="label label-important">20</span> My Dashboard </a> </li>
        <li class="bg_lg span3"> <a href="charts.html"> <i class="icon-signal"></i> Charts</a> </li>
        <li class="bg_ly"> <a href="widgets.html"> <i class="icon-inbox"></i><span class="label label-success">101</span> Widgets </a> </li>
        <li class="bg_lo"> <a href="tables.html"> <i class="icon-th"></i> Tables</a> </li>
        <li class="bg_ls"> <a href="grid.html"> <i class="icon-fullscreen"></i> Full width</a> </li>
        <li class="bg_lo span3"> <a href="form-common.html"> <i class="icon-th-list"></i> Forms</a> </li>
        <li class="bg_ls"> <a href="buttons.html"> <i class="icon-tint"></i> Buttons</a> </li>
        <li class="bg_lb"> <a href="interface.html"> <i class="icon-pencil"></i>Elements</a> </li>
        <li class="bg_lg"> <a href="calendar.html"> <i class="icon-calendar"></i> Calendar</a> </li>
        <li class="bg_lr"> <a href="error404.html"> <i class="icon-info-sign"></i> Error</a> </li>

      </ul>
    </div>
    <!--End-Action boxes-->

    <div class="row-fluid">
      <div class="span8">
        <div class="widget-box widget-chat">
          <div class="widget-title bg_lb"> <span class="icon"> <i class="icon-comment"></i> </span>
            <h5>SMS Message Option</h5>
          </div>
          <div class="widget-content nopadding collapse in" id="collapseG4">
            <div class="chat-users panel-right2">
              <div class="panel-title">
                <h5>Last Records</h5>
              </div>
              <div class="panel-content nopadding">
                <ul class="contact-list">
                  <?php
                  foreach ($chatRecords as $obj) {
                    # code...
                    ?>
                    <li id="user-Alex"><a href="javascript:void(0)" onclick="loadAllSMS({{$obj->id}})"><img alt="" src="img/demo/av1.jpg" /> <span>{{$obj->fullName()}} <br />{{ $obj->last_sms_at }}</span></a>
                      <?php if ($obj->unread_sms > 0) { ?>
                        <span class="msg-count badge badge-info">{{$obj->unread_sms}}</span>
                      <?php
                        }
                        ?>

                    </li>
                  <?php
                  }
                  ?>

                </ul>
              </div>
            </div>
            <div class="chat-content panel-left2">
              <div class="chat-messages" id="chat-messages">
                <div id="chat-messages-inner"></div>
              </div>
              <div class="chat-message well">
                <button id="text-msg-send-btn" class="btn btn-success">Send</button>
                <span class="input-box">
                  <input type="text" name="msg-box" id="msg-box" />
                </span> </div>
            </div>
          </div>
        </div>
      </div>
      <?php /*
      <div class="span6">
        <div class="widget-box">
          <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="icon-chevron-down"></i></span>
            <h5>Latest Received SMS</h5>
          </div>
          <div class="widget-content nopadding collapse in" id="collapseG2">
            <ul class="recent-posts">
              <?php
              foreach ($lastIncommingSMS as $key => $obj) {
                # code...
                ?>
                <li>
                  <div class="user-thumb"> <img width="40" height="40" alt="User" src="img/demo/av1.jpg"> </div>
                  <div class="article-post"> <span class="user-info"> Record :{{$obj->fullName()}} / Date Time:{{$obj->dateTime()}}</span>
                    <p><a href="/record/{{$obj->record_id}}">{{$obj->message}}</a> </p>
                  </div>
                </li>
              <?php
              }
              ?>
              <li>
                <button class="btn btn-warning btn-mini">View All</button>
              </li>
            </ul>
          </div>
        </div>
      </div>
*/ ?>
    </div>
  </div>
</div>
<!--Chart-box-->

<!--End-Chart-box-->


<!--end-main-container-part-->

@include('include/footer')


<script type="text/javascript">
  var chatSelectedRecordId = 0;
  $(document).ready(function() {

    $('#text-msg-send-btn').click(function() {
      var input = $(this).siblings('span').children('input[type=text]');
      if (input.val() != '' && chatSelectedRecordId != 0) {

        $.ajax({
          url: "<?= URL::to('record/send-sms/') ?>/" + chatSelectedRecordId,
          type: "post",
          data: {
            _token: '<?php echo csrf_token(); ?>',
            message: input.val()
          },
          success: function(response) {
            add_message('You', '<?= URL::to('/') ?>/img/demo/av1.jpg', input.val(), true);
            // You will get response from your PHP page (what you echo or print)
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
          }
        });

        //	add_message('You','img/demo/av1.jpg',input.val(),true);
      }
    });

  });


  var i = 0;

  function add_message(name, img, msg, clear) {
    i = i + 1;
    var inner = $('#chat-messages-inner');
    var time = new Date();
    var hours = time.getHours();
    var minutes = time.getMinutes();
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    var id = 'msg-' + i;
    var idname = name.replace(' ', '-').toLowerCase();
    inner.append('<p id="' + id + '" class="user-' + idname + '">' +
      '<span class="msg-block"><img src="' + img + '" alt="" /><strong>' + name + '</strong><span class="chat-sms-type">- Send </span>  <span class="time">- ' + hours + ':' + minutes + '</span>' +
      '<span class="msg">' + msg + '</span></span></p>');
    $('#' + id).hide().fadeIn(800);
    if (clear) {
      $('.chat-message input').val('').focus();
    }
    $('#chat-messages').animate({
      scrollTop: inner.height()
    }, 1000);
  }

  function add_message_obj(name, img, msg, date, type, clear) {
    i = i + 1;
    var inner = $('#chat-messages-inner');
    var time = new Date(date);
    var hours = time.getHours();
    var minutes = time.getMinutes();
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    var id = 'msg-' + i;
    if(name==null){ name="Record" }
    var idname = name.replace(' ', '-').toLowerCase();
    inner.append('<p id="' + id + '" class="user-' + idname + '">' +
      '<span class="msg-block"><img src="' + img + '" alt="" /><strong>' + name + '</strong><span class="chat-sms-type">- ' + type + '</span>  <span class="time">- ' + hours + ':' + minutes + '</span>' +
      '<span class="msg">' + msg + '</span></span></p>');
    $('#' + id).hide().fadeIn(800);
    if (clear) {
      $('.chat-message input').val('').focus();
    }
    $('#chat-messages').animate({
      scrollTop: inner.height()
    }, 20);
  }

  function loadAllSMS(recordID) {
    i = 0;
    chatSelectedRecordId = recordID;
    $('#chat-messages-inner').html("Loading...");
    $.ajax({
      url: "<?= URL::to('record/get-sms/') ?>/" + recordID,
      type: "post",
      data: {
        _token: '<?php echo csrf_token(); ?>'
      },
      success: function(response) {
        $('#chat-messages-inner').html("");
        console.log(response);
        response.forEach(element => {
          add_message_obj(element.name, '<?= URL::to('/') ?>/img/demo/av1.jpg', element.message, element.created_at, element.type);

        });
        // add_message('You', 'img/demo/av1.jpg', input.val(), true);
        // You will get response from your PHP page (what you echo or print)
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
  }
</script>