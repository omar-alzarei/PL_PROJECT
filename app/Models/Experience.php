<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\experiences as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Experience extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'exp_description',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'expert_id');
    }
}
