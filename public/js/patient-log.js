
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
var _log = {
    url: {
        logList: userLogList,
        logExport: userLogExport
    },
    error: _defineProperty({
        unknown: 'Something happened. Try again.'

    }, 'unknown', 'Something happened. Try again.')
};
var orginalData = [];
var pageLimit = 10;
var pagesCount;
var orginalData;
$(document).ready(function () {

    /**
    * Login log  Table Initialize
    */
    getData(1);
});

/** 
        * Getting Login log List
        */
function getData(page) {
    $('.order-listing-loader').attr('style', '');
    var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

    var patientId = $.trim($('#patient_id').val());
   
    $.ajax({
        method: 'GET',
        url: _log.url.logList+"?page="+page,
        data: {
            'patientId': patientId,
           
            
        },
        success: function success(response) {
            $('.order-listing-loader').attr('style', 'display:none');
            
            if(response.data.length !=0){
                var json  = response.data.data;
                var tableResponse = "";
                var cnt =response.data.from;
           
                $.each(json,function(i,v){
                    var finalNewResponse = '';
                    var finalOldResponse = 'N/A';
                    if(v.new_response.length !=0){
                        
                        $.each(v.new_response,function(i,vs){
                            finalNewResponse +='<b>'+i+'</b>' +' : '+vs+'<br>';
                        })
                    }
                    
                    if(v.old_response.length !=0){
                        
                        $.each(v.old_response,function(i,vs){
                            var strong = '<b>'+i+'</b>';
                            finalOldResponse += strong+' : '+vs+'<br>';
                        })
                    }
                    tableResponse +='<tr><td>'+cnt+++'</td><td>'+v.username+'</td><td>'+finalOldResponse+'</td><td>'+finalNewResponse+'</td><td>'+moment(v.created_at).format('MM/DD/YYYY hh:mm A')+'</td></tr>';
                })
               
            }else{
                var tableResponse = "<tr><td colspan='5'>No Record Available</td></tr>";
            }


            $('#response_id').html("");
            $('#response_id').html(tableResponse)
            var paginateUrl = '';
            if(response.data.length !=0){
                console.log(response.data)
                    var paginate = '<ul class="pagination">';

                    for(var i = 1;i<=response.data.last_page;i++){
                        var active = '';
                        if(response.data.current_page == i){
                            active = 'active';
                        }
                        var url =  _log.url.logList+"?page="+i+"&patientId="+patientId;
                        paginate +='<li  class="page-item '+active+'" ><a class="page-link" href="'+url+'">'+i+'</a></li>';
                    }
                    // $.each(response.data.links,function(i,v){
                    //     var active = '';
                        
                    //     if(v.active ==true){
                    //         active = 'active';
                    //     }
                    //     if(v.url != null){
                    //         paginate +='<li  class="page-item '+active+'" ><a class="page-link" href="'+v.url+'">'+v.label+'</a></li>';
                    //     }
                    // })

                paginate +='</ul>'
                paginateUrl +=paginate
                console.log(paginate);
                $('#paginateId').html(paginateUrl);
            }
        },
        error: function error(_error) {
            console.error(_error);
            toastr.error('Something happened. Try again');
        }
    });
}


$('body').on('click', '#search-data', function (e) {

    getData(1);
})
$('body').on('click', '#resetTable', function (e) {
    $('#user_name').val('');
    $('#fields').val('');
    $('#sort').val('');
    $('#created_date').val('');
    $('#user_id').val('');
    getData(1);
})

$('body').on('click', '.record_id', function (e) {
    var fields = $(this).attr('data-field');
    var sort = $(this).attr('data-sort');

    $('#fields').val(fields);
    $('#sort').val(sort);
    getData(1, fields, sort);
})
$(document).on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var myurl = $(this).attr('href');
    var page = $(this).attr('href').split('page=')[1];
    getData(page);
});
function export_data() {
    var userName = $.trim($('#user_name').val());
    var field = $('#fields').val();
    var createdDate = $('#created_date').val();
    var sort = $('#sort').val();

    var URLExport = _log.url.logExport + '?username=' + userName +  '&field=' + field + '&sort=' + sort + '&createdat=' + createdDate;
    $('#export-data').attr("style", '');
    $('#export-data').attr("href", URLExport);
}


