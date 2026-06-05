$(document).ready(function() {
    loadAjaxList();

    // Save announcement
    $('#saveAnnouncement').click(function() {
        saveAnnouncement();
    });

    // Update announcement
    $('#updateAnnouncement').click(function() {
        updateAnnouncement();
    });

    // File preview
    $('#media').change(function() {
        previewMedia(this.files, '#media-preview');
    });

    $('#edit_media').change(function() {
        previewMedia(this.files, '#edit-media-preview');
    });
});

function loadAjaxList() {
    $.ajax({
        type: "GET",
        url: _AJAX_LIST,
        success: function(data) {
            $('#resp').html(data);
        },
        error: function(response) {
            toastr.error('Failed to load announcements');
        }
    });
}

function openCreateModal() {
    $('#announcementCreateForm')[0].reset();
    $('.error').text('');
    $('#media-preview').html('');

    // Reset CKEditor
    if (window.descriptionEditor) {
        window.descriptionEditor.setData('');
    }
    if (window.stepsSummaryEditor) {
        window.stepsSummaryEditor.setData('');
    }

    $('#createAnnouncementModal').modal('show');
}

function saveAnnouncement() {
    $('.error').text('');
    $('#loaderCreate').show();
    $('#saveAnnouncement').prop('disabled', true);

    var formData = new FormData($('#announcementCreateForm')[0]);

    // Get CKEditor content
    if (window.descriptionEditor) {
        formData.set('description', window.descriptionEditor.getData());
    }
    if (window.stepsSummaryEditor) {
        formData.set('steps_summary', window.stepsSummaryEditor.getData());
    }

    $.ajax({
        type: "POST",
        url: _STORE_URL,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#loaderCreate').hide();
            $('#saveAnnouncement').prop('disabled', false);

            if (response.status) {
                toastr.success(response.msg);
                $('#createAnnouncementModal').modal('hide');
                loadAjaxList();
            } else {
                if (response.error) {
                    $.each(response.error, function(key, value) {
                        // Handle array validation errors like media.0, media.1, etc.
                        var errorKey = key.replace(/\.\d+$/, '');
                        $('.' + errorKey + '_error').text(value[0]);
                    });
                }
                toastr.error(response.msg || 'Failed to save announcement');
            }
        },
        error: function(response) {
            $('#loaderCreate').hide();
            $('#saveAnnouncement').prop('disabled', false);
            toastr.error('An error occurred');
        }
    });
}

function editAnnouncement(id) {
    $('.error').text('');

    // Clear previous file input and preview
    $('#edit_media').val('');
    $('#edit-media-preview').html('');

    $.ajax({
        type: "GET",
        url: _SHOW_URL + '/' + id,
        success: function(response) {
            if (response.status) {
                var data = response.data;

                $('#edit_id').val(data.id);
                $('#edit_title').val(data.title);

                // Set CKEditor content
                if (window.editDescriptionEditor) {
                    window.editDescriptionEditor.setData(data.description || '');
                }
                if (window.editStepsSummaryEditor) {
                    window.editStepsSummaryEditor.setData(data.steps_summary || '');
                }

                // Display existing media
                var mediaHtml = '';
                if (data.media && data.media.length > 0) {
                    data.media.forEach(function(media) {
                        var src = _MEDIA_SHOW_URL + '/' + media.id;
                        if (media.media_type == 'photo') {
                            mediaHtml += `
                                <div class="media-item-wrapper" style="display:inline-block;margin:5px;position:relative;">
                                    <img src="${src}" style="max-width:80px;max-height:80px;border-radius:5px;object-fit:cover;">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteMedia(${media.id})"
                                            style="position:absolute;top:0;right:0;border-radius:50%;width:20px;height:20px;padding:0;font-size:10px;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            `;
                        } else {
                            mediaHtml += `
                                <div class="media-item-wrapper" style="display:inline-block;margin:5px;position:relative;">
                                    <video style="max-width:80px;max-height:80px;border-radius:5px;" controls>
                                        <source src="${src}" type="video/mp4">
                                    </video>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteMedia(${media.id})"
                                            style="position:absolute;top:0;right:0;border-radius:50%;width:20px;height:20px;padding:0;font-size:10px;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            `;
                        }
                    });
                    $('#existing-media-group').show();
                } else {
                    $('#existing-media-group').hide();
                }
                $('#existing-media').html(mediaHtml);

                $('#editAnnouncementModal').modal('show');
            }
        },
        error: function() {
            toastr.error('Failed to load announcement details');
        }
    });
}

