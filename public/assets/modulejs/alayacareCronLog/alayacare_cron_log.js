$('#filter-btn').click(function() {
        $("#search-filter-btn").toggle();
    });

    $(document).ready(function() {
        // Initialize date range picker with predefined ranges
        $('#date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'MM/DD/YYYY'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
                'Next Week': [moment().add(1, 'week').startOf('week'), moment().add(1, 'week').endOf('week')],
                'Last Week': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
            }
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        $('#date_range').on('cancel.daterangepicker', function() {
            $(this).val('');
        });

        cronLogList(1);
    });

    
    function resetData() {
        $('#date_range').val('');
        $('#type').val('');
        $('#cron_type').val('');
        cronLogList(1);
    }

    function cronLogList(page) {
        var dateRange = $('#date_range').val();
        var type = $('#type').val();
        var cronType = $('#cron_type').val();

        $('.location-wise-data-loader').attr('style', 'display:flex');
        $('.shimmer_id').removeClass('hide');
        $('#resp').html('');

        $.ajax({
            url: _AJAX_LIST+"?page=" + page,
            type: "GET",
            data: {
                'created_date': dateRange,
                'type': type,
                'cron_type': cronType,
            },
            success: function(res) {
                $('.shimmer_id').addClass('hide');
                $('.location-wise-data-loader').attr('style', 'display:none');
                $('#resp').html(res);
            },
            error: function(jqr) {
                $('.shimmer_id').addClass('hide');
                $('.location-wise-data-loader').attr('style', 'display:none');
                toastr.error('Failed to load records');
            }
        });
        return false;
    }

    // Pagination click handler
    $('body').on('click', '.pagination a', function(event) {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        cronLogList(page);
    });

    // View record detail in modal
    function viewRecord(id) {
        $.ajax({
            url: _VIEW_LOG+"/" + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    $('#modal_request_response').text(formatJson(data.request_response));
                    $('#modal_return_response').text(formatJson(data.return_response));
                    $('#cronLogModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Failed to load record details');
            }
        });
    }

    function formatJson(str) {
        if (!str) return 'N/A';
        try {
            var parsed = JSON.parse(str);
            return JSON.stringify(parsed, null, 2);
        } catch (e) {
            return str;
        }
    }