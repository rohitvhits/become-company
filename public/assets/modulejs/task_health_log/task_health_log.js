$(function () {
    $(".wmd-view-topscroll").scroll(function () {
        $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".wmd-view").scroll(function () {
        $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
    });
    loadTaskHealthLogList(1);

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

function loadTaskHealthLogList(page) {
    $('#resp').html("");
    $('#loadertag').attr('style','display:block');
    var created_date = $("#created_date").val();
    var agency_id = $("#agency_id").val();
    var type = $("#type").val();
    $.ajax({
        url: _TASK_HEALTH_LOG_LIST + "?page=" + page,
        type: "get",
        data: {
            'agency_id': agency_id,
            'created_date' : created_date,
            'type' : type
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
    loadTaskHealthLogList(page);
});

function showSwal(type,id) {
    var message = $('#'+id).html();
    console.log(message)
    $('.dataContainer').html('');
    // if(message != ""){
    //     $('#exampleModal-4').modal('show');
    //     let content = '';
    //     content += ` <pre>{<br>`;
    //     $.each(JSON.parse(message), function(key, value) {
    //            var values = "-";
    //             if (value === undefined || value === null || value === "") {

    //             }else{
    //                 values = value;

    //             }
    //             content += `<span class="key">"${capitalizeFirstLetter(key.replace('_', ' '))}"</span>: <span class="string">"${values}"</span>,<br>`;
    //     });
    //     content += ` } <pre>`;
    //     $('.dataContainer').html(content);
    // }
    if (message != "") {
    $('#exampleModal-4').modal('show');

    let content = '';
    content += `<pre>{\n`;

    let data = JSON.parse(message);

    $.each(data, function (key, value) {

        let values = "-";

        if (value !== undefined && value !== null && value !== "") {

            if (typeof value === "object") {
                values = JSON.stringify(value, null, 2);
            } 
            else if (typeof value === "string") {

                try {
                    // try parsing normally
                    let parsed = JSON.parse(value);
                    values = JSON.stringify(parsed, null, 2);
                } catch (e) {

                    try {
                        // remove escape characters and try again
                        let cleaned = value.replace(/\\"/g, '"');
                        let parsed2 = JSON.parse(cleaned);
                        values = JSON.stringify(parsed2, null, 2);
                    } catch (err) {
                        values = value;
                    }

                }

            } else {
                values = value;
            }

        }

        content += `"${capitalizeFirstLetter(key.replace('_', ' '))}": ${values},\n`;
    });

    content += `}\n</pre>`;

    $('.dataContainer').html(content);
}
}


function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
