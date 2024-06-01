<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'muscle_id', 'description', 'image'];


    public function muscle()
    {
        return $this->belongsTo(Muscle::class, 'muscle_id');
    }
    public function muscles()
    {
        return $this->belongsToMany(Muscle::class, 'exercise_muscle');
    }
}
