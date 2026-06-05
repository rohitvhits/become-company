$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadAjaxList(1);
});


function loadAjaxList(page) {
    $('.hideClass').removeClass('d-none');
    $.ajax({
        url: _EFAX_LOG_LIST + "?page=" + page,
        type: "get",
        data: {
            'patient_id':$('#patient_id').val(),
            'type':$('#type').val(),
            'created_date':$('#created_date').val(),
            'created_by':$('#review_document_user').val(),
        },
        success: function (response) {
            $('.hideClass').addClass('d-none');
            $('#efax_reponse_id').html("")
            $('#efax_reponse_id').html(response);
        }
    });
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadAjaxList(page);
});

$("#filter-btn").click(function() {
    $("#search-filter-btn").slideToggle(600);
});

$(function() {
    var start = moment().subtract(0, 'days');
    var end = moment();
    $('#created_date').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        startOfWeek: 'sunday',
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                'month').endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                .endOf('month')
            ],
            'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
                .endOf('isoWeek')
            ],
            'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
                'weeks').endOf('isoWeek')],
        }
    }, function(chosen_date, end_date) {

        $('#created_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
});

$("#review_document_user").tokenInput(_SEARCH_NYBEST_USER, {
    tokenLimit: 1,
    zindex: 9999,
   
    onAdd: function (item) {
       
    },
    onReady: function() {
        setTimeout(function () {
            $(".token-input-dropdown").css({
                "max-height": "180px",
                "overflow-y": "auto"
            });
        }, 500);
    }
});


function refresh(){
    $('#patient_id').val('')
    $('#type').val('')
    $('#created_date').val('')
    $('#review_document_user').tokenInput('clear');
    loadAjaxList(1);
}

function exportCsv(){
    $.ajax({
        url: _EFAX_LOG_CSV,
        type: "get",
        data: {
            'patient_id':$('#patient_id').val(),
            'type':$('#type').val(),
            'created_date':$('#created_date').val(),
            'created_by':$('#review_document_user').val(),
        },
        success: function (response) {
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "efax_report" + _DATE_TIME + ".csv";
            link.click();
        }
    });
}