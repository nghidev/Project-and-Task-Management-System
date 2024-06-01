<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'name',
        'create_by',
    ];

    // Phương thức quan hệ với User qua khóa ngoại 'create_by'
    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    // Phương thức quan hệ với nhiều User qua bảng trung gian
    public function users()
    {
        return $this->belongsToMany(User::class, 'unit_user');
    }

    // Unit.php
    public function projects()
    {
        return $this->hasMany(Project::class, 'unit_id');
    }
}
