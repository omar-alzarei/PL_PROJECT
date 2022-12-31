<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        "evaluation_value"
    ];


    public function expert() {
        return $this->belongsTo(User::class, 'expert_Id', 'id');
    }
	 public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
