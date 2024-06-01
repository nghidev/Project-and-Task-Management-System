<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'is_deadline',
        'done_deadline',
        'create_by',
        'assigned_user_id',
        'project_id', // Đảm bảo thêm thuộc tính này vào fillable
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id'); // Thêm mối quan hệ này
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    // Hook vào sự kiện creating để kiểm tra logic
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            // Kiểm tra xem assigned_user_id có trong project_user không
            $exists = DB::table('project_user')
                        ->where('project_id', $task->project_id)
                        ->where('user_id', $task->assigned_user_id)
                        ->exists();

            if (!$exists) {
                throw ValidationException::withMessages([
                    'assigned_user_id' => 'The assigned user must be part of the project.'
                ]);
            }
        });
    }
}
