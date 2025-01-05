<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class house extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = ['url','name', 'price', 'description', 'tags', 'kamar', 'wc', 'available', 'user_id', 'quantity'];
  protected $casts = [
    'url' => 'array', 
    'available' => 'boolean', 
    'kamar' => 'integer', 
    'wc' => 'integer', 
    'quantity' => 'integer',
  ];
  public function addresses()
  {
    return $this->belongsTo(address::class, 'user_id', 'id');
  }
}
