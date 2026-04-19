<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'installment_id',
        'payment_date',
        'amount',
        'payment_method',
        'cheque_no',
        'bank',
        'description',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    // Auto-sync with sale paid_amount when payment is created/updated/deleted
    protected static function booted()
    {
        static::created(function ($payment) {
            $payment->syncSalePaidAmount();
        });

        static::updated(function ($payment) {
            $payment->syncSalePaidAmount();
        });

        static::deleted(function ($payment) {
            $payment->syncSalePaidAmount();
        });
    }

    public function syncSalePaidAmount()
    {
        $sale = $this->sale;
        
        // Calculate total paid from all payments
        $totalPaid = $sale->payments()->sum('amount');
        
        // Update sale paid_amount
        $sale->update(['paid_amount' => $totalPaid]);

        // If this payment is for an installment, update installment amount_paid
        if ($this->installment_id) {
            $installment = $this->installment;
            $installmentPaid = $installment->payments()->sum('amount');
            $installment->update(['amount_paid' => $installmentPaid]);
        }
    }
}