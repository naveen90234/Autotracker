<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JWTAuth;

class UserInterest extends Model
{
    protected $table = 'user_interestes';
    protected $fillable = [
        'user_id', 'interest_id'
    ];

    public function interestname()
        {
            return $this->hasOne(Category::class, 'id', 'interest_id');
        }


   

}
