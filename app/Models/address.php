<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class address extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','address_categotie_id', 'phone', 'alamat', 'jalan', 'detail'];

    public function user()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function address_category()
    {
      return $this->belongsTo(User::class, 'address_categotie_id', 'id');
    }
}