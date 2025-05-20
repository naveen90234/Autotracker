<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model {
    
	 protected $fillable = [
        'slug','title','description','created_at','updated_at'
    ];
	
}
