<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    use HasFactory;

    // Đặt tên bảng
    protected $table = 'nutrients';

    // Định nghĩa các cột được phép điền thông tin (mass assignable)
    protected $fillable = [
        'name',
        'description',
    ];


    // Định nghĩa các quan hệ với các model khác nếu có (ví dụ: belongsToMany, hasMany, etc.)
}

