<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'tax_percentage',
        'discount_percentage',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Accessors
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTaxAmountAttribute(): float
    {
        return ($this->subtotal * $this->tax_percentage) / 100;
    }

    public function getDiscountAmountAttribute(): float
    {
        return ($this->subtotal * $this->discount_percentage) / 100;
    }

    public function getFormattedLineTotalAttribute(): string
    {
        return '$' . number_format($this->line_total, 2);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 2);
    }

    // Methods
    public function calculateLineTotal(): void
    {
        $subtotal = $this->quantity * $this->unit_price;
        $taxAmount = ($subtotal * $this->tax_percentage) / 100;
        $discountAmount = ($subtotal * $this->discount_percentage) / 100;

        $this->line_total = $subtotal + $taxAmount - $discountAmount;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateLineTotal();
        });

        static::saved(function ($item) {
            // Recalculate invoice totals when item is saved
            if ($item->invoice) {
                $item->invoice->calculateTotals();
                $item->invoice->saveQuietly(); // Save without triggering events
            }
        });

        static::deleted(function ($item) {
            // Recalculate invoice totals when item is deleted
            if ($item->invoice) {
                $item->invoice->calculateTotals();
                $item->invoice->saveQuietly();
            }
        });
    }
}