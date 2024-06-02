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
        'project_id',
        'name',
        'description',
        'status',
        'due_date',
        'create_by',
        'assigned_user_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
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

    // Helper để lấy trạng thái dưới dạng text
    public function getStatusTextAttribute()
    {
        $statuses = [
            0 => 'Mới',
            1 => 'Đang làm',
            2 => 'Hoàn thành',
        ];

        return $statuses[$this->status];
    }
}
