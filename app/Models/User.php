<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birth_date',
        'is_admin',
        'level'

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
        'password' => 'hashed',
    ];

    protected $append = [
        'image',
    ];
    //some eloquent relations
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function referral_link()
    {
        return $this->hasOne(ReferralLink::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    //image attribute for the user
    public function getImageAttribute()
    {
        $image = $this->getMedia('user')->first();
        if ($image) {
            return $image->getFullUrl();
        }
        //if there is no image return default image to avoid errors
        return asset("images/default_image.png");
    }
}
