$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadReportList(1);

    var start = moment().subtract(0, 'days');
    var end = moment();
    $('.datepickernn').daterangepicker({
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

        $('.datepickernn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
    })
});

function loadReportList(page) {
    $('#resp').html("");
    $('#loadertag').attr('style','display:block');
    var created_date = $("#created_date").val();
    var name = $("#name").val();
    $.ajax({
        url: FEEDBACK_FORM_LIST + "?page=" + page,
        type: "get",
        data: {
            'name': name,
            'created_date' : created_date,
        },
        success: function (response) {
            $('#resp').html("")
            $('#resp').html(response);
            $('#loadertag').attr('style','display:none');
        }
    });
}

$('body').on('click', '.pagination a', function (event) {
    $('li').removeClass('active');
    $(this).parent('li').addClass('active');
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadReportList(page);
});

function showSwal(type,id) {
    var message = $('#'+id).html();
    var surveyData = [
        {"que_id":"1","que":"How satisfied are you with our service?","answer":null},
        {"que_id":"2","que":"Is the appointment scheduling feature intuitive and easy to use?","answer":null},
        {"que_id":"3","que":"Do you think there are any features missing from the system?","answer":null},
        {"que_id":"4","que":"Any additional comments?","answer":null}
    ];

    // Correct way to get total number of questions
    var totalLength = surveyData.length;
    $('.dataContainer').html('');
    if(message != ""){
        $('#exampleModal-4').modal('show');
        let content = '';
        var notShow = 0;
        var totalLength = JSON.parse(message).length;
        content += ` <div class="accordion accordion-bordered" id="accordion-2" role="tablist">`;
        $.each(JSON.parse(message), function(key, value) {
            var show = '';
            if(key == 0){
                show = 'show';
            }
            if(value.answer == null){
                notShow += 1;
            }else{
                content += `<div class="card">
                        <div class="card-header" role="tab" id="heading-${key}">
                          <h6 class="mb-0">
                            <a data-toggle="collapse" href="#collapse-${key}" aria-expanded="false" aria-controls="collapse-${key}">
                                ${value.question}
                            </a>
                          </h6>
                        </div>
                        <div id="collapse-${key}" class="collapse ${show}" role="tabpanel" aria-labelledby="heading-${key}" data-parent="#accordion-2">
                          <div class="card-body">
                            <p class="mb-0">${value.answer} </p>
                          </div>
                        </div>
                        </div>`;
            }
        });
        content += ` </div>`;
        if(totalLength == notShow){
            content = 'The patient has visited the link but submitted a blank response.';
        }
        $('.dataContainer').html(content);
    }
}
