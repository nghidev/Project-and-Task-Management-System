<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Muscle extends Model
{
    use HasFactory;

    // Table name explicitly defined, not necessary if the convention is followed
    protected $table = 'muscles';

    // Mass assignable attributes
    protected $fillable = [
        'name',
        'muscle_group_id',
        'description',
        'image',
    ];

    // Indicates if the model should be timestamped. Default is true.
    public $timestamps = true;

    /**
     * The "booted" method of the model.
     * This is a great spot to attach relationship-related events, 
     * like deleting all related records if this one is deleted.
     */
    protected static function booted()
    {
        static::deleting(function ($muscle) {
            // Handle related records deletion logic here if necessary
        });
    }

    /**
     * Get the muscle group that the muscle belongs to.
     */
    public function muscleGroup()
    {
        return $this->belongsTo(MuscleGroup::class);
    }

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'exercise_muscle');
    }

    // Additional methods, relationships, etc. can be added below as needed
}
