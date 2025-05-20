<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'year', 'model', 'make', 'vehicle_images', 'track_by', 'vehicle_nickname', 'identification_number', 'current_miles'
    ];

    protected $casts = [
        'vehicle_images' => 'array',
    ];
    
}
