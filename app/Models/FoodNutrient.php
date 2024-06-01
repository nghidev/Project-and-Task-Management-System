<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodNutrient extends Model
{
    use HasFactory;

    protected $table = 'food_nutrient';

    protected $fillable = [
        'food_id',
        'nutrient_id',
        'description',
    ];

    // Các mối quan hệ nếu có
}
