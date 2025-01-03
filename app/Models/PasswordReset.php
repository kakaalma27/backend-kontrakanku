<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
  protected $table = 'password_resets';
  protected $fillable = ['email', 'token', 'is_verified', 'attempts'];

  protected $dates = ['created_at', 'updated_at'];
}
