<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'generated_at',
        'file_path'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
