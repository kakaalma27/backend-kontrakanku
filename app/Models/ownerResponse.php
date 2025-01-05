<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ownerResponse extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'complaint_id', 'response'];
    public function user()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }
  
    public function userComplaint()
    {
      return $this->belongsTo(userComplaint::class, 'complaint_id', 'id');
    }
}
