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
        'paid_amount',
        'status',
        'discount',
        'payment_method',
        'sale_date',
        'has_installments',
        'installment_count',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'has_installments' => 'boolean',
    ];

    // Relationships
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

    public function installments()
    {
        return $this->hasMany(Installment::class)->orderBy('installment_number');
    }

    // Computed helpers
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->net_amount - $this->paid_amount);
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->remaining_amount <= 0;
    }

    public function getNetAmountAttribute()
    {
        return max(0, $this->total_amount - $this->discount);
    }

    public function getPendingAmountAttribute()
    {
        return max(0, $this->net_amount - $this->paid_amount);
    }

    // Helper to check if all installments are paid
    public function getInstallmentsFullyPaidAttribute()
    {
        if (!$this->has_installments) {
            return null;
        }
        
        return $this->installments()->where('status', '!=', 'paid')->count() === 0;
    }
}