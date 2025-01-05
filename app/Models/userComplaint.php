<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class userComplaint extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'status'];
    public function user()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function addresses()
    {
      return $this->belongsTo(address::class, 'user_id', 'id');
    }

    public function house()
    {
      return $this->belongsTo(house::class, 'user_id', 'id');
    }
}
