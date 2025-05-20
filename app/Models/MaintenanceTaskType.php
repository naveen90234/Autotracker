<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceTaskType extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status'];

    public function carParts()
{
    return $this->belongsToMany(CarPart::class, 'maintenance_task_type_car_part', 'maintenance_task_type_id', 'car_part_id');
}

}
