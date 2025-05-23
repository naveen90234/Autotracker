<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'profile_picture', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['profile_picture_url'];

    public function getProfilePictureUrlAttribute()
    {
        $img = $this->profile_picture;
        if ($img != NULL) {
            $img = base_url() . '/' . $img;
        }
        return $img;
    }
}
