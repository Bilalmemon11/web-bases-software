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
    ];

    protected $casts = [
        'sale_date' => 'date',
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
        return $this->belongsToMany(Unit::class, 'sale_units') // 👈 same fix here
            ->withPivot('unit_price')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Computed helpers
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount - $this->discount;
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
}
