<style>
    .modal-lg-plus {
        max-width: 900px; / Set your custom width /
    }
    
</style>
<div id="logModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg-plus" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">E-Signature Log List <br>#<span id="tempName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="logTable" class="table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Status</th>
                            <th>Message</th>
                            <th>Action By</th>
                            {{-- <th>Sent Timestamp</th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>