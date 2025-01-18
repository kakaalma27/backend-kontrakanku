<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserVerification extends Model
{
  use HasFactory;
  protected $fillable = ['user_id', 'phone', 'verification_code', 'is_verified', 'expires_at', 'attempts'];

  public function User()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}