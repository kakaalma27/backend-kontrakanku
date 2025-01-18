<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class userNotifications extends Model
{
  use HasFactory;
  protected $fillable = ['user_id', 'title', 'massage'];

  public function User()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}