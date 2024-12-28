<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class userProfile extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = ['user_id', 'name', 'phone', 'jenis_kelamin', 'alamat'];

  public function User()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}
