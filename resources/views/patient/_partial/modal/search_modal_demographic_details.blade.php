<style>
    #patient_search_demographic_modal .modal-footer button{
        padding: 0.42rem 1.25rem;
    }
    #patient_search_demographic_modal .modal-header{
        padding: 15px 25px;
    }
    #patient_search_demographic_modal .modal-body{
        padding: 20px 25px;
    }
</style>

<div class="modal fade" id="patient_search_demographic_modal" aria-modal="true" role="dialog" style="padding-right: 17px; display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Existing Records Details</h4>
                <button onclick="clearLinkModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="javascript:voide(0)" method="POST" id="link_patient">
                <input type="hidden" name="_token" value="{{ csrf_token()}}">
                <div class="modal-body">
                    <div id="resp">
                    </div>
                    <span id="link_error" class="error mt-2"></span>
                </div>
                <div class="modal-footer">
                    <div class="pull-right ml-3">
                            <img src="{{ asset('/ajax-loader.gif') }}" class="link-order-listing-loader1" alt="loader" id="linkLoader" style="display:none">
                    </div>
                    <button type="button" onclick="clearLinkModal()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="linkSearchData();" id="linkPatientId">Link</button>
                    <button type="button" class="btn btn-warning" onclick="submitCreateData();" id="linkPatientId">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>