<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'installment_number',
        'amount_due',
        'amount_paid',
        'due_date',
        'status',
        'description',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Computed attributes
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->amount_due - $this->amount_paid);
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->remaining_amount <= 0;
    }

    // Auto-update status when amount_paid changes
    protected static function booted()
    {
        static::saving(function ($installment) {
            if ($installment->amount_paid >= $installment->amount_due) {
                $installment->status = 'paid';
            } elseif ($installment->amount_paid > 0) {
                $installment->status = 'partial';
            } else {
                $installment->status = 'pending';
            }
        });
    }
}