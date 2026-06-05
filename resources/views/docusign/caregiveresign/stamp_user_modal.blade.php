<style>
    .table-check {
        padding-left: 10px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    td.row_td {
        padding: 0 5px 0px 5px;
        padding-left: 25px;
    }

    .options-group {
        display: none;
    }

    .modal-header {
        background-color: #f7f7f7;
        padding: 5px 5px;
    }

    .ModalLabel {
        text-align: center;
    }
</style>
<!-- Field Master Start -->
<div class="modal fade" id="stampUserFormModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-header">
            <h5 class="modal-title" id="ModalLabel">Add Stamp</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <form class="forms-sample" name="adduser" action="" method="post" id="stampUserFormAdd"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body pl-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Stamp Name<span
                                            class="error mt-2">*</span></label>                                    
                                            <div class="col-sm-9">
                                        
                                            <select name="stamp_user" id="stampUserDropdown" class="form-control">
                                                @if (!empty($stamp_user) && count($stamp_user) > 0)
                                                <option value="">-- Select a Stamp User --</option>
                                                    @foreach ($stamp_user as $stamp)
                                                        <option value="{{ url('stmp') }}/{{ $stamp['id'] }}" 
                                                            {{ !empty($selectedStamp) && $selectedStamp == url('stmp') . '/' . $stamp['id'] ? 'selected' : '' }}>
                                                            {{ $stamp['title'] ?? 'Unnamed Stamp' }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                <option value="">No stamp user data available</option>

                                                @endif
                                            </select>
                                            <br>
                                            @if (!empty($stamp_user) && count($stamp_user) > 0)
                                            <img id="stampImagePreview" 
                                                src="{{ !empty($selectedStamp) ? $selectedStamp : '' }}" 
                                                alt="Stamp Preview"
                                                style="margin-top:10px; width:100px; height:100px; display:{{ !empty($selectedStamp) ? 'block' : 'none' }};">
                                                @else
                                                
                                            <p>No stamp user data available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="stampUserForm" data-uid="">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

