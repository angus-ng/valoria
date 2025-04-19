<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCrown extends Model
{
    protected $fillable = [
        'user_id',
        'crown_id',
        'obtained',
        'obtained_at',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function crown()
    {
        return $this->belongsTo(\App\Models\Crown::class);
    }
    
    protected $casts = [
        'obtained' => 'boolean',
        'obtained_at' => 'datetime',
    ];
}
