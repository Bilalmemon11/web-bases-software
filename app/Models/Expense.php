<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'category', 'description', 'amount', 'expense_date', 'attachment'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
