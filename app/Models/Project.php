<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'name',
        'create_by',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'project_user');
    // }

    // app/Models/Project.php
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }


    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Project.php
    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by');
    }
}
