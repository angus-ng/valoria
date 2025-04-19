<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArmorPiece extends Model
{
    /** @use HasFactory<\Database\Factories\ArmorPieceFactory> */
    use HasFactory;

    protected $fillable = [
        'armor_set_id',
        'slot',
    ];
    
    public function set()
    {
        return $this->belongsTo(ArmorSet::class, 'armor_set_id');
    }
}
