<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Crown extends Model
{
    /** @use HasFactory<\Database\Factories\CrownFactory> */
    use HasFactory;

    protected $fillable = [
        'monster_id',
        'crown_type',
    ];

    public function monster(): BelongsTo
    {
        return $this->belongsTo(Monster::class);
    }
    

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_crowns')
            ->withPivot(['obtained', 'obtained_at'])
            ->withTimestamps();
    }
}
