<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'current_miles',
        'remind_me',
        'remind_me_miles',
        'notification_type',
        'notification_time',
        'last_service_id',
        'note',
        'car_id'
    ];
}
