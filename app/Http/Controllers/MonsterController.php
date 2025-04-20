<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crown;
use App\Models\Monster;

class MonsterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:monsters,name',
            'icon_url' => 'nullable|url',
            'habitat_ids' => 'array|exists:habitats,id',
            'has_crowns' => 'boolean',
        ]);
    
        $monster = Monster::create([
            'name' => $validated['name'],
            'icon_url' => $validated['icon_url'] ?? null,
        ]);
    
        if (!empty($validated['habitat_ids'])) {
            $monster->habitats()->attach($validated['habitat_ids']);
        }
    
        if (!empty($validated['has_crowns'])) {
            Crown::create([
                'monster_id' => $monster->id,
                'crown_type' => 'small',
            ]);
    
            Crown::create([
                'monster_id' => $monster->id,
                'crown_type' => 'large',
            ]);
        }
    
        return response()->json($monster->load('crowns', 'habitats'), 201);
    }
}
