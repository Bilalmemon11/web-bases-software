<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'amount',
        'method',
        'payment_date',
        'notes',
        // Bank / Cheque details
        'bank_name',
        'cheque_no',
        'account_no',
        'transaction_ref',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /**
     * Human-readable method label.
     */
    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash'          => 'Cash',
            'cheque'        => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'online'        => 'Online Transfer',
            default         => ucfirst($this->method),
        };
    }

    /**
     * A compact reference string shown in reports:
     *   Cheque  → "12345 MCB"
     *   Bank    → "MCB – AC# 001234"
     *   Online  → transaction ref
     *   Cash    → "—"
     */
    public function getReferenceAttribute(): string
    {
        return match ($this->method) {
            'cheque'        => trim("{$this->cheque_no} {$this->bank_name}"),
            'bank_transfer' => trim("{$this->bank_name}" . ($this->account_no ? " – AC# {$this->account_no}" : '')),
            'online'        => $this->transaction_ref ?? '—',
            default         => '—',
        };
    }
}