<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Wajib untuk soft delete

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'category_id',
        'created_by',
        'deleted_by', // Wajib diisi saat soft delete
        'title',
        'description',
        'due_date'
    ];

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relasi ke User pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}