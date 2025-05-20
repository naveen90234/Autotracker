<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JWTAuth;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name', 'slug', 'image'
    ];

    protected $hidden = ['image'];

    // protected $appends = ['category_image_url'];


    public function setSlugAttribute($value)
    {
        if (static::whereSlug($slug = Str::slug($value))->exists()) {

            $slug = $this->incrementSlug($slug);
        }

        $this->attributes['slug'] = $slug;
    }


    public function incrementSlug($slug)
    {

        $original = $slug;

        $count = 2;

        while (static::whereSlug($slug)->exists()) {

            $slug = "{$original}-" . $count++;
        }

        return $slug;
    }

    public function getCategoryImageUrlAttribute()
    {
        $img = $this->image;
        if ($img != NULL) {
            $img = base_url() . '/' . $img;
        }
        return $img;
    }

}
