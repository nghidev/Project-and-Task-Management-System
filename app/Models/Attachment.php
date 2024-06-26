<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'filename',
        'filepath',
        'filetype',
        'uploaded_by',
    ];

    // Mối quan hệ với model Task
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Mối quan hệ với model User
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
