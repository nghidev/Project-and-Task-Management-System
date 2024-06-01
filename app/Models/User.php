<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // Mối quan hệ với coaching sessions khi là người hướng dẫn (coach)
    public function coachedSessions()
    {
        return $this->hasMany(CoachingSession::class, 'coach_id');
    }

    // Mối quan hệ với coaching sessions khi là người được hướng dẫn (client)
    public function clientSessions()
    {
        return $this->hasMany(CoachingSession::class, 'client_id');
    }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_user');
    }
  
}
