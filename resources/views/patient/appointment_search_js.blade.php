<script>
    /* ..Start.. For page refresh when search data then show search area */
    $(document).ready(function() {
        var url = window.location.search;
        var arguments = url.split('?')[1];
        var searchText = arguments.split('=')[0];
        if (searchText == 'sms_status') {
            $("#search-div").show();
        }
    });
    /* ..End.. For page refresh when search data then show search area */
    $("#searchbtns").click(function() {
        $("#search-div").toggle();
    });

    $(document).on("click", ".searchAppoinment", function() {

        var due_date = $('#due_date').val();
        var sms_status = $('#sms_status').val();
        var status = $('#status_id').val();
        var agency_fk = $('#agency_fk').val();
        var first_name = $('#agency_name').val();
        var mobile = $('#mobile').val();
        var assign_user_id = $('#assign_user_id').val();
        var appointment_date = $('#appointment_date').val();
        var locationId = $('#locationId').val();
        var created_date = $('#created_date').val();
        var service_id = $('#service_id').val();
        var status_update = $('#status_update').val();

        if (due_date == '' && sms_status == '' && (status == '' || status == null) && agency_fk == '' &&
            first_name == '' &&
            mobile == '' && assign_user_id == '' && appointment_date == '' && locationId == '' &&
            created_date == '' && service_id == '') {
            alert('Please select or enter any one search text');
            return false;
        } else {
            sms_status = sms_status != null ? sms_status : '';
            status = status != null ? status : '';
            agency_fk = agency_fk != null ? agency_fk : '';
            first_name = first_name != null ? first_name : '';
            mobile = mobile != null ? mobile : '';
            assign_user_id = assign_user_id != null ? assign_user_id : '';
            due_date = due_date != null ? due_date : '';
            appointment_date = appointment_date != null ? appointment_date : '';
            locationId = locationId != null ? locationId : '';
            created_date = created_date != null ? created_date : '';
            service_id = service_id != null ? service_id : '';

            if (status_update == "cancel Appointments" || status_update == "refused Appointments") {
                var links = "<?php echo URL::to('/'); ?>/{{ $appointmentUrl }}&sms_status=" + sms_status + "&agency_fk=" + agency_fk + "&first_name=" + first_name + "&mobile=" + mobile + "&service_id=" +
                service_id + "&assign_user_id=" + assign_user_id + "&due_date=" + due_date +
                "&appointment_date=" + appointment_date + "&locationId=" + locationId + "&created_date=" +
                created_date + "";
            }else{
                var links = "<?php echo URL::to('/'); ?>/{{ $appointmentUrl }}?sms_status=" + sms_status + "&status=" +
                status +
                "&agency_fk=" + agency_fk + "&first_name=" + first_name + "&mobile=" + mobile + "&service_id=" +
                service_id + "&assign_user_id=" + assign_user_id + "&due_date=" + due_date +
                "&appointment_date=" + appointment_date + "&locationId=" + locationId + "&created_date=" +
                created_date + "";
            }
            
            window.location.href = links;
        }
    });

    $(function() {
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
        $('.due_datenn').daterangepicker({
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

            $('.due_datenn').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })
    });

    $(".datepicker").datepicker();

    $(function() {
        var start = moment().subtract(0, 'days');
        var end = moment();


        $('.datepicker1').daterangepicker({
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

            $('.datepicker1').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
                'MM/DD/YYYY'));
        })


    });
</script>
