<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MuscleGroup extends Model
{
    use HasFactory;

    // Đặt tên bảng nếu muốn khai báo rõ ràng (không bắt buộc vì Laravel tự định nghĩa theo quy tắc)
    protected $table = 'muscle_groups';

    // Định nghĩa các trường có thể được mass assigned
    protected $fillable = ['name', 'description', 'slug'];

    // Các thêm các method cho các relationships, accessors, mutators, etc. ở đây

    public function muscles()
    {
        return $this->hasMany(Muscle::class, 'muscle_group_id');
    }
}
