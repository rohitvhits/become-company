
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
var _loginLog = {
    url: {
        loginLogList: userLoginList,
        loginLogExport: userLoginExport
    },
    error: _defineProperty({
        unknown: 'Something happened. Try again.'

    }, 'unknown', 'Something happened. Try again.')
};
var orginalData = [];
var pageLimit = 1;
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

    var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

    var userName = $.trim($('#user_name').val());
    var userId =$.trim($('#user_id').val());
    var ip = $.trim($('#ip').val());
    var country = $.trim($('#country').val());
    var countryCode = $.trim($('#country_code').val());
    var loginStatus = $('#login_status').val();
    var field = $('#fields').val();
    var sort = $('#sort').val();
    var createdDate = $('#created_date').val();



    $.ajax({
        method: 'GET',
        url: _loginLog.url.loginLogList + "?page=" + page,
        data: {
            'username': userName,
            'country': country,
            'ip': ip,
            'country': country,
            'countrycode': countryCode,
            'loginstatus': loginStatus,
            'createdat': createdDate,
            'field': field,
            'sort': sort,
            'userid':userId
        },
        success: function success(response) {

            $('.order-listing-loader').attr('style', 'display:none');
            $('#loginlog_list_id').html("");
            $('#loginlog_list_id').html(response);
        },
        error: function error(_error) {
            console.error(_error);
            toastr.error('Something happened. Try again');
        }
    });
}


$('body').on('click', '#searchid', function (e) {

    getData(1);
})
$('body').on('click', '#resetTable', function (e) {
    $('#user_name').val('');
    $('#ip').val('');
    $('#country').val('');
    $('#country_code').val('');
    $('#login_status').val('');
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
    var ip = $.trim($('#ip').val());
    var country = $.trim($('#country').val());
    var countryCode = $.trim($('#country_code').val());
    var loginStatus = $('#login_status').val();
    var field = $('#fields').val();
    var createdDate = $('#created_date').val();
    var userId =$.trim($('#user_id').val());
    var sort = $('#sort').val();

    var URLExport = _loginLog.url.loginLogExport + '?username=' + userName + '&ip=' + ip + '&country=' + country + '&countrycode=' + countryCode + '&loginstatus=' + loginStatus + '&field=' + field + '&sort=' + sort + '&createdat=' + createdDate+ '&userid=' + userId;
    $('#export-data').attr("style", '');
    $('#export-data').attr("href", URLExport);
}


