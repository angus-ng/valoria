<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArmorSet extends Model
{
    /** @use HasFactory<\Database\Factories\ArmorSetFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'monster_id',
        'source_type',
        'rarity',
        'event_only',
    ];

    public function monster()
    {
        return $this->belongsTo(Monster::class);
    }

    public function pieces()
    {
        return $this->hasMany(ArmorPiece::class);
    }

    public function getSourceLabelAttribute()
    {
        return $this->monster ? $this->monster->name : $this->source_type;
    }

    protected $casts = [
        'event_only' => 'boolean',
    ];
}
