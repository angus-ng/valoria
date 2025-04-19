<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitat extends Model
{
    /** @use HasFactory<\Database\Factories\HabitatFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_url',
    ];

    public function monsters()
    {
        return $this->belongsToMany(Monster::class);
    }
}
