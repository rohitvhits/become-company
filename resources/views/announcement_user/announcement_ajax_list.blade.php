@if(!empty($query) && count($query) > 0)

@php
    $unread = $query->where('is_read', '0')->count();
@endphp

<script>
    // Update unread badge in page header
    (function() {
        var cnt = {{ $unread }};
        if (cnt > 0) {
            $('#ann-unread-count').text(cnt + ' unread').show();
        } else {
            $('#ann-unread-count').hide();
        }
    })();
</script>

<table class="table mb-0" id="announcement-listing">
    <thead>
        <tr>
            <th style="width:48px;">#</th>
            <th style="width:22%;">Title</th>
            <th>Description</th>
            <th style="width:90px; text-align:center;">Media</th>
            <th style="width:90px; text-align:center;">Status</th>
            <th style="width:150px;">Date</th>
            <th style="width:80px; text-align:center;">Action</th>
        </tr>
    </thead>
    <tbody>
        @php $i = ($query->currentPage() - 1) * $query->perPage() + 1; @endphp
        @foreach($query as $announcement)
        <tr class="{{ $announcement->is_read == '0' ? 'ann-unread' : '' }}" data-id="{{ $announcement->id }}">
            <td style="color:#a0aec0; font-size:12px; font-weight:600;">{{ $i++ }}</td>
            <td>
                <a class="ann-title-link" onclick="viewAnnouncement({{ $announcement->id }})">
                    {{ $announcement->title }}
                </a>
            </td>
            <td>
                <div class="ann-desc-preview">
                    {!! Str::limit(strip_tags($announcement->description), 90) !!}
                </div>
                @if($announcement->steps_summary)
                <div class="ann-desc-preview mt-1" style="color:#a0aec0;">
                    <i class="fa fa-list-ul" style="font-size:10px;"></i>
                    {!! Str::limit(strip_tags($announcement->steps_summary), 60) !!}
                </div>
                @endif
            </td>
            <td style="text-align:center;">
                @if($announcement->media && count($announcement->media) > 0)
                <span class="badge-media" onclick="viewAnnouncement({{ $announcement->id }})">
                    <i class="fa fa-paperclip"></i> {{ count($announcement->media) }}
                </span>
                @else
                <span style="color:#cbd5e0; font-size:13px;">—</span>
                @endif
            </td>
            <td style="text-align:center;">
                @if($announcement->is_read == '0')
                <span class="badge-new" id="status-badge-{{ $announcement->id }}">New</span>
                @else
                <span class="badge-read-status" id="status-badge-{{ $announcement->id }}">Read</span>
                @endif
            </td>
            <td style="font-size:12.5px; color:#718096;">
                {{ date('m/d/Y', strtotime($announcement->created_date)) }}<br>
                <span style="color:#a0aec0; font-size:11px;">{{ date('h:i A', strtotime($announcement->created_date)) }}</span>
            </td>
            <td style="text-align:center;">
                <button class="ann-action-btn ann-btn-view" onclick="viewAnnouncement({{ $announcement->id }})" title="View Details">
                    <i class="fa fa-eye"></i>
                </button>
                @if($announcement->is_read == '0')
                <button class="ann-action-btn ann-btn-check mark-read-btn" data-id="{{ $announcement->id }}" title="Mark as Read" id="markbtn-{{ $announcement->id }}">
                    <i class="fa fa-check"></i>
                </button>
                @else
                <button class="ann-action-btn ann-btn-done" title="Already Read" id="markbtn-{{ $announcement->id }}" disabled>
                    <i class="fa fa-check-circle"></i>
                </button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-between align-items-center" style="padding: 12px 14px; border-top: 1px solid #edf2f7;">
    <small class="text-muted">
        Showing {{ $query->firstItem() }}–{{ $query->lastItem() }} of {{ $query->total() }} announcements
    </small>
    <div>{{ $query->links('pagination::bootstrap-4') }}</div>
</div>

@else
<div class="ann-empty">
    <i class="fa fa-bullhorn"></i>
    <p>No announcements yet. Check back later.</p>
</div>
@endif
