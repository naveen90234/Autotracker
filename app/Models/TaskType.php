<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    use HasFactory;

    protected $table = 'task_types'; // Ensure it matches your database table name

    protected $fillable = ['title', 'description'];

    /**
     * Get the car parts associated with this task type.
     */
    public function carParts()
    {
        return $this->belongsToMany(CarPart::class, 'car_part_task_type', 'task_type_id', 'car_part_id');
    }
}
