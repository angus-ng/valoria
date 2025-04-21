<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ArmorSet;
use App\Models\ArmorPiece;
use App\Models\User;

class ArmorSetController extends Controller
{
    public function all()
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
        $request->merge([
            'source_type' => ucfirst(strtolower($request->input('source_type'))),
        ]);        
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

    public function index()
    {
        $userId = Auth::id();
    
        $armorSets = \App\Models\ArmorSet::with([
            'monster:id,name,icon_url',
            'pieces' => function ($query) use ($userId) {
                $query->with(['users' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }]);
            },
        ])->get();
    
        return inertia('armors/Index', [
            'armorSets' => $armorSets,
        ]);
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'armor_piece_id' => 'required|exists:armor_pieces,id',
        ]);

        $user = User::findOrFail(Auth::id());
        $piece = ArmorPiece::findOrFail($validated['armor_piece_id']);

        $existing = $user->armorPieces()->wherePivot('armor_piece_id', $piece->id)->first();

        if ($existing) {
            $currentObtained = $existing->pivot->obtained;

            $user->armorPieces()->updateExistingPivot($piece->id, [
                'obtained' => DB::raw($currentObtained ? 'false' : 'true'),
                'obtained_at' => now(),
            ]);
        } else {
            $user->armorPieces()->attach($piece->id, [
                'obtained' => DB::raw('true'),
                'obtained_at' => now(),
            ]);
        }

        return redirect()->route('armors.index');
    }
    
}
