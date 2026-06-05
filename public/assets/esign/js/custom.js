function getChange(id){
		
				
	}
	
	function HideShowCheck(divId, txtId, textVal) {

            var check = $('#' + txtId).prop('checked');
			
            $.each(headers, function (i, kl) {
                if (kl.type == 'checkbox') {
                    if (kl.opponent == 'text') {
                        if (txtId == kl.SenderId) {
                            if (check == true) {
                                $('#' + kl.ReceiverId).removeClass('Depending');
                            } else {
                                $('#' + kl.ReceiverId).addClass('Depending');
                                $('#' + kl.ReceiverDivId).val(" ");
                                HideShow(kl.ReceiverId, "");
                            }
                        }
                    }
                    if (kl.opponent == 'image') {
                        if (text == kl.value) {
                            $('#' + kl.ReceiverDivId).removeClass('Depending');
                        } else {
                            $('#' + kl.ReceiverDivId).addClass('Depending');
                            var slp = kl.ReceiverDivId.split('_');
                            $('#img' + slp[1]).attr('src', 'https://www.cdpasny.com/assets/images/new_favicon_01.png');
                            $('#img' + slp[1]).attr('dataids', '');
                        }
                    }
                    if (kl.opponent == 'checkbox') {
                        if (txtId == kl.SenderId) {
                            if (check == true) {
                                $('#' + kl.ReceiverDivId).removeClass('Depending');
                            } else {
                                $('#' + kl.ReceiverDivId).addClass('Depending');
                                $('#' + kl.ReceiverDivId).prop('checked', false);
                                HideShowCheck("", kl.ReceiverId, "");
                            }
                        }
                    }
                    if (kl.opponent == 'dropdown') {
                        if (txtId == kl.SenderId) {
                            if (check == true) {
                                $('#' + kl.ReceiverId).removeClass('Depending');

                            } else {
                                $('#' + kl.ReceiverId).addClass('Depending');
                                var childId = $('#' + kl.ReceiverId).children().attr("id");

                                $("#" + kl.ReceiverId).val('');
                                HideShowDrop(kl.ReceiverDivId, "", "")
                            }
                        }
                    }
                    if (kl.opponent == 'radio' && divId == kl.ReceiverId) {

                        var explode = $('#' + kl.ReceiverDivId).attr('groupname');

                        if (check == true) {
                            $.each($("input[name='" + explode + "']"), function (i, ls) {
                                var TotalRadioId = $(this).parent().attr('id');
                                $("#" + TotalRadioId).removeClass('Depending');
                            })


                        } else {
                            $.each($("input[name='" + explode + "']"), function (i, ls) {
                                var TotalRadioId = $(this).parent().attr('id');
                                $("#" + TotalRadioId).addClass('Depending');

                                $("#" + $(this).attr('id')).prop("checked", false);
                                HideShowRadio($(this).attr('id'), "")
                            })
                        }
                    }
                }
            });
        }