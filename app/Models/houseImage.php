<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class houseImage extends Model
{
  use HasFactory, SoftDeletes;
  protected $fillable = ['house_id', 'url'];

  public function house()
  {
    return $this->belongsTo(house::class, 'house_id', 'id');
  }
}
