<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class address extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','house_id','category','name', 'phone', 'alamat', 'detail'];

    public function user()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function house()
    {
        return $this->belongsTo(House::class, 'house_id', 'id'); // Menambahkan relasi ke House
    }

}