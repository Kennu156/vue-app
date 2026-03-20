<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarkerController extends Controller
{
    public function index()
    {
        return Inertia::render('Map', [
            'markers' => Marker::orderByDesc('added')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
        ]);

        Marker::create($request->only('name', 'latitude', 'longitude', 'description'));
        return back();
    }

    public function update(Request $request, Marker $marker)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
        ]);

        $marker->update($request->only('name', 'latitude', 'longitude', 'description'));
        return back();
    }

    public function destroy(Marker $marker)
    {
        $marker->delete();
        return back();
    }
}