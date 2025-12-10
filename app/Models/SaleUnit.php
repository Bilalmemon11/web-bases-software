<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleUnit extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'unit_id', 'unit_price'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
