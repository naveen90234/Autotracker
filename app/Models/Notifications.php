<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $fillable = [
        'user_id', 'sender_id', 'type', 'title', 'message', 'reference_id', 'is_seen', 'notification_image',  'notification_send_time'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'sender_id');
    }
}