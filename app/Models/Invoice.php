<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Invoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'customer_contact',
        'customer_address',
        'status', // DRAFT, ISSUED, PAID, CANCELLED
        'type',   // PROFORMA, COMMERCIAL
        'issued_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'additional_tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'down_payment',
        'notes',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
