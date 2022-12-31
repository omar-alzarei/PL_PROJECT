<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = ['from', 'to' , 'expert_id'];
	
	protected $with = ['expert'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function expert() {
        return $this->belongsTo(User::class, 'expert_id', 'id');
    }
}
