<input type="hidden" id="call-total-count" value="{{ $totalCount ?? $callDetails->total() }}">

@if($errorMessage)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ $errorMessage }}</strong>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="white-space:nowrap">No</th>
                <th style="white-space:nowrap">Date &amp; Time</th>
                <th style="white-space:nowrap">Type</th>
                <th style="white-space:nowrap">Caller Name</th>
                <th style="white-space:nowrap">Caller Number</th>
                <th style="white-space:nowrap">Dialed Number</th>
                <th style="white-space:nowrap">Extension</th>
                <th style="white-space:nowrap">Duration</th>
                <th style="white-space:nowrap">Talk Time</th>
                <th style="white-space:nowrap">Action</th>
                <th style="white-space:nowrap">Release Reason</th>
                <th style="white-space:nowrap">Codec</th>
                <th style="white-space:nowrap">Recording</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1 + (($callDetails->currentPage() - 1) * $callDetails->perPage()); @endphp
            @forelse($callDetails as $call)
                @php
                    $cdrR      = $call['CdrR'] ?? [];
                    $duration  = (int)($call['duration'] ?? 0);
                    $talkTime  = (int)($call['time_talking'] ?? 0);
                    $ext       = $call['orig_sub'] ?? $call['by_sub'] ?? $cdrR['orig_sub'] ?? '-';
                    $callerNum = isset($call['orig_from_uri'])
                                    ? preg_replace('/^sip:|@.*$/i', '', $call['orig_from_uri'])
                                    : '-';
                    $cdrId = $call['cdr_id'] ?? $cdrR['id'] ?? '';
                    $type  = (int)($call['type'] ?? -1);
                    $typeBadge = match($type) {
                        0       => '<span class="badge badge-primary">Outbound</span>',
                        1       => '<span class="badge badge-success">Inbound</span>',
                        2       => '<span class="badge badge-warning">Missed</span>',
                        default => '<span class="badge badge-secondary">Unknown</span>',
                    };
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td style="white-space:nowrap">{{ isset($call['time_start']) ? \Carbon\Carbon::createFromTimestamp($call['time_start'])->format('m-d-Y h:i:s A') : '-' }}</td>
                    <td>{!! $typeBadge !!}</td>
                    <td>{{ $call['orig_from_name'] ?? $cdrR['orig_from_name'] ?? '-' }}</td>
                    <td style="white-space:nowrap">{{ $callerNum }}</td>
                    <td style="white-space:nowrap">{{ $call['orig_req_user'] ?? $call['orig_to_user'] ?? '-' }}</td>
                    <td>{{ $ext }}</td>
                    <td style="white-space:nowrap">{{ $duration > 0 ? sprintf('%d:%02d', floor($duration / 60), $duration % 60) : '0:00' }}</td>
                    <td style="white-space:nowrap">{{ $talkTime > 0 ? sprintf('%d:%02d', floor($talkTime / 60), $talkTime % 60) : '0:00' }}</td>
                    <td>{{ $cdrR['by_action'] ?? '-' }}</td>
                    <td>{{ $cdrR['release_text'] ?? '-' }}</td>
                    <td>{{ $cdrR['codec'] ?? '-' }}</td>
                    <td>
                        @if($cdrId)
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary btn-recording"
                                    data-cdrid="{{ $cdrId }}"
                                    data-timestart="{{ $call['time_start'] ?? '' }}"
                                    title="Download Recording">
                                <i class="fa fa-download"></i>
                            </button>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center text-muted py-4">No call details found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($callDetails->hasPages())
    <div class="pull-right pegination-margin">
        {{ $callDetails->appends(request()->input())->links('pagination::simple-bootstrap-4') }}
    </div>
@endif
