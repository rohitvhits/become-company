@foreach($invoices as $invoice)
    <tr>
        <td class="text-center">
            <div class="form-check d-flex justify-content-center">
                <input class="form-check-input invoice-checkbox" type="checkbox"
                       value="{{ $invoice->id }}" data-status="{{ $invoice->status }}"
                       style="transform: scale(1.2); margin: 0;">
            </div>
        </td>
        <td>
            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-decoration-none font-weight-bold" style="color: #36a9f3;">
                {{ $invoice->invoice_number }}
            </a>
            @if($invoice->title)
                <br><small class="text-muted">{{ Str::limit($invoice->title, 30) }}</small>
            @endif
        </td>
        <td>
            <div class="font-weight-semibold">{{ $invoice->agency->agency_name }}</div>
            <small class="text-muted">{{ $invoice->agency->email }}</small>
        </td>
        <td class="text-muted">{{ $invoice->created_at->format('M d, Y') }}</td>
        <td>
            <span class="{{ $invoice->is_overdue ? 'text-danger font-weight-bold' : 'text-muted' }}">
                {{ $invoice->due_date->format('M d, Y') }}
            </span>
            @if($invoice->is_overdue)
                <br><small class="text-danger">({{ abs($invoice->days_until_due) }} days overdue)</small>
            @endif
        </td>
        <td>
            <span class="font-weight-bold invoice-amount">${{ number_format($invoice->total_amount, 2) }}</span>
            @if($invoice->total_paid > 0 && $invoice->status !== 'paid')
                <br><small class="text-success">${{ number_format($invoice->total_paid, 2) }} paid</small>
            @endif
        </td>
        <td>
            @if($invoice->status === 'paid')
                <span class="invoice-status-badge paid invoice-action-btn">
                    <i class="mdi mdi-check me-1"></i>Paid
                </span>
            @elseif($invoice->status === 'overdue')
                <span class="invoice-status-badge overdue invoice-action-btn">
                    <i class="mdi mdi-alert-triangle me-1"></i>Overdue
                </span>
            @elseif($invoice->status === 'sent')
                <span class="invoice-status-badge sent invoice-action-btn">
                    <i class="mdi mdi-send me-1"></i>Sent
                </span>
            @else
                <span class="invoice-status-badge draft invoice-action-btn">
                    <i class="mdi mdi-pencil me-1"></i>{{ ucfirst($invoice->status) }}
                </span>
            @endif
        </td>
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.invoices.show', $invoice) }}"
                   class="invoice-badge-btn btn-primary btn-sm invoice-action-btn" title="View">
                    <i class="mdi mdi-eye"></i>
                </a>
                @if($invoice->canBeEdited())
                    <a href="{{ route('admin.invoices.edit', $invoice) }}"
                       class="invoice-badge-btn btn-warning btn-sm invoice-action-btn" title="Edit">
                        <i class="mdi mdi-pencil"></i>
                    </a>
                @endif
                @if($invoice->status === 'draft')
                    <button type="button" class="invoice-badge-btn btn-info btn-sm invoice-action-btn"
                            onclick="sendInvoice({{ $invoice->id }}, this)" title="Send">
                        <i class="mdi mdi-send"></i>
                    </button>
                @endif
                @if($invoice->status !== 'paid')
                    <button type="button" class="invoice-badge-btn btn-success btn-sm invoice-action-btn"
                            onclick="markAsPaid({{ $invoice->id }}, {{ $invoice->total_amount }}, '{{ $invoice->invoice_number }}')" title="Mark as Paid">
                        <i class="mdi mdi-check"></i>
                    </button>
                @endif
                <div class="btn-group">
                    <button type="button" class="invoice-badge-btn btn-secondary btn-sm invoice-action-btn dropdown-toggle"
                            data-toggle="dropdown" title="More">
                        <i class="mdi mdi-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.invoices.download', $invoice) }}">
                            <i class="mdi mdi-download me-2"></i>Download PDF
                        </a></li>
                        @if($invoice->status === 'sent')
                            <li><button class="dropdown-item" onclick="sendInvoice({{ $invoice->id }}, this)">
                                <i class="mdi mdi-send me-2"></i>Resend Email
                            </button></li>
                        @endif
                        @if($invoice->canBeDeleted())
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="deleteInvoice({{ $invoice->id }}, this)">
                                <i class="mdi mdi-delete me-2"></i>Delete
                            </button></li>
                        @endif
                    </ul>
                </div>
            </div>
        </td>
    </tr>
@endforeach