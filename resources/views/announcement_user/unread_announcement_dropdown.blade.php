@if(!empty($data) && count($data) > 0)
<div class="announcement_dropdown_class">
    @foreach($data as $announcement)
    @php
        $description = strip_tags($announcement->description);
        $shortDesc = strlen($description) > 65 ? substr($description, 0, 65) . '...' : $description;
        $url = url('announcement-list');
    @endphp
    <div class="dropdown-item preview-item" onclick="markAnnouncementReadAndRedirect('{{$announcement->id}}','{{$url}}')" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;cursor:pointer;">
        <div class="card" style="width: 423px;border-radius: 10px;">
            <div class="card-body row">
                <div class="col-md-1" style="margin-left: -15px;">
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-primary">
                            <i class="mdi mdi-bullhorn mx-0"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-11" style="margin-left: 9px;">
                    <a href="{{$url}}" target="_blank"><h6 class="card-title" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">{{ $announcement->title }}</h6></a>
                    <p class="tx-12 mb-2 text-muted" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">#{{ $announcement->id }} | {{ $shortDesc }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <p class="tx-10 text-muted mb-0" style="margin-left: 45px;margin-top: -24px;">
                        @if($announcement->creator)
                            {{ $announcement->creator->first_name ?? '' }} {{ $announcement->creator->last_name ?? '' }}
                        @else
                            System Admin
                        @endif
                        | {{ date('M d, Y, h:i A', strtotime($announcement->created_date)) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="dropdown-item preview-item" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">
    <div class="card" style="width: 423px;border-radius: 10px;">
        <div class="card-body row">
            <div class="col-md-1" style="margin-left: -15px;">
                <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                        <i class="mdi mdi-check-circle mx-0"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-11" style="margin-left: 9px;">
                <h6 class="card-title" style="white-space: normal;word-wrap: break-word;word-break: break-word;overflow-wrap: break-word;">You're all caught up! No new announcements.</h6>
            </div>
        </div>
    </div>
</div>
@endif
