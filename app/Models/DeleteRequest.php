<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteRequest extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'reason', 'user_type'];
}
