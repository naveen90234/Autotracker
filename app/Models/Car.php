<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model {
    use HasFactory;

    protected $fillable = ['year', 'make', 'model'];

    public function parts()
{
    return $this->belongsToMany(CarPart::class, 'car_part_car');
}

}
