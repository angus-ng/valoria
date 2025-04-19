<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crown extends Model
{
    /** @use HasFactory<\Database\Factories\CrownFactory> */
    use HasFactory;

    protected $fillable = [
        'monster_id',
        'crown_type',
    ];

    public function monster()
    {
        return $this->belongsTo(Monster::class);
    }
    
    public function userCrowns()
    {
        return $this->hasMany(UserCrown::class);
    }
}
