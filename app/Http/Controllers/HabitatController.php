<?php

namespace App\Http\Controllers;

use App\Models\Habitat;
use Illuminate\Http\Request;

class HabitatController extends Controller
{
    public function index()
    {
        return Habitat::select('id', 'name', 'icon_url')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:habitats,name'],
            'icon_url' => ['nullable', 'url'],
        ]);

        Habitat::create($request->only(['name', 'icon_url']));

        return back()->with('success', 'Habitat added!');
    }

    public function destroy(Habitat $habitat)
    {
        $habitat->delete();

        return back()->with('success', 'Habitat deleted.');
    }
}
