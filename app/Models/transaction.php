<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class transaction extends Model
{
  use HasFactory;
  protected $table = 'transactions_houses';
  protected $fillable = ['user_id', 'house_id', 'booking_id', 'payment', 'price', 'status'];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function house()
  {
      return $this->belongsTo(house::class, 'house_id', 'id');
  }

  public function bookings()
  {
      return $this->belongsTo(userBookingHouse::class, 'booking_id', 'id');
  }
}