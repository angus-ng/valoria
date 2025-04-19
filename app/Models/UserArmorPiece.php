<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserArmorPiece extends Model
{
    protected $fillable = [
        'user_id',
        'armor_piece_id',
        'obtained',
        'obtained_at',
    ];
    
    protected $casts = [
        'obtained' => 'boolean',
        'obtained_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function armorPiece()
    {
        return $this->belongsTo(ArmorPiece::class);
    }
    
}
