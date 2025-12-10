<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PredefinedUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'size',
        'cost_price',
        'default_sale_price'
    ];
}
