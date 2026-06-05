<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'agency_id',
        'payment_method',
        'transaction_id',
        'payment_gateway_response',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            'failed' => '<span class="badge badge-danger">Failed</span>',
            'refunded' => '<span class="badge badge-info">Refunded</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        $labels = [
            'stripe' => 'Credit/Debit Card',
            'paypal' => 'PayPal',
            'manual' => 'Manual Payment',
        ];

        return $labels[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    public function getCardDetailsAttribute(): array
    {
        if ($this->payment_method !== 'stripe' || !$this->payment_gateway_response) {
            return [];
        }

        $response = $this->payment_gateway_response;

        // Extract card details from Stripe response
        return [
            'brand' => $response['card_brand'] ?? null,
            'last4' => $response['card_last4'] ?? null,
            'exp_month' => $response['card_exp_month'] ?? null,
            'exp_year' => $response['card_exp_year'] ?? null,
            'funding' => $response['card_funding'] ?? null,
        ];
    }

    public function getPaypalDetailsAttribute(): array
    {
        if ($this->payment_method !== 'paypal' || !$this->payment_gateway_response) {
            return [];
        }

        $response = $this->payment_gateway_response;

        return [
            'payer_email' => $response['payer_email'] ?? null,
            'payer_id' => $response['payer_id'] ?? null,
            'payer_name' => $response['payer_name'] ?? null,
        ];
    }

    public function getFormattedCardNumberAttribute(): string
    {
        $cardDetails = $this->card_details;

        if (empty($cardDetails['last4'])) {
            return '';
        }

        return '**** **** **** ' . $cardDetails['last4'];
    }

    public function getCardBrandIconAttribute(): string
    {
        $cardDetails = $this->card_details;
        $brand = strtolower($cardDetails['brand'] ?? '');

        $icons = [
            'visa' => 'mdi-credit-card',
            'mastercard' => 'mdi-credit-card',
            'amex' => 'mdi-credit-card',
            'discover' => 'mdi-credit-card',
            'jcb' => 'mdi-credit-card',
            'diners' => 'mdi-credit-card',
            'unionpay' => 'mdi-credit-card',
        ];

        return $icons[$brand] ?? 'mdi-credit-card';
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Methods
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // Check if invoice is fully paid
        if ($this->invoice->balance <= 0) {
            $this->invoice->markAsPaid();
        }
    }

    public function markAsFailed(string $reason = null): void
    {
        $response = $this->payment_gateway_response ?? [];
        if ($reason) {
            $response['failure_reason'] = $reason;
        }

        $this->update([
            'status' => 'failed',
            'payment_gateway_response' => $response,
        ]);
    }

    public function refund(float $amount = null): void
    {
        $refundAmount = $amount ?? $this->amount;

        $this->update([
            'status' => 'refunded',
            'amount' => $this->amount - $refundAmount,
        ]);

        // Create a new refund record
        static::create([
            'invoice_id' => $this->invoice_id,
            'payment_method' => $this->payment_method,
            'transaction_id' => 'REFUND-' . $this->transaction_id,
            'amount' => -$refundAmount,
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($payment) {
            if ($payment->wasChanged('status') && $payment->status === 'completed') {
                // Update invoice status if fully paid
                $totalPaid = $payment->invoice->payments()->completed()->sum('amount');
                if ($totalPaid >= $payment->invoice->total_amount) {
                    $payment->invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            }
        });
    }
}