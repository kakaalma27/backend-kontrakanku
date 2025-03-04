<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ownerInputKeuangan extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'metode','price', 'start_date', 'end_date'];
    public function user()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }
  
}
