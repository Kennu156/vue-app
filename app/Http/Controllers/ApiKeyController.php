<?php
namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApiKeyController extends Controller
{
    public function index()
    {
        return Inertia::render('ApiKeys/Index', [
            'apiKeys' => ApiKey::where('user_id', auth()->id())
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        ApiKey::create([
            'user_id' => auth()->id(),
            'name'    => $request->name,
            'key'     => ApiKey::generate(),
        ]);

        return back();
    }

    public function destroy(ApiKey $apiKey)
    {
        abort_if($apiKey->user_id !== auth()->id(), 403);
        $apiKey->delete();
        return back();
    }
}