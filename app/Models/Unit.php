<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'unit_no',
        'type',
        'size',
        'cost_price',
        'sale_price',
        'status'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }


    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_units') // 👈 specify correct table name
            ->withPivot('unit_price')
            ->withTimestamps();
    }

    // Helper
    public function getIsSoldAttribute()
    {
        return $this->status === 'sold';
    }

    // Automatically set unit_no when creating a new record
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($unit) {
            // Only generate if not provided
            if (empty($unit->unit_no)) {
                $unit->unit_no = static::generateMeaningfulUnitNo($unit->project_id);
            }
        });
    }

    /**
     * Generate a human-readable sequential unit number.
     *
     * @param  int|null  $projectId
     * @return string
     */
    public static function generateMeaningfulUnitNo($projectId = null)
    {
        $query = static::query();

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $lastUnit = $query->orderByDesc('id')->first();

        $letter = 'A';
        $number = 1;

        if ($lastUnit && preg_match('/Unit-([A-Z])-?(\d+)/i', $lastUnit->unit_no, $matches)) {
            $letter = $matches[1];
            $number = intval($matches[2]) + 1;

            // If number exceeds 99, move to next letter
            if ($number > 99) {
                $letter = chr(ord($letter) + 1);
                $number = 1;
            }
        }

        return sprintf('Unit-%s%02d', $letter, $number);
    }

    /**
     * Get the sale through which this unit was sold.
     */
    public function soldSale()
    {
        return $this->sales()->where('sales.status', 'sold')->latest()->first();
    }

    /**
     * Get the sale through which this unit was reserved.
     */
    public function reservedSale()
    {
        return $this->sales()->where('sales.status', 'reserved')->latest()->first();
    }

    /**
     * Get the client who bought this unit.
     */
    public function soldTo()
    {
        $sale = $this->soldSale();
        return $sale ? $sale->client : null;
    }

    /**
     * Get the client who reserved this unit.
     */
    public function reservedBy()
    {
        $sale = $this->reservedSale();
        return $sale ? $sale->client : null;
    }
}
