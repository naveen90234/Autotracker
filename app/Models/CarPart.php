<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarPart extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function cars()
    {
        return $this->belongsToMany(Car::class, 'car_part_car');
    }

    public function maintenanceTaskTypes()
    {
        return $this->belongsToMany(MaintenanceTaskType::class, 'car_part_task_type', 'car_part_id', 'task_type_id');
    }

    public function taskTypes()
{
    return $this->belongsToMany(TaskType::class, 'car_part_task_type', 'car_part_id', 'task_type_id');
}

}
