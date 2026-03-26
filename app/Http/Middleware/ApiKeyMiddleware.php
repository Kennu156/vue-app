<?php
namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$key) {
            return response()->json([
                'error' => 'API võti puudub. Lisa päisesse X-API-Key või ?api_key=...',
            ], 401);
        }

        $apiKey = ApiKey::where('key', $key)->first();

        if (!$apiKey) {
            return response()->json([
                'error' => 'Vigane API võti.',
            ], 403);
        }

        $apiKey->update(['last_used_at' => now()]);

        return $next($request);
    }
}