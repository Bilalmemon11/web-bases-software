<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_id',
        'total_amount',
        'status',
        'discount',
        'payment_method',
        'sale_date',
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'sale_units')
            ->withPivot('unit_price')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('payment_date');
    }

    // ----------------------------------------------------------------
    // Computed Attributes
    // ----------------------------------------------------------------

    /**
     * Total actually paid — sum of all payment records.
     */
    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * Net amount after discount.
     */
    public function getNetAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->discount);
    }

    /**
     * Outstanding balance.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->net_amount - $this->paid_amount);
    }

    /**
     * Is the sale fully paid?
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_amount <= 0;
    }

    /**
     * Payment progress as a percentage.
     */
    public function getPaymentProgressAttribute(): float
    {
        if ($this->net_amount <= 0) return 100;
        return round(($this->paid_amount / $this->net_amount) * 100, 1);
    }
}