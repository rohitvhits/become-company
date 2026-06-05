@include('include/header')
@include('include/sidebar')
<style type="text/css">
    .new_contact_list {}

    .new_contact_list a {
        display: block;
        background: #f1f1f194;
        padding: 10px;
        border-bottom: 1px solid #ddd;
        display: flex;
        align-items: center;
        color: #000;
        font-size: 12px;
    }

    .new_contact_list li {
        display: block;
    }

    .new_contact_list a img {
        margin-right: 10px;
    }

    .new_chat_message form {
        display: flex;
        background: #efefef;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 15px;
    }

    .new_chat_message input[type=text] {
        border: 1px solid #ddd;
        padding: 9px;
        flex: 1;
    }

    .new_chat_message .input-box {
        margin-right: 15px;
        flex: 100%;
    }

    .img_div {
        width: 41px;
        margin-right: 15px;
        position: relative;
        overflow: hidden;
        flex: 0 0 41px;
    }

    .img_div input#selectimg {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        opacity: 0;
    }

    .img_div i {
        background: #ff9800;
        height: 41px;
        width: 41px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        flex: 0 0 38px;
        color: #fff;
    }

    #chat-messages-inner p img {
        float: none;
    }

    .new_contact_list {
        max-height: 523px;
        overflow: auto;
    }
    .sr-btn{
  height :38px;
}
.sr-height{
  height: calc(100vh - 200px);

}
.sr-side-ul{
  overflow: auto;
    max-height: 315px;

}
.sr-side-ul::-webkit-scrollbar {
  width: 7px;
}

/* Track */
.sr-side-ul::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
.sr-side-ul::-webkit-scrollbar-thumb {
  background: #a1a3a5;
}

/* Handle on hover */
.sr-side-ul::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.sr-search-side{
  border: 1px solid #e3e7ed;
    padding: 10px;
    border-radius: 10px;
    max-height: 382px;
}

