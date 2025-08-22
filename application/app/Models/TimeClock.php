<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeClock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'clocked_at', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'clocked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedClockedAtAttribute()
    {
        return $this->clocked_at->format('d/m/Y H:i:s');
    }
}