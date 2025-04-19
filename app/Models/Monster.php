<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Monster extends Model
{
    /** @use HasFactory<\Database\Factories\MonsterFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_url',
        'slug'
    ];

    public function habitats()
    {
        return $this->belongsToMany(Habitat::class);
    }
    
    public function crowns()
    {
        return $this->hasMany(Crown::class);
    }

    public function smallCrown()
    {
        return $this->hasOne(Crown::class)->where('crown_type', 'small');
    }

    public function largeCrown()
    {
        return $this->hasOne(Crown::class)->where('crown_type', 'large');
    }

    protected static function booted(): void
{
    static::creating(function ($monster) {
        $monster->slug = Str::slug($monster->name);
    });
}
}
