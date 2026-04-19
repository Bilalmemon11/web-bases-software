<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'name',
        'phone',
        'cnic',
        'address',
        'notes'
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
