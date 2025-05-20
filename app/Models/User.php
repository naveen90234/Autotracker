<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'email',
        'dob',
        'mobile',
        'country_code_name',
        'country_code',
        'mobile_number',
        'profile_picture',
        'location',
        'age',
        'bio',
        'password',
        'remember_token',
        'status',
        'notification_status',
        'location_status',
        'latitude',
        'longitude',
        'otp_verify',
        'verified_at',
        'email_verified_at',
        'is_two_factor',
        'online',
        'plan_id',
        'is_premium',
        'is_free_plan',
        'is_subscription_expired',
        'garagecount',
        'zip_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_picture_url',
        'profile_picture_thumb_url',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getProfilePictureUrlAttribute()
    {
        $img = $this->profile_picture;
        if ($img != NULL) {
            $img = base_url() . '/' . $img;
        }
        return $img;
    }

    public function getProfilePictureThumbUrlAttribute()
    {
        $img = $this->profile_picture;
        if ($img != NULL) {
            $img = base_url() . '/' . str_replace($s=strrchr($img, '/'), "/thumb$s", $img);
        }
        return $img;
    }

    public function getDobAttribute($value)
    {
        if($value != NULL){
            return Carbon::parse($value)->format('m/d/Y');
        }
        return $value;
    }

      public function userinterest()
        {
            return $this->hasMany(UserInterest::class, 'user_id', 'id')->with('interestname');
        }

}
