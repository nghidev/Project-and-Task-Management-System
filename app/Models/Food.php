<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'foods';

    protected $fillable = [
        'name',
        'description',
        'calo',
        'image'
    ];
    // Bạn có thể thêm các phương thức khác ở đây (như relationships, scopes,...)

    // public function nutrients()
    // {
    //     return $this->belongsToMany(Nutrient::class, 'food_nutrient');
    // }

    public function nutrients()
    {
        return $this->belongsToMany(Nutrient::class)->withPivot('description');
    }
    
}
