<div class="modal fade" id="import-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Import CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="appps_id">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" name="adduser" method="post" id="formnew">
                    @csrf
                    @if($auth->agency_fk == '')
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Company<span style="color:red">*</span>:</label>
                        <select name="agency_id" class="form-control" id="agency_ids">
                            <option value="">Select Company</option>
                            @if (count($agencyList) > 0)
                            @foreach ($agencyList as $vsl)
                            <option value="{{$vsl->id}}">{{$vsl->agency_name}}</option>
                            @endforeach
                            @endif
                        </select>
                        <span class="error mt-2 text-danger" id="agency_error" for="file_name"></span>
                    </div>
                    @else
                    <input type="hidden" name="agency_id" value="{{$user->agency_fk}}">
                    @endif
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Upload CSV<span style="color:red">*</span>:</label>
                        <input type="file" class="form-control" id="timeidnew" name="images">
                        <span class="error mt-2 text-danger" id="images_error" for="file_name"></span>
                    </div>
                    <div class="form-group">
                        <p>Click here to download the <a href="{{ URL::to('/hub_sample.csv') }}">sample file.</a></p>
                    </div>
                </form>
                <div id="importLoader" style="display:none; padding:20px;">
                    <div class="shimmer-loader" style="height: 20px; width: 80%; margin: 0 auto 10px;"></div>
                    <div class="shimmer-loader" style="height: 20px; width: 90%; margin: 0 auto 10px;"></div>
                    <div class="shimmer-loader" style="height: 20px; width: 70%; margin: 0 auto;"></div>
                </div>
                <div id="importResponseMsg" style="display:none;text-align:center;padding:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="saveImport()" id="import-save" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>