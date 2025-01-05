<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class transactionsDetails extends Model
{
  use HasFactory, SoftDeletes;
  protected $table = 'user_transactions_details_houses';
  protected $fillable = ['user_id', 'house_id', 'payment_id', 'booking_id'];

  public function User()
  {
    return $this->hasMany(User::class, 'user_id', 'id');
  }

  public function houses()
  {
    return $this->hasMany(house::class, 'house_id', 'id');
  }
  public function booking()
  {
    return $this->hasMany(userBookingHouse::class, 'booking_id', 'id');
  }

  public function transactions()
  {
    return $this->hasMany(transaction::class, 'payment_id', 'id');
  }
}
