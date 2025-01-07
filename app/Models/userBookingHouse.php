<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class userBookingHouse extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'house_id', 'status', 'start_date', 'end_date'];
    public function user()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }
  
    public function house()
    {
      return $this->hasOne(house::class, 'house_id', 'id');
    }
}