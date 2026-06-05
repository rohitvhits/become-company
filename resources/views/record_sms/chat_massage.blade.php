<style>
#loadersId{
float:left
}</style>
<div class="modal-content">
				<div class="modal-header">
				  <h5 class="modal-title" id="ModalLabel">Notes Message
					 <?php if(in_array($user->user_type_fk,array(3,4))){ ?>
					 <div class="pull-right" style="margin-left:20%">
					 <img src="<?php echo URL::to('/');?>/img/spinner.gif" id="loadersId" style="display:none;">
						<input type="radio" name="radio"  value="All" onclick="getmessage('All')" checked>All
						<input type="radio" name="radio" value="Emc" onclick="getmessage('Emc')">EMC
						<input type="radio" name="radio" value="Agency" onclick="getmessage('Agency')">Agency
					</div>
					<?php }?>
				  </h5>
				 
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				  
				</div>
				<div class="modal-body">
					 <div id="testing_id" >
					
                                  <div class="card-body">
                                   
                                     <div class="list-wrapper">
                                        <div class="chat-messages" id="chat-messages">
                                           <div id="chat-messages-inner">
                                          </div>
                                        </div>
                                        <div class="chat-message  custom-chat">
                                          <span id="all_error" style="color:red"></span>
										  <button class="btn btn-success btn-sm" id="text-msg-send-btn">Send</button>
                                          <span class="input-box">
                                          <!--   <input type="text" name="msg-box" id="text-msg-box" /> -->
                                          <textarea style="margin-bottom: 0 !important; width: 100%;" name="msg-box" id="text-msg-box"></textarea>
                                          </span>
                                        </div>
                                     </div>
                                  </div>
                            
					
					</div>
				</div>
			
				
</div>

				
		
                 

<script type="text/javascript">
         $("#testing_id").animate({ scrollTop: $("#testing_id")[0].scrollHeight }, 'fast');
</script>

<script>
  function nl2br (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
  $(document).ready(function() {
  
    $('#text-msg-send-btn').click(function() {
		var radioValue = $('input[name="radio"]:checked').val();
		$('#all_error').html("");
		<?php if(in_array($user->user_type_fk,array(3,4))){?>
			if(radioValue =='All'){
				$('#all_error').html("Please choose emc or agency");
				return false;
			}
			<?php } ?>
      var input = $(this).siblings('span').children('textarea');
      // var showmessage=nl2br($('#text-msg-box').val());
       var showmessage=nl2br(input.val());
    // alert(abc);

      if (input.val().trim() != '') {
        $.ajax({
          url: "<?= URL::to('record/send-sms/' . $record_id) ?>",
          type: "post",
          data: {
            _token: '<?php echo csrf_token(); ?>',
            message: input.val(),
			"radioValue":radioValue
          },
          success: function(response) {
           <?php $auth = auth()->user();?>
		   <?php if(in_array($user->user_type_fk,array(5,6))){?>
		   radioValue = 'Agency';
		   <?php } ?> 
            add_message('<?php echo $auth->first_name;?>', radioValue, showmessage, true);
 
		   // add_message('You', ' ', showmessage, true);
            // You will get response from your PHP page (what you echo or print)
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
          }
        });

        //  add_message('You','img/demo/av1.jpg',input.val(),true);
      } 
    });
    loadAllSMS();

  });


  var i = 0;

  function add_message(name, ctype, msg, clear) {

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
      '<span class="msg-block"> <strong>' + name + ' ( '+ ctype +')</strong><span class="time"> ' + hours + ':' + minutes + '</span>' +
      '<span class="msg">' + msg + '</span></span></p>');
    $('#' + id).hide().fadeIn(800);
    if (clear) {
      $('.chat-message textarea').val('').focus();
    }
    $('#chat-messages').animate({
      scrollTop: inner.height()
    }, 1000);
  }


  function add_message_obj(name, img, msg, date, type,sender_id, clear) {
    //alert(sender_id);
    i = i + 1;
    var user_id="<?php echo $user->id; ?>";
    var inner = $('#chat-messages-inner');
    var time = new Date(date);
    var hours = time.getHours();
    var minutes = time.getMinutes();
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    var id = 'msg-' + i;
  /*  var type="Receive";
  if(user_id == sender_id ){
   name="You";
   type="Send";

  }else if (name == null) {
      name = "Record";
    }*/
    var idname = name.replace(' ', '-').toLowerCase();
    inner.append('<p id="' + id + '" class="user-' + idname + '">' +
      '<span class="msg-block"><strong>' + name + ' ('+type+') </strong><span class="time"> ' + hours + ':' + minutes + '</span>' +
      '<span class="msg">' + msg + '</span></span></p>');
    $('#' + id).hide().fadeIn(800);
    if (clear) {
      $('.chat-message textarea').val('').focus();
    }
    $('#chat-messages').animate({
      scrollTop: inner.height()
    }, 20);
  }

  function loadAllSMS() {
	$('#chat-messages-inner').html(" ");
	var readMessage = $('input[name="radio"]:checked').val();
	$('#loadersId').attr('style','display:block');
    $.ajax({
      url: "<?= URL::to('record/get-notes/' . $record_id) ?>",
      type: "post",
      data: {
        _token: '<?php echo csrf_token(); ?>',
		readMessage:readMessage
      },
      success: function(response) {
        console.log(response);
        response.forEach(element => {
          add_message_obj(element.first_name, '<?= URL::to('/') ?>/img/demo/av1.jpg', element.message, element.created_at, element.type,element.sender_id);

        });
		$('#loadersId').attr('style','display:none');
        // add_message('You', 'img/demo/av1.jpg', input.val(), true);
        // You will get response from your PHP page (what you echo or print)
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
  }
function getmessage(val){
	loadAllSMS();
}
</script>
