
@forelse($list as $item)
        @php
            $isNew      = $item->call_status === 'pending' ||$item->call_status === 'called' && !$item->admin_verified;
            $isUpcoming = $item->call_fired_at && !$item->converted_to_appointment && !$item->admin_verified;
            $rowClass   = $isNew ? 'row-new' : ($isUpcoming ? 'row-upcoming' : '');
        @endphp
        <tr class="{{ $rowClass }}">
            <td>
                @if($isNew)<span class="row-new-dot"></span>@endif
                {{ $item->id }}
            </td>
            <td>
                <strong>{{ $item->patient_name ?? '-' }}</strong>
                @if($item->triggered_by)
                    <br><small class="text-muted">{{ $item->triggered_by }}</small>
                @endif
            </td>
            <td>{{ $item->mobile ?? '-' }}</td>
            <td>{{ $item->agency_name ?? '-' }}</td>
            <td>
                @if($item->call_status === 'called')
                    <span class="badge badge-called">Called</span>
                @elseif($item->call_status === 'booked')
                    <span class="badge badge-booked">Booked</span>
                @elseif( $item->call_status === 'self-booked')
                    <span class="badge badge-booked">Self-Booked</span>
                @elseif($item->call_status === 'failed')
                    <span class="badge badge-failed">Failed</span>
                @elseif($item->call_status === 'no_response')
                    <span class="badge" style="background:#6c757d;color:#fff;">No Response</span>
                    @if($item->call_attempts)
                        <br><small class="text-muted">{{ $item->call_attempts }} attempt(s)</small>
                    @endif
                @else
                    <span class="badge badge-pending">Pending</span>
                @endif
            </td>
            <td style="white-space:nowrap;">
                {{ $item->call_fired_at ? $item->call_fired_at->format('m/d/Y h:i A') : '-' }}
            </td>
            <td style="white-space:nowrap;">
                {{ $item->created_at ? $item->created_at->format('m/d/Y h:i A') : '-' }}
            </td>
            <td>{{ $item->callAppointment->location->location_name ?? '-' }}</td>
            <td>
                @if($item->admin_verified)
                    <span class="badge badge-verified"><i class="fa fa-check"></i> Yes</span>
                    @if($item->admin_verified_at)
                        <br><small class="text-muted">{{ $item->admin_verified_at->format('m/d/Y') }}</small>
                    @endif
                @else
                    <span class="badge badge-unverified">No</span>
                @endif
            </td>
            <td>
                @if($item->converted_to_appointment)
                    <span class="badge badge-converted"><i class="fa fa-calendar-check-o"></i> Yes</span>
                @else
                    <span class="badge badge-unverified">No</span>
                @endif
            </td>
            <td style="white-space:nowrap;">
                @if($item->confirmation_sms_sent)
                    <span class="badge" style="background:#28a745;color:#fff;" title="Confirmation SMS sent"><i class="fa fa-comment"></i> Conf</span>
                @endif
                @if($item->reminder_sms_sent)
                    <span class="badge" style="background:#17a2b8;color:#fff;" title="Reminder SMS sent"><i class="fa fa-bell"></i> Rem</span>
                @endif
                @if(!$item->confirmation_sms_sent && !$item->reminder_sms_sent)
                    <span class="text-muted" style="font-size:11px;">-</span>
                @endif
            </td>
            <td style="white-space:nowrap;">
                <a href="{{ url('ai-call-logs/'.$item->id) }}" class="btn btn-primary action-btn" title="Review">
                    <i class="fa fa-eye"></i> Review
                </a>
            </td>
        </tr>
@empty
    <tr>
        <td colspan="12" class="text-center text-muted" style="padding:30px;">
            <i class="fa fa-inbox" style="font-size:24px;"></i><br>No records found
        </td>
    </tr>
@endforelse
<tr>
    <td colspan="12">
        {{ $list->appends(request()->query())->links() }}
    </td>
</tr>
