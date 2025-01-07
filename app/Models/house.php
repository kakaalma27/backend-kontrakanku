<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class house extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = ['path','name', 'price', 'description', 'tags', 'kamar', 'wc', 'available', 'user_id', 'quantity'];

  public function user()
  {
    return $this->belongsToMany(User::class, 'user_id', 'id');
  }

  public function addresses()
  {
    return $this->hasOne(address::class, 'house_id', 'id'); // Relasi one-to-one dengan Address
  }
}