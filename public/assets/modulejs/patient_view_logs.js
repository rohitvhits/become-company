$(document).on('click', '.log-pegination .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    getAppointmentLogs(page);
});


function getAppointmentLogs(page) {

    var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

    $.ajax({
        method: 'GET',
        url: _PATIENT_VIEW_LOGS + "?page=" + page,
        data: {
            'id': _RECORD_ID,
            '_token': _CSRF_TOKEN
        },
        beforeSend: function() {
            $('#loadertagLogs').show();
        },
        success: function success(response) {

            $('#loadertagLogs').hide();
            $('#logList').html("");
            $('#logList').html(response);
        },
        error: function error(_error) {
          
            toastr.error('Something happened. Try again');
        }
    });
}

function viewLog(id){
	$.ajax({
		url: _VIEW_LOGS_DETAILS,
		data: {
			id: id
		},
		success: function(res){
			let old_response = res.data.old_response;
			let new_response = res.data.new_response;
			$('#log-model').modal('show');
			let content = '';
			content += `<div class=\"row\">`;
			content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-primary text-white\" style="padding:10px !important"><b>Old Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
			content += highlightJson(old_response);
			content += `</div></div></div>`;
			content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-success text-white\"  style="padding:10px !important"><b>New Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
			content += highlightJson(new_response);
			content += `</div></div></div>`;
			content += `</div>`;
			$('.dataContainer').html(content);
		}
	});
}

function highlightJson(jsonInput) {
    if (!jsonInput) return '<pre style="word-break:break-all;white-space:pre-wrap;">-</pre>';
    let obj;
    if (typeof jsonInput === 'string') {
        try {
            obj = JSON.parse(jsonInput);
			
        } catch (e) {
            // If not JSON, just show as text
            return '<pre style="word-break:break-all;white-space:pre-wrap;">' + jsonInput + '</pre>';
        }
    } else if (typeof jsonInput === 'object') {
        obj = jsonInput;
		
    } else {
        return '<pre style="word-break:break-all;white-space:pre-wrap;">' + String(jsonInput) + '</pre>';
    }
    let pretty = JSON.stringify(obj, null, 4);
	let content = '';
        content += ` <pre>{<br>`;
        $.each(JSON.parse(pretty), function(key, value) {
               var values = "-";
                if (value === undefined || value === null || value === "") {
                   
                }else{
                    values = value;
                 
                }
                content += `<span class="key">"${capitalizeFirstLetter(key.replace('_', ' '))}"</span>: <span class="string">"${values}"</span>,<br>`;
        });
        content += ` } <pre>`;

       return content;
}

function capitalizeFirstLetter(string) {
	string = string.replace(':', '');
	string = string.replace('"','');
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}