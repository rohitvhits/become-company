$(document).ready(function () {
    loadAnnouncementList();

    // Open video player modal on thumbnail click
    $(document).on('click', '.ann-video-thumb', function (e) {
        e.preventDefault();
        var src   = $(this).data('src');
        var label = $(this).data('label') || 'Video';
        $('#ann_video_title').text(label);
        $('#ann_video_source').attr('src', src);
var player = document.getElementById('ann_video_player');
        player.load();
        $('#annVideoPlayerModal').modal('show');
    });

    // Stop video when modal closes
    $('#annVideoPlayerModal').on('hide.bs.modal', function () {
        var player = document.getElementById('ann_video_player');
        player.pause();
        player.currentTime = 0;
    });

    $('#ann_video_close_btn, #ann_video_close_btn2').on('click', function () {
        $('#annVideoPlayerModal').modal('hide');
    });

    // Mark as read — action button in table
    $(document).on('click', '.mark-read-btn', function (e) {
        e.stopPropagation();
        var id = $(this).data('id');
        markAsRead(id);
    });

    // Mark as read — button inside modal
    $(document).on('click', '#ann_modal_mark_read_btn', function () {
        var id = $(this).data('id');
        markAsRead(id);
        $('#viewAnnouncementUserModal').modal('hide');
    });

    // Pagination
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (url) loadAnnouncementList(url.replace(/^http:\/\//i, 'https://'));
    });
});

/* ── Load list ── */
function loadAnnouncementList(url) {
    var ajaxUrl = url || _AJAX_LIST;
    $('#announcement-container').html(
        '<div class="ann-empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>'
    );
    $.ajax({
        type: 'GET',
        url: ajaxUrl,
        success: function (data) {
            $('#announcement-container').html(data);
            $('html, body').animate({ scrollTop: $('#announcement-container').offset().top - 80 }, 200);
        },
        error: function () {
            toastr.error('Failed to load announcements');
        }
    });
}

/* ── View announcement modal ── */
function viewAnnouncement(id) {
    $.ajax({
        type: 'GET',
        url: _SHOW_URL + '/' + id,
        success: function (response) {
            if (!response.status) {
                toastr.error('Announcement not found');
                return;
            }
            var d = response.data;

            // Title & meta
            $('#ann_modal_title').text(d.title);
            $('#ann_modal_date').text(formatDate(d.created_date));

            // Status badge
            if (d.is_read == '0' || typeof d.is_read === 'undefined') {
                // Check from DOM
                var rowIsRead = $('tr[data-id="' + id + '"] .badge-new').length === 0;
                if (!rowIsRead) {
                    $('#ann_modal_status_wrap').html('<span class="badge-new" style="font-size:10px;">New</span>');
                } else {
                    $('#ann_modal_status_wrap').html('<span class="badge-read-status" style="font-size:10px;">Read</span>');
                }
            } else {
                $('#ann_modal_status_wrap').html('<span class="badge-read-status" style="font-size:10px;">Read</span>');
            }

            // Description
            $('#ann_modal_description').html(d.description || '<em class="text-muted">No description</em>');

            // Steps / Summary
            if (d.steps_summary) {
                $('#ann_modal_steps').html(d.steps_summary);
                $('#ann_modal_steps_wrap').show();
            } else {
                $('#ann_modal_steps_wrap').hide();
            }

            // Media
            if (d.media && d.media.length > 0) {
                var mediaHtml = '';
                var galleryGroup = 'ann-gallery-' + d.id;
                $.each(d.media, function (_i, m) {
                    var src = _MEDIA_URL + '/' + m.id;
                    var label = m.file_name || ('Attachment ' + (_i + 1));
                    if (m.media_type === 'photo') {
                        mediaHtml += '<a data-fancybox="' + galleryGroup + '" href="' + src + '" class="ann-media-item">'
                            + '<img src="' + src + '" alt="Image" loading="lazy">'
                            + '</a>';
                    } else {
                        mediaHtml += '<a href="javascript:void(0)" class="ann-media-item ann-video-thumb"'
                            + ' data-src="' + src + '" data-label="' + label + '">'
                            + '<video src="' + src + '" preload="metadata" muted></video>'
                            + '<div class="play-overlay"><i class="fa fa-play-circle"></i></div>'
                            + '</a>';
                    }
                });
                $('#ann_modal_media').html(mediaHtml);
                $('#ann_modal_media_wrap').show();
                // Rebind Fancybox after dynamic content injection
                if (typeof Fancybox !== 'undefined') {
                    Fancybox.bind('#ann_modal_media [data-fancybox]');
                }
            } else {
                $('#ann_modal_media_wrap').hide();
            }

            // Mark as read button (show only if unread)
            var isUnread = $('tr[data-id="' + id + '"]').hasClass('ann-unread');
            if (isUnread) {
                $('#ann_modal_mark_read_btn').data('id', id).show();
            } else {
                $('#ann_modal_mark_read_btn').hide();
            }

            $('#viewAnnouncementUserModal').modal('show');
        },
        error: function () {
            toastr.error('Failed to load announcement details');
        }
    });
}

/* ── Mark as read ── */
function markAsRead(id) {
    $.ajax({
        type: 'POST',
        url: _MARK_READ,
        data: { _token: _CSRF_TOKEN, announcement_id: id },
        success: function (response) {
            if (response.status) {
                // Update row
                var row = $('tr[data-id="' + id + '"]');
                row.removeClass('ann-unread');
                row.find('td:first-child').css('border-left', 'none');

                // Update status badge
                $('#status-badge-' + id)
                    .removeClass('badge-new')
                    .addClass('badge-read-status')
                    .text('Read');

                // Update action button
                $('#markbtn-' + id)
                    .removeClass('ann-btn-check mark-read-btn')
                    .addClass('ann-btn-done')
                    .attr('disabled', true)
                    .attr('title', 'Already Read')
                    .removeAttr('data-id')
                    .html('<i class="fa fa-check-circle"></i>');

                // Hide modal mark read button
                $('#ann_modal_mark_read_btn').hide();
                $('#ann_modal_status_wrap').html('<span class="badge-read-status" style="font-size:10px;">Read</span>');

                updateNotificationCount();
            }
        },
        error: function () {
            toastr.error('Failed to mark as read');
        }
    });
}

/* ── Update header notification count ── */
function updateNotificationCount() {
    $.ajax({
        type: 'GET',
        url: _ANN_COUNT,
        success: function (response) {
            if (response.count > 0) {
                $('#announcement_count').text(response.count).show();
                $('#ann-unread-count').text(response.count + ' unread').show();
            } else {
                $('#announcement_count').hide();
                $('#ann-unread-count').hide();
            }
        }
    });
}

/* ── Format date ── */
function formatDate(dateStr) {
    if (!dateStr) return '';
    try {
        var d = new Date(dateStr);
        var opts = { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
        return d.toLocaleDateString('en-US', opts);
    } catch (e) {
        return dateStr;
    }
}
