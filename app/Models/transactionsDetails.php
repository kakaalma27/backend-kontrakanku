<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class transactionsDetails extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = ['user_id', 'house_id', 'payment_id'];

  public function User()
  {
    return $this->hasMany(User::class, 'user_id', 'id');
  }

  public function houses()
  {
    return $this->hasMany(house::class, 'user_id', 'id');
  }

  public function transactions()
  {
    return $this->hasMany(transaction::class, 'payment_id', 'id');
  }
}
