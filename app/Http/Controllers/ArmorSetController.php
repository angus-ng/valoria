<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ArmorSet;
use App\Models\ArmorPiece;

class ArmorSetController extends Controller
{
    public function index()
    {
        $sets = ArmorSet::with(['pieces', 'monster:id,name,icon_url'])->get();
    
        return response()->json($sets);
    }

    public function destroy($id)
    {
        $set = ArmorSet::findOrFail($id);
        $set->delete();

        return back()->with('success', 'Armor Set deleted.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rarity' => ['required', 'integer', 'min:1', 'max:8'],
            'event_only' => ['required'],
            'source_type' => ['required', 'string'],
            'monster_id' => ['nullable', 'exists:monsters,id'],
            'pieces' => ['required', 'array'],
        ]);
    
        $selectedSlots = collect($validated['pieces'])
            ->filter(fn($checked) => $checked)
            ->keys()
            ->all();
    
        if (count($selectedSlots) === 0) {
            return redirect()->back()->withErrors(['pieces' => 'At least one armor piece must be selected.']);
        }
    
        $armorSetId = DB::table('armor_sets')->insertGetId([
            'name' => $validated['name'],
            'rarity' => $validated['rarity'],
            'event_only' => DB::raw($validated['event_only'] ? 'true' : 'false'),
            'source_type' => $validated['source_type'],
            'monster_id' => $validated['monster_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        foreach ($selectedSlots as $slot) {
            ArmorPiece::create([
                'armor_set_id' => $armorSetId,
                'slot' => $slot,
            ]);
        }
    
        return redirect()->back()->with('success', 'Armor set added successfully!');
    }
    
}