function updateAnnouncement() {
    $('.error').text('');
    $('#loaderEdit').show();
    $('#updateAnnouncement').prop('disabled', true);

    var id = $('#edit_id').val();
    var formData = new FormData($('#announcementEditForm')[0]);

    // Get CKEditor content
    if (window.editDescriptionEditor) {
        formData.set('description', window.editDescriptionEditor.getData());
    }
    if (window.editStepsSummaryEditor) {
        formData.set('steps_summary', window.editStepsSummaryEditor.getData());
    }

    $.ajax({
        type: "POST",
        url: _UPDATE_URL + '/' + id,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#loaderEdit').hide();
            $('#updateAnnouncement').prop('disabled', false);

            if (response.status) {
                toastr.success(response.msg);
                $('#editAnnouncementModal').modal('hide');
                loadAjaxList();
            } else {
                if (response.error) {
                    $.each(response.error, function(key, value) {
                        // Handle array validation errors like media.0, media.1, etc.
                        var errorKey = key.replace(/\.\d+$/, '');
                        $('.edit_' + errorKey + '_error').text(value[0]);
                    });
                }
                toastr.error(response.msg || 'Failed to update announcement');
            }
        },
        error: function(response) {
            $('#loaderEdit').hide();
            $('#updateAnnouncement').prop('disabled', false);
            toastr.error('An error occurred');
        }
    });
}

function deleteAnnouncement(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this announcement?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "DELETE",
                url: _DELETE_URL + '/' + id,
                data: { _token: _CSRF_TOKEN },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.msg);
                        loadAjaxList();
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function() {
                    toastr.error('Failed to delete announcement');
                }
            });
        }
    });
}

function publishAnnouncement(id) {
    Swal.fire({
        title: 'Publish Announcement?',
        text: "This will show the announcement to all users. This may take a few minutes for large user bases.",
        type: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, publish it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            // Show loading indicator
            Swal.fire({
                title: 'Publishing...',
                html: 'Please wait while the announcement is being published to all users.<br><small>This may take a few minutes.</small>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: "POST",
                url: _PUBLISH_URL + '/' + id,
                data: { _token: _CSRF_TOKEN },
                timeout: 600000, // 10 minute timeout for large user bases
                success: function(response) {
                    Swal.close();
                    if (response.status) {
                        toastr.success(response.msg);
                        loadAjaxList();
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    if (status === 'timeout') {
                        toastr.error('Request timed out. The publish process may still be running in the background.');
                    } else {
                        toastr.error('Failed to publish announcement');
                    }
                }
            });
        }
    });
}

function deleteMedia(id) {
    Swal.fire({
        title: 'Delete Media?',
        text: "This action cannot be undone.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "DELETE",
                url: _DELETE_MEDIA_URL + '/' + id,
                data: { _token: _CSRF_TOKEN },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.msg);
                        editAnnouncement($('#edit_id').val());
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function() {
                    toastr.error('Failed to delete media');
                }
            });
        }
    });
}

function viewAnnouncement(id) {
    $.ajax({
        type: "GET",
        url: _SHOW_URL + '/' + id,
        success: function(response) {
            if (response.status) {
                var data = response.data;

                $('#view_title').text(data.title);
                $('#view_description').html(data.description);

                if (data.steps_summary) {
                    $('#view_steps_summary').html(data.steps_summary);
                    $('#view_steps_group').show();
                } else {
                    $('#view_steps_group').hide();
                }

                if (data.media && data.media.length > 0) {
                    var mediaHtml = '';
                    data.media.forEach(function(media) {
                        var src = _MEDIA_SHOW_URL + '/' + media.id;
                        if (media.media_type == 'photo') {
                            mediaHtml += `<img src="${src}" style="max-width:100px;max-height:100px;border-radius:5px;margin:5px;object-fit:cover;">`;
                        } else {
                            mediaHtml += `
                                <video style="max-width:150px;max-height:100px;border-radius:5px;margin:5px;" controls>
                                    <source src="${src}" type="video/mp4">
                                </video>
                            `;
                        }
                    });
                    $('#view_media').html(mediaHtml);
                    $('#view_media_group').show();
                } else {
                    $('#view_media_group').hide();
                }

                $('#viewAnnouncementModal').modal('show');
            }
        },
        error: function() {
            toastr.error('Failed to load announcement details');
        }
    });
}

function previewMedia(files, containerId) {
    var preview = $(containerId);
    preview.html('');

    Array.from(files).forEach(file => {
        var reader = new FileReader();
        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                preview.append(`<img src="${e.target.result}" style="max-width:80px;max-height:80px;border-radius:5px;margin:5px;object-fit:cover;">`);
            } else if (file.type.startsWith('video/')) {
                preview.append(`
                    <video style="max-width:80px;max-height:80px;border-radius:5px;margin:5px;" controls>
                        <source src="${e.target.result}" type="${file.type}">
                    </video>
                `);
            }
        };
        reader.readAsDataURL(file);
    });
}