</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row grid-margin-top">
            <div class="col-12 ">
                <div class="widget-box widget-chat">
                    <div class="widget-title bg_lb"> <span class="icon"> <i class="icon-comment"></i> </span>
                    <div class="row">
                            <div class="col-md-6"><h5>SMS Messages</h5></div>
                            <div class="col-md-6 text-right mb-2"><a href="javascrtipt:void(0)" onclick="getModal()" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal-2"><i class="fa fa-plus"></i> Add New Message</a></div>
                        </div>
                    </div>
                    <div class="card sr-height" style="">


                        <div class="card-body">
                            
                            <div class="row">
                                <div class="col-4">
                                    <div class="sr-search-side">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="d-flex mb-2">
                                                        <input type="text" class="form-control mr-2" id="searching_id">
                                                        <button type="button" id="text-msg-send-btn " class="btn btn-success sr-btn" onclick="search();">Search</button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <span id="chat_respo_id"></span>
                                    </div>
                                </div>
                           
                            

                                <div class="chat-content col-8">
                                    <div class="chat-messages" id="chat-messages" style="height:279px;">
                                        <div id="chat-messages-inner"></div>
                                    </div>
                                    <div class="chat-message well new_chat_message">
                                        <form id="attachsubmit" action='<?php echo URL::to('/record/send-file/'); ?>' name="adduser"
                                            method="post" class="form-horizontal" onsubmit="return false;">

                                            <span class="input-box">
                                                <input type="text" name="message" id="msg-box" name="message" />
                                            </span>

                                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                            <div class="img_div">
                                                <i class="fa fa-upload"></i>
                                                <input type="file" name="selectfile" id="selectimg" />
                                            </div>
                                            <button type="button" id="text-msg-send-btn" class="btn btn-success"
                                                onclick="sendMessagefile();">Send</button>
                                        </form>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Chart-box-->
    <div class="modal fade" id="exampleModal-2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Send SMS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="attachsubmitnew" action='<?php echo URL::to('/record/send-file/'); ?>' name="adduser" method="post"
                    class="form-horizontal" onsubmit="return false;">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" id="phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <input type="text" name="message" id="msg-box-new" name="message" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Attachment</label>
                            <input type="file" name="selectfile" id="selectimg-new" />
                        </div>

                    </div>

                    <div class="modal-footer">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <button type="button" class="btn btn-success" onclick="sendMessagefileNew();">Submit</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--End-Chart-box-->


    <!--end-main-container-part-->

    @include('include/footer')


    <script type="text/javascript">
        var chatSelectedRecordId = 0;
        $(document).ready(function() {
            $('#selectimg').change(function() {
                //sendMessagefile();
            });
            $('#text-msg-send-btn').click(function() {
                /*
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
               // add_message('You', '<?= URL::to('/') ?>/img/demo/envelope.png', input.val(),'', true);
                // You will get response from your PHP page (what you echo or print)
              },
              error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
              }
            });

            //	add_message('You','img/demo/envelope.png',input.val(),true);
          }*/
            });

        });

        function sendMessagefile() {
            var alldata = new FormData($('#attachsubmit')[0]);
            var id = $('#user_id').val();
            var name = $('#enrollment_name').val();
            var message = $('#msg-box').val();
            var phone = $('#phone').val();
            if (chatSelectedRecordId != 0 && message != "") {
                $.ajax({
                    type: 'POST',
                    data: alldata,
                    url: "<?= URL::to('sms/sendSMS/') ?>/" + chatSelectedRecordId,
                    dataType: "json",
                    mimeType: "multipart/form-data",
                    contentType: false,
                    processData: false,
                    success: function(response) {

                        add_message('You', '<?= URL::to('/') ?>/img/demo/envelope.png', message, response.file,
                            true);
                        $('#msg-box').val('').focus();
                        $('#selectimg').val('');

                        // You will get response from your PHP page (what you echo or print)
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            }
        }

        var i = 0;

        function add_message(name, img, msg, data, clear) {
            i = i + 1;
            var images = "";
            if (data != "") {
                images = "<img onclick='window.open(this.src)' src='" + data + "' />";
            }
            var inner = $('#chat-messages-inner');
            var time = new Date();
            var hours = time.getHours();
            var minutes = time.getMinutes();
            if (hours < 10) hours = '0' + hours;
            if (minutes < 10) minutes = '0' + minutes;
            var id = 'msg-' + i;
            if (name == null) {
                name = "";
            }
            var idname = name.replace(' ', '-').toLowerCase();
            type = '<span class="icon"> <i class="icon-share"></i> </span>';
            inner.append('<p id="' + id + '" class="user-' + idname + '">' +
                '<span class="msg-block"><img src="' + img + '" alt="" /><strong>' + name +
                '</strong><span class="chat-sms-type-new">' + type + ' </span>  <span class="time">- ' + hours + ':' +
                minutes + '</span>' +
                '<span class="msg">' + msg + '</span><span class="msg-images"> ' + images + '</span>  </span></p>');
            console.log('append');
            $('#' + id).hide().fadeIn(800);
            if (clear) {
                //$('.chat-message input').val('').focus();
                $('#msg-box').val('').focus();
                $('#selectimg').val('');
            }
            $('#chat-messages').animate({
                scrollTop: inner.height()
            }, 1000);
        }

        function add_message_obj(name, img, msg, date, type, media, clear) {
            var images = "";
            if (media) {
                console.log(media);
                for (var k = 0; k < media.length; k++) {
                    var images1 = media[k].replace("/var/www/html/public", "https://web.exmedc.com/");

                    images += "<img onclick='window.open(this.src)' src='" + images1 + "' />";
                    console.log(images);
                }
            }
            i = i + 1;
            var inner = $('#chat-messages-inner');
            var time = new Date(date);
            var hours = time.getHours();
            var minutes = time.getMinutes();
            if (hours < 10) hours = '0' + hours;
            if (minutes < 10) minutes = '0' + minutes;
            var id = 'msg-' + i;
            if (name == null) {
                name = ""
            };
            if (type == "Incoming") {
                newtype="<span class='badge badge-primary'>Incomming</span>";
                type = '<span class="icon"> <i class="icon-reply"></i> </span>';
            } else {
                newtype="<span class='badge badge-success'>Sent</span>";
                type = '<span class="icon"> <i class="icon-share"></i> </span>';
            }
            var idname = name.replace(' ', '-').toLowerCase();
            inner.append('<p id="' + id + '" class="user-' + idname + '">' +
                '<span class="msg-block"><img src="' + img + '" alt="" /><strong>' + name +
                '</strong><span class="chat-sms-type-new">' + type + '</span>  <span class="time">- ' + hours + ':' +
                minutes + '<br>'+newtype+'</span>' +
                '<span class="msg">' + msg + '</span><span class="msg-images"> ' + images + '</span>  </span></p>');
            $('#' + id).hide().fadeIn(800);
            if (clear) {
                $('.chat-message input').val('').focus();
            }
            $('#chat-messages').animate({
                scrollTop: inner.height()
            }, 20);
        }

        function loadAllSMS(phone) {
            i = 0;
            chatSelectedRecordId = phone;
            $('#chat-messages-inner').html("Loading...");
            $.ajax({
                url: "<?= URL::to('sms/get-sms-by-no/') ?>/" + phone,
                type: "post",
                data: {
                    _token: '<?php echo csrf_token(); ?>'
                },
                success: function(response) {
                    $('#chat-messages-inner').html("");
                    console.log(response);
                    response.forEach(element => {
                        add_message_obj(element.name, '<?= URL::to('/') ?>/img/demo/envelope.png',
                            element.message, element.created_at, element.type, JSON.parse(element
                                .media));

                    });
                    // add_message('You', 'img/demo/envelope.png', input.val(), true);
                    // You will get response from your PHP page (what you echo or print)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }

        function sendMessagefileNew() {
            var alldata = new FormData($('#attachsubmitnew')[0]);
            var id = $('#user_id').val();
            var name = $('#enrollment_name').val();
            var message = $('#msg-box-new').val();
            var phone = $('#phone').val();
            if (phone != '' && message != "") {
                $.ajax({
                    type: 'POST',
                    data: alldata,
                    url: "<?= URL::to('sms/sendSMS/') ?>/" + phone,
                    dataType: "json",
                    mimeType: "multipart/form-data",
                    contentType: false,
                    processData: false,
                    success: function(response) {

                        //    add_message('You', '<?= URL::to('/') ?>/img/demo/envelope.png',message,response.file, true);
                        $('#msg-box-new').val('').focus();
                        $('#selectimg-new').val('');
                        window.location.reload();

                        // You will get response from your PHP page (what you echo or print)
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            }
        }

        function search(page){
            var searching_id = $('#searching_id').val();
            $.ajax({
            type: 'GET',
            data: {'searching_id':searching_id},
            url: "{{url('/sms-searching')}}?page="+page,
            
                success: function(response) {
                    
                    $('#chat_respo_id').html('');
                    
                    
                    $('#chat_respo_id').html(response);
                    // You will get response from your PHP page (what you echo or print)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
            return false;
        }
search(1);
$(document).on('click', '.pagination a', function(event) {
	$('li').removeClass('active');
	$(this).parent('li').addClass('active');
	event.preventDefault();
	var myurl = $(this).attr('href');
	var page = $(this).attr('href').split('page=')[1];
	search(page);
});
    </script>
