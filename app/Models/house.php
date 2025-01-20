<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class house extends Model
{
  use HasFactory;
  protected $fillable = ['name', 'price', 'description', 'tags', 'kamar', 'wc', 'available', 'user_id', 'quantity', 'address_id'];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function addresses()
  {
      return $this->belongsTo(address::class, 'address_id', 'id');
  }
  public function bookings() {
    return $this->hasMany(UserBookingHouse::class);
  }
  public function transactions() {
    return $this->hasMany(transaction::class);
  }

  public function houseImage() {
    return $this->hasMany(houseImage::class);
  }
}