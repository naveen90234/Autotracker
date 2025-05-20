<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersionControlSetting extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['field_title', 'field_name', 'field_type', 'value'];

}
