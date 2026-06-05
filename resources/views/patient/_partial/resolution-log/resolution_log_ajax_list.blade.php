<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th nowrap>#</th>
            <th nowrap>Team</th>
            <th nowrap>Resolution</th>
            <th nowrap>Cancel Reason</th>
            <th nowrap>Refuse Reason</th>
            <th nowrap>Notes</th>
            <th nowrap>Created Date / Created By</th>
        </tr>
    </thead>

    <tbody>
        @if(count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp
        
            @foreach($query as $val)
            <tr>
                    <td>{{ $cnt++}}</td>
                    <td>
                        {{$val->team?? '-'}}
                    </td>
                    <td>
                        @php $resStatus = $val->resolution; @endphp
                        @if($val->resolution == 'unableToContact')
                            @php $resStatus = 'Unable To Contact'; @endphp
                        @endif
                        {{$resStatus??'-'}}
                    </td>
                    <td>
                        {{$val->cancel_reason??'-'}}
                        @if(!empty($val->other_cancel_reason))
                            <i class="fa fa-info-circle ml-1" style="cursor: pointer; color: #17a2b8;" data-toggle="tooltip" data-placement="top" title="{{ $val->other_cancel_reason }}"></i>
                        @endif
                    </td>
                    <td>
                        {{$val->refuse_reason??'-'}}
                        @if(!empty($val->other_refuse_reason))
                            <i class="fa fa-info-circle ml-1" style="cursor: pointer; color: #17a2b8;" data-toggle="tooltip" data-placement="top" title="{{ $val->other_refuse_reason }}"></i>
                        @endif
                    </td>
                    <td>
                        @if(strlen($val->notes) < 50)
                            {{ $val->notes }}
                        @else
                            @php $noteId = 'note-toggle-' . $cnt; @endphp
                            <div class="note-wrapper">
                                <input type="checkbox" id="{{ $noteId }}" class="note-toggle">
                                <div class="note-text">
                                    <span class="note-preview">{{ Str::limit($val->notes, 50) }}</span>
                                    <label for="{{ $noteId }}" class="note-read-toggle read-more">Read more</label>
                                    <span class="note-full">{{ $val->notes }}</span>
                                    <label for="{{ $noteId }}" class="note-read-toggle read-less">Read less</label>
                                </div>
                            </div>
                        @endif
                    </td>
                    <td>
                        {{ Common::convertMDYTime($val->created_at)}} <br>
                        @if(isset($val->first_name))
                        {{ $val->first_name.' '.$val->last_name}}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr>
                <td colspan="7">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right resolution-data pegination-margin" id="resolution-data">
{{ $query->links() }}
</div>