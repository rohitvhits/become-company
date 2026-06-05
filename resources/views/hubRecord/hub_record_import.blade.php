<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th>Id</th>
            <th>File Name</th>
            <th>Total</th>
            <th>Success</th>
            <th>Failed</th>
            <th>Deactivate</th>
            <th>Update</th>
            <th>Status</th>
            <th>Error</th>
            <th>Created Date / Created By</th>
        </tr>
    </thead>

    <tbody>
        @if(isset($query) && count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp
            @foreach($query as $log)
                <tr>
                    <td>{{$cnt}}</td>
                    <td width="30%">
                        <span class="file-name" title="{{$log->file_name}}">{{$log->file_name}}</span>
                    </td>
                    <td class="text-center" width="5%">
                        {{ $log->total_records }}
                    </td>
                    <td class="text-center text-success" width="5%">
                        {{ $log->successful_records }}
                    </td>
                    <td class="text-center text-danger" width="5%">
                        {{ $log->failed_records }}
                    </td>
                    <td class="text-center text-info" width="5%">
                        {{ $log->deactivate_records??'-' }}
                    </td>
                    <td class="text-center text-primary" width="5%">
                        {{ $log->updated_records }}
                    </td>
                    <td width="10%">
                        <span class="status-badge {{$log->status}}">{{ucfirst($log->status)}}</span>
                    </td>
                    <td width="30%">
                        @if($log->error_details)
                            @php
                                $errors = json_decode($log->error_details, true);
                                $isJson = is_array($errors);
                                $preview = $isJson ? array_slice($errors, 0, 3) : [];
                                $hasMore = $isJson && count($errors) > 3;
                            @endphp
                            @if($isJson)
                                <div class="import-error-preview">
                                    @foreach($preview as $err)
                                        <div class="small text-danger">{{ $err }}</div>
                                    @endforeach
                                    @if($hasMore)
                                        <a href="javascript:void(0)"
                                           class="small text-primary"
                                           onclick="showImportErrors(this)"
                                           data-errors="{{ base64_encode($log->error_details) }}">
                                           See more ({{ count($errors) - 3 }} more)
                                        </a>
                                    @endif
                                </div>
                            @else
                                <span class="small text-danger">{{ Str::limit($log->error_details, 120) }}</span>
                                @if(strlen($log->error_details) > 120)
                                    <a href="javascript:void(0)"
                                       class="small text-primary d-block"
                                       onclick="showImportErrors(this)"
                                       data-errors="{{ base64_encode(json_encode([$log->error_details])) }}">
                                       See more
                                    </a>
                                @endif
                            @endif
                        @endif
                    </td>
                    <td width="10%">
                        {{ date('m/d/Y H:i:s', strtotime($log->created_date)) }}<br/>
                       {{ optional($log->users)->first_name }} {{ optional($log->users)->last_name }}
                    </td>
                </tr>
                @php $cnt++; @endphp
            @endforeach
        @endif
        @if(count($query) == 0)
            <tr class="txt-center">
                <td colspan="8">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right hub_record_log pegination-margin" id="hub_record_log">
{{ $query->links() }}
</div>

<!-- Import Error Modal -->
<div class="modal fade" id="importErrorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Errors</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="max-height:500px; overflow-y:auto;">
                <div id="importErrorList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showImportErrors(el) {
    var errors = JSON.parse(atob($(el).data('errors')));
    var html = errors.map(function(e) {
        return '<div class="small text-danger border-bottom py-1">' + $('<div>').text(e).html() + '</div>';
    }).join('');
    $('#importErrorList').html(html);
    $('#importErrorModal').modal('show');
}
</script>