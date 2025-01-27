<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordReset extends Model
{
    use HasFactory;

    protected $table = 'password_resets';
    protected $fillable = ['email', 'token', 'is_verified', 'attempts'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return Carbon::instance($date)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }
}
