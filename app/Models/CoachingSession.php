<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachingSession extends Model
{
    protected $table = 'coaching_sessions'; // Tên bảng trong cơ sở dữ liệu

    protected $fillable = [
        'coach_id',
        'client_id',
    ];
    // Mối quan hệ với người dùng (coach)
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    // Mối quan hệ với người dùng (client)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
