<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CrownController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
    
        $crowns = Crown::with(['monster:id,name,icon_url', 'monster.habitats:id,name,icon_url'])
        ->get()
        ->map(function ($crown) use ($userId) {
            $pivot = $crown->users()->where('user_id', $userId)->first();
            $crown->user_obtained = $pivot?->pivot->obtained ?? false;
            return $crown;
        });
    
        return inertia('crowns/Index', [
            'crowns' => $crowns,
        ]);
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'monster_id' => 'required|exists:monsters,id',
            'crown_type' => 'required|in:small,large',
        ]);
    
        $crown = Crown::where('monster_id', $validated['monster_id'])
            ->where('crown_type', $validated['crown_type'])
            ->firstOrFail();
    
        $user = User::find(Auth::id());

        $existing = $user->crowns()->wherePivot('crown_id', $crown->id)->first();
    
        if ($existing) {
            $currentObtained = $existing->pivot->obtained;
    
            $user->crowns()->updateExistingPivot($crown->id, [
                'obtained' => DB::raw(!$currentObtained ? 'true' : 'false'),
                'obtained_at' => now(),
            ]);
        } else {
            $user->crowns()->attach($crown->id, [
                'obtained' => DB::raw('true'),
                'obtained_at' => now(),
            ]);
        }
    
        return redirect()->route('crowns.index');
    }
    
}
