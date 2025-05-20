<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'driving_style_id',
        'service_name',
        'service_date',
        'service_mileage',
        'service_cost',
        'note',
        'document'
    ];

    protected $casts = [
        'parts_list_id_cost' => 'array',
    ];
}
