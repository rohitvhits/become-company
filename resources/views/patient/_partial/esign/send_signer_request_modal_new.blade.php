<style>
    #sendRequestNew .modal-footer {
        padding: 4px 1px !important;
    }

    #sendRequestNew .form-group label{
        line-height:normal !important;
        margin-bottom:0px !important
    }

    #sendRequestNew .modal-header{
        padding:8px 16px !important;
        border-bottom:0px !important;
        border-top-left-radius:0px !important;
        border-top-right-radius:0px !important
    }

    #sendRequestNew .modal-title{
        font-size:18px !important;
    }

    #sendRequestNew .action-icons a {
        margin-right: 10px;
        opacity: 0.85;
        transition: opacity 0.2s;
    }

    #sendRequestNew .action-icons a:hover {
        opacity: 1;
    }

    .shimmer-loader {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite linear;
        border-radius: 4px;
        height: 20px;
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    .shimmer-row td {
        padding: 12px 8px !important;
    }

    #sendRequestNew .modal-body {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
<div class="modal fade" id="sendRequestNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color:#1e1e2f !important">
                <h5 class="modal-title" id="exampleModalLabel-2 mr-2">Send Request Signer <i class="fa fa-user mr-2"></i><span id="completedSigner"></span>/<span id="totalSigner"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="snedIdNew">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Signer Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="signerTableBody">
                                <tr class="shimmer-row">
                                    <td><div class="shimmer-loader"></div></td>
                                    <td><div class="shimmer-loader"></div></td>
                                    <td><div class="shimmer-loader"></div></td>
                                </tr>
                                <tr class="shimmer-row">
                                    <td><div class="shimmer-loader"></div></td>
                                    <td><div class="shimmer-loader"></div></td>
                                    <td><div class="shimmer-loader"></div></td>
                                </tr>
                                <tr class="shimmer-row">
                                    <td><div class="shimmer-loader"></div></td>
                                    <td><div class="shimmer-loader"></div></td>
                                    <td><div class="shimmer-loader"></div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <button type="button" class="btn btn-primary btn-sm px-4 mr-2" onclick="resfreshSignerDataNew()">Refresh</button>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function initSignerPusherListener() {
    if (typeof window.pusherInstance !== 'undefined') {
        if (!window.signerChannelBound) {
            var signerChannel = window.pusherInstance.subscribe('signer-status');
            signerChannel.bind('SignerStatusUpdated', function(data) {
                // Only process Pusher notification when the modal is open
                if (!$('#sendRequestNew').hasClass('show') && !$('#sendRequestNew').is(':visible')) {
                    return;
                }
                var currentGroupId = $('#groupIdNew').val();
                if (currentGroupId && data.groupId == currentGroupId) {
                    resfreshSignerDataNew();
                }
            });
            window.signerChannelBound = true;
        }
    } else {
        setTimeout(initSignerPusherListener, 500);
    }
}
initSignerPusherListener();
</script>