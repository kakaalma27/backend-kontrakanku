<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class house extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = ['name', 'price', 'description', 'tags', 'kamar', 'wc', 'available', 'user_id', 'quantity'];

  public function houseCategory()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function images()
  {
    return $this->hasMany(houseImage::class, 'house_id', 'id');
  }
}
