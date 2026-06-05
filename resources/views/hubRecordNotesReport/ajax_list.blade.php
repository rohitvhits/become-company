<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Hub</th>
            <th>Company Name</th>
            <th>Name</th>
            <th>Subject</th>
            <th>Notes</th>
            <th>Created Date / Created By</th>
        </tr>
    </thead>

    <tbody>
        @if(count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp
        
            @foreach($query as $val)
                <tr>
                    <td>{{$cnt}}</td>
                    <td>
                        @if(isset($val->hub_record_id))
                           <a href="{{ url('hub-record/view')}}/{{ $val->hub_record_id}}" target="_blank"> {{ $val->hub_record_id}}</a>
                        @endif
                    </td>
                    <td>
                        {{ $val->agency_name }}
                    </td>
                    <td>
                        {{ $val->first_name }} {{ $val->last_name }}
                    </td>
                    <td>
                        {{ $val->subject }}
                    </td>
                    <td>
                        @if(strlen($val->message) < 50)
                            {{ $val->message }}
                        @else
                            @php $noteId = 'note-toggle-' . $cnt; @endphp
                            <div class="note-wrapper">
                                <input type="checkbox" id="{{ $noteId }}" class="note-toggle">
                                <div class="note-text">
                                    <span class="note-preview">{{ Str::limit($val->message, 50) }}</span>
                                    <label for="{{ $noteId }}" class="note-read-toggle read-more">Read more</label>
                                    <span class="note-full">{{ $val->message }}</span>
                                    <label for="{{ $noteId }}" class="note-read-toggle read-less">Read less</label>
                                </div>
                            </div>
                        @endif
                    </td>
                    <td>
                        {{ Common::convertMDYTime($val->created_date)}} <br>
                        @if(isset($val->users->first_name))
                        {{ $val->users->first_name.' '.$val->users->last_name}}
                        @endif
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

<div class="pull-right hub_record_report_paginate pegination-margin" id="hub_record_report_paginate">
{{ $query->links() }}
</div>