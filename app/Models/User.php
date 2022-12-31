<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;




class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'wallet',
        'is_expert',
        'consult_price',
        'image',
        'phone_number',
        'address',
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

    protected $with = ['consults', 'experiences'];
    /**
     * ......
     */
    //get response
    public function experiences() {
        return $this->hasMany(Experience::class, 'expert_id');
    }

    public function consults() {
        return $this->belongsToMany(Consult::class);
    }
    public function availableTimes() {
        return $this->hasMany(AvailableTime::class);
    }

    public function bookings() {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function appointments() {
        return $this->hasMany(Booking::class, 'expert_id');
    }

    public function messages() {
        return $this->hasMany(Message::class, "receiver_id");
    }

    public function favorites() {
        return $this->hasMany(Favorite::class);
    }
	public function evaluations() {
		return $this->hasMany(Evaluation::class, 'expert_id');
	}
	
	public function get_evaluations() {
		return $this->hasMany(Evaluation::class, 'user_id');
	}
}
