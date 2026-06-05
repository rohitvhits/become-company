function loadDashboardList(page = 1) {
    $('.shimmer_id').removeClass('hide');
    $('#response_requested_id').html("");
    $('.location-wise-data-loader').attr('style', 'display:flex');
    $.ajax({
        url: _LOAD_DATA_URL,
        data: {
            'page': page,
            'template_name': $('#filter_template_name').val(),
            'status': $('#filter_status').val(),
            'created_date': $('#filter_created_date').val(),
        },
        type: "GET",
        success: function(res) {
            $('.shimmer_id').addClass('hide');
            $('#response_requested_id').html(res);
            $('.location-wise-data-loader').attr('style', 'display:none');
        }
    });
}

loadDashboardList(1);

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

$('body').on('click', '#response_requested_id .pagination a', function(event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    let page = $(this).attr('href').split('page=')[1];
    loadDashboardList(page);
});

function resetFilters() {
    $('#filter_template_name').val("");
    $('#filter_status').val("");
    $('#filter_created_date').val("");
    loadDashboardList(1);
}

$("#open-assign-user-modal").click(function() {
    $('#assignEsignUserModal').modal('show');
    initTokenInput();
    loadAssignUserList();
});

let tokenInitialized = false;
function initTokenInput() {
    if (tokenInitialized) {
        $("#assign_esign_user_search").tokenInput("clear");
        return;
    }
    $("#assign_esign_user_search").tokenInput(_SEARCH_NYBEST_USER, {
        tokenLimit: 1,
        preventDuplicates: true,
        zindex: 1060,
        hintText: 'Type to search NyBest users',
        noResultsText: 'No users found',
        searchingText: 'Searching...'
    });
    tokenInitialized = true;
}

function loadAssignUserList(page) {
    let url = _LIST_ASSIGN_USER_URL;
    if (page) {
        url += '?page=' + page;
    }
    $('#default-assign-user-list-container').html('');
    $('.assign_user_shimmer_id').removeClass('hide').show();
    $.ajax({
        url: url,
        type: "GET",
        success: function(res) {
            $('.assign_user_shimmer_id').addClass('hide').hide();
            $('#default-assign-user-list-container').html(res);
        },
        error:function(jqr){
            showErrorAndLoginRedirection(jqr);
        }
    });
}

$("#btn-assign-esign-user-submit").click(function() {
    let tokens = $("#assign_esign_user_search").tokenInput("get");
    if (tokens.length === 0) {
        toastr.error('Please select a user.');
        return;
    }
    let userId = tokens[0].id;
    let btn = $(this);
    btn.prop('disabled', true);
    $('#assign-user-loader').removeClass('d-none');

    $.ajax({
        url: _STORE_ASSIGN_USER_URL,
        type: "POST",
        data: { user_id: userId, _token: _CSRF_TOKEN },
        success: function(res) {
            btn.prop('disabled', false);
            $('#assign-user-loader').addClass('d-none');
            toastr.success(res.error_msg);
            $("#assign_esign_user_search").tokenInput("clear");
            loadAssignUserList();
        },
        error: function(jqr) {
            btn.prop('disabled', false);
            $('#assign-user-loader').addClass('d-none');
            showErrorAndLoginRedirection(jqr);
        }
    });
});

$("#btn-refresh-assign-user-list").click(function() {
    loadAssignUserList();
});

$('body').on('click', '#default-assign-user-list-container .pagination a', function(e) {
    e.preventDefault();
    let page = $(this).attr('href').split('page=')[1];
    loadAssignUserList(page);
});

$('body').on('click', '.btn-remove-assign-user', function() {
    let id = $(this).data('id');
    $.confirm({
        title: 'Are you sure?',
        content: 'Would you like to remove this user?',
        columnClass: 'col-md-6',
        buttons: {
            formSubmit: {
                text: 'Confirm',
                btnClass: 'btn-danger',
                action: function() {
                    $.ajax({
                        url: _DELETE_ASSIGN_USER_URL,
                        type: "POST",
                        data: { id: id, _token: _CSRF_TOKEN },
                        success: function(res) {
                           toastr.success(res.error_msg);
                            loadAssignUserList();
                        },
                        error:function(jqr){
                            showErrorAndLoginRedirection(jqr);
                        }
                    });
                }
            },
            cancel: function() {}
        }
    });
});

$(document).ready(function(){
    $('#filter_created_date').daterangepicker({
        autoUpdateInput: false,
        ranges: {
            'Today':        [moment(), moment()],
            'Yesterday':    [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days':  [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month':   [moment().startOf('month'), moment().endOf('month')],
            'Last Month':   [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        }
    }, function(start, end){
        $('#filter_created_date').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    });
});