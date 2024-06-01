<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseMuscle extends Model
{
    protected $table = 'exercise_muscle';

    // Định nghĩa các trường có thể được mass assignable
    protected $fillable = ['exercise_id', 'muscle_id'];

    // Nếu bạn không muốn sử dụng tính năng timestamps, bạn có thể vô hiệu hóa nó
    // public $timestamps = false;

    // Các phương thức quan hệ với các model khác (nếu cần)
    // ...
}
