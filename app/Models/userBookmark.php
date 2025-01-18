<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class userBookmark extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'house_id'];
    protected $dates = ['deleted_at'];
    public function user()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function house()
    {
      return $this->belongsTo(house::class, 'house_id', 'id');
    }
}