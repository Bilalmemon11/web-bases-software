<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'cnic',
        'address',
        'notes',
        'investment_amount',
        'is_manager',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('investment_amount', 'profit_share', 'role')
            ->withTimestamps();
    }

}
