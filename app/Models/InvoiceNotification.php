<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'type',
        'sent_to',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'invoice_sent' => 'Invoice Sent',
            'payment_received' => 'Payment Received',
            'reminder' => 'Payment Reminder',
            'overdue' => 'Overdue Notice',
        ];

        return $labels[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getFormattedSentAtAttribute(): string
    {
        return $this->sent_at->format('M d, Y H:i A');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('sent_at', 'desc');
    }

    // Methods
    public static function log(Invoice $invoice, string $type, string $sentTo): self
    {
        return static::create([
            'invoice_id' => $invoice->id,
            'type' => $type,
            'sent_to' => $sentTo,
            'sent_at' => now(),
        ]);
    }

    public static function logInvoiceSent(Invoice $invoice, string $email): self
    {
        return static::log($invoice, 'invoice_sent', $email);
    }

    public static function logPaymentReceived(Invoice $invoice, string $email): self
    {
        return static::log($invoice, 'payment_received', $email);
    }

    public static function logReminder(Invoice $invoice, string $email): self
    {
        return static::log($invoice, 'reminder', $email);
    }

    public static function logOverdue(Invoice $invoice, string $email): self
    {
        return static::log($invoice, 'overdue', $email);
    }
}