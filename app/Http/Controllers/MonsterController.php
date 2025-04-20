<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crown;
use App\Models\Monster;
use Inertia\Inertia;

class MonsterController extends Controller
{
    public function index(Request $request)
    {
        $habitats = (array) $request->input('habitats', []);
    
        $monsters = Monster::with(['habitats', 'crowns'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when(count($habitats), function ($query) use ($habitats) {
                $query->whereHas('habitats', function ($q) use ($habitats) {
                    $q->whereIn('habitats.id', $habitats);
                }, '=', count($habitats));
            })
            ->when($request->boolean('crownable'), function ($query) {
                $query->whereHas('crowns');
            })
            ->get();
    
        return Inertia::render('monsters/Index', [
            'monsters' => $monsters,
            'filters' => [
                'search' => $request->input('search', ''),
                'habitats' => $habitats,
                'crownable' => $request->boolean('crownable'),
            ],
        ]);
    }
    

    public function all()
    {
        $monsters = Monster::with(['habitats', 'crowns'])->get();

        return response()->json($monsters);
    }

    public function show(Monster $monster)
    {
        return Inertia::render('monsters/Monster', [
            'monster' => $monster->load(['habitats', 'crowns']),
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:monsters,name',
            'icon_url' => 'nullable|url',
            'habitats' => 'array',
            'habitats.*' => 'exists:habitats,id',
            'crownable' => 'boolean',
        ]);
    
        $monster = Monster::create([
            'name' => $validated['name'],
            'icon_url' => $validated['icon_url'] ?? null,
        ]);
    
        if (!empty($validated['habitats'])) {
            $monster->habitats()->attach($validated['habitats']);
        }
    
        if (!empty($validated['crownable'])) {
            Crown::create([
                'monster_id' => $monster->id,
                'crown_type' => 'small',
            ]);
    
            Crown::create([
                'monster_id' => $monster->id,
                'crown_type' => 'large',
            ]);
        }
    
        return back()->with('success', 'Monster Created');
    }

    public function destroy($id)
    {
        $monster = Monster::findOrFail($id);
        $monster->delete();

        return back()->with('success', 'Monster deleted.');
    }
}
