<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class address extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'category','name', 'phone', 'alamat', 'detail'];

    public function user()
    {
      return $this->belongsToMany(User::class, 'user_id', 'id');
    }
    public function houses()
    {
        return $this->belongsToMany(house::class, 'house_id');
    }

}