<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            $project->slug = Str::slug($project->name);
        });
    }

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'total_investment',
        'land_cost',
        'sale_price',
        'status',
    ];

    /* -----------------------------------------------------------------
     | Relationships
     |----------------------------------------------------------------- */

    public function members()
    {
        return $this->belongsToMany(Member::class, 'project_members')
            ->withPivot('investment_amount', 'profit_share', 'role')
            ->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function reports()
    {
        return $this->hasMany(ProjectReport::class);
    }

    /* -----------------------------------------------------------------
     | Computed Attributes
     |----------------------------------------------------------------- */

    /**
     * Get total expenses (including land cost).
     */
    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount') + ($this->land_cost ?? 0);
    }

    /**
     * Get total sales revenue from sale_units pivot table.
     */
    public function getTotalSalesAttribute()
    {
        // Gross sales (before discount)
        return DB::table('sale_units')
            ->join('sales', 'sales.id', '=', 'sale_units.sale_id')
            ->where('sales.project_id', $this->id)
            ->sum('sale_units.unit_price');
    }

    public function getTotalDiscountsAttribute()
    {
        return $this->sales()->sum('discount');
    }
    /**
     * Get total amount received from all sales (paid amount).
     */
    public function getTotalReceivedAttribute()
    {
        return $this->sales()->sum('paid_amount');
    }

    /**
     * Get total remaining balance from all sales.
     */
    public function getTotalPendingAttribute()
    {
        return $this->sales->sum(fn($sale) => $sale->net_amount - $sale->paid_amount - $sale->discount);
    }

    /**
     * Get overall profit for the project.
     */
    public function getProfitAttribute()
    {
        // Use discounted (net) total for projected profit
        return ($this->total_sales - $this->total_discounts) - $this->total_expenses;
    }

    /**
     * Get sales progress percentage (sold units / total units).
     */
    public function getProgressAttribute()
    {
        $total = $this->units()->count();
        $sold = $this->units()->where('status', 'sold')->count();

        return $total ? round(($sold / $total) * 100, 2) : 0;
    }

    /**
     * Get average sale price per unit (useful for reports).
     */
    public function getAverageSalePriceAttribute()
    {
        $soldUnits = $this->units()->where('status', 'sold')->count();
        return $soldUnits ? round($this->total_sales / $soldUnits, 2) : 0;
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
