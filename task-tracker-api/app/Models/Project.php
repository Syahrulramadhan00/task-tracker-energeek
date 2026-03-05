<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'name',
        'description',
        'status' // active atau archived
    ];

    // Relasi ke Task (Satu project punya banyak task)
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Relasi ke User pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}  
