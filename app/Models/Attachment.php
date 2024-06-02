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
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
