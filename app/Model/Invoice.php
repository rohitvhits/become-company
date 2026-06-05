<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'agency_id',
        'type',
        'status',
        'title',
        'description',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'discount_percentage',
        'discount_amount',
        'total_amount',
        'due_date',
        'terms_conditions',
        'pdf_path',
        'created_by',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected $dates = [
        'due_date',
        'sent_at',
        'paid_at',
    ];

    protected static function booted()
    {
        static::deleting(function ($model) {
            // Only set deleted_by for soft deletes, not force deletes
            if (!$model->isForceDeleting() && Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->del_flag = 'Y';
                $model->saveQuietly(); // avoids recursion with delete()
            }
        });

        static::restoring(function ($model) {
            $model->deleted_by = null; // clear on restore if you want
        });
    }

    // Relationships
    public function agency(): BelongsTo
    {
        return $this->belongsTo(\App\Agency::class, 'agency_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(InvoiceNotification::class);
    }

    // Accessors & Mutators
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'draft' => '<span class="badge badge-secondary">Draft</span>',
            'sent' => '<span class="badge badge-primary">Sent</span>',
            'paid' => '<span class="badge badge-success">Paid</span>',
            'overdue' => '<span class="badge badge-danger">Overdue</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date < Carbon::now();
    }

    public function getDaysUntilDueAttribute(): int
    {
        return Carbon::now()->diffInDays($this->due_date, false);
    }

    // Scopes
    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                    ->where('due_date', '<', Carbon::now());
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->where('status', '!=', 'paid')
                    ->whereBetween('due_date', [Carbon::now(), Carbon::now()->addDays($days)]);
    }

    // Methods
    public function calculateTotals(): void
    {
        if ($this->type === 'detailed') {
            $this->subtotal = $this->items->sum('line_total');
        }

        $this->tax_amount = ($this->subtotal * $this->tax_percentage) / 100;
        $this->discount_amount = ($this->subtotal * $this->discount_percentage) / 100;
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => Carbon::now(),
        ]);
    }

    public function markAsPaid(float $amount = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        // Create payment record
        $this->payments()->create([
            'payment_method' => 'manual',
            'amount' => $amount ?? $this->total_amount,
            'status' => 'completed',
            'paid_at' => Carbon::now(),
        ]);
    }

    public function generateInvoiceNumber(): string
    {
        $year = Carbon::now()->year;
        $lastInvoice = static::whereYear('created_at', $year)
                           ->orderBy('id', 'desc')
                           ->first();

        $nextNumber = $lastInvoice ?
                     (int) substr($lastInvoice->invoice_number, -4) + 1 :
                     1;

        return 'INV-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()
                   ->where('status', 'completed')
                   ->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_amount - $this->total_paid;
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeDeleted(): bool
    {
        return $this->status === 'draft';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = $invoice->generateInvoiceNumber();
            }
        });

        static::updating(function ($invoice) {
            if ($invoice->isDirty(['status']) && $invoice->status === 'overdue') {
                // Auto-update overdue status based on due date
                if ($invoice->due_date >= Carbon::now() || $invoice->status === 'paid') {
                    $invoice->status = $invoice->getOriginal('status');
                }
            }
        });
    }
}