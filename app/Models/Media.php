<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reference_id',  'media_type', 'media_path'];

    protected $appends = ['media_path_url'];

    public function getMediaPathUrlAttribute(){
        $imgs = $this->media_path;
        if($imgs != NULL){
            $imgs = base_url() . '/' . $imgs;
        }
        return $imgs;
    }
}
