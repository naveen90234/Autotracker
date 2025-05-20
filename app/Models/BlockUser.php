<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'blocked_by',
        'blocked_to',
        'group_code'
    ];

    function blockedByUser(){
        return $this->belongsTo(User::class, 'blocked_by');
    }

    function blockedToUser(){
        return $this->belongsTo(User::class, 'blocked_to');
    }
}
