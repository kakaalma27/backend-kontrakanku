<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class houseImage extends Model
{
    use HasFactory;
    protected $fillable = ['path', 'house_id'];
    public function houses() {
        return $this->belongsTo(house::class, 'house_id', 'id');
      }
}
