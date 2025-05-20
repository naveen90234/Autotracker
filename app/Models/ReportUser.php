<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportUser extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reporter_id', 'group_code', 'description'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reporter(){
        return $this->belongsTo(User::class, 'reporter_id');
    }

}
