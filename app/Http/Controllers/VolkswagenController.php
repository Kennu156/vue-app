<?php
namespace App\Http\Controllers;

use App\Models\Volkswagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VolkswagenController extends Controller
{
    public function index()
    {
        $cars = Cache::remember('volkswagens-all', 300, fn () =>
            Volkswagen::with('user')->latest()->get()
        );

        return Inertia::render('Volkswagens/Index', [
            'cars' => $cars,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'year'        => 'required|integer|min:1938|max:2099',
            'model'       => 'required|string|max:100',
            'engine'      => 'required|string|max:100',
            'mileage'     => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'color'       => 'nullable|string|max:50',
            'image'       => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('volkswagens', 'public');
        }

        $data['user_id'] = auth()->id();
        Volkswagen::create($data);
        Cache::forget('volkswagens-all');

        return back()->with('success', 'Auto lisatud!');
    }

    public function update(Request $request, Volkswagen $volkswagen)
    {

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'year'        => 'required|integer|min:1938|max:2099',
            'model'       => 'required|string|max:100',
            'engine'      => 'required|string|max:100',
            'mileage'     => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'color'       => 'nullable|string|max:50',
            'image'       => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            if ($volkswagen->image) Storage::disk('public')->delete($volkswagen->image);
            $data['image'] = $request->file('image')->store('volkswagens', 'public');
        }

        $volkswagen->update($data);
        Cache::forget('volkswagens-all');

        return back();
    }

    public function destroy(Volkswagen $volkswagen)
    {
        if ($volkswagen->image) Storage::disk('public')->delete($volkswagen->image);
        $volkswagen->delete();
        Cache::forget('volkswagens-all');

        return back();
    }
}