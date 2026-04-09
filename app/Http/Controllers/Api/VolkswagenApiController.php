<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Volkswagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VolkswagenApiController extends Controller
{
    /**
     * GET /api/volkswagens
     *
     * Parameetrid:
     *   search  — otsi pealkirja järgi
     *   model   — filtreeri mudeli järgi (nt. Golf, Passat)
     *   year    — filtreeri aasta järgi
     *   sort    — title|year|price|mileage|created_at (vaikimisi: created_at)
     *   order   — asc|desc (vaikimisi: desc)
     *   limit   — 1-100 (vaikimisi: 20)
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $model  = $request->query('model');
        $year   = $request->query('year');
        $sort   = in_array($request->query('sort'), ['title','year','price','mileage','created_at'])
                    ? $request->query('sort') : 'created_at';
        $order  = $request->query('order', 'desc') === 'asc' ? 'asc' : 'desc';
        $limit  = min((int) $request->query('limit', 20), 100);

        $cacheKey = "api-vw-{$search}-{$model}-{$year}-{$sort}-{$order}-{$limit}";

        $cars = Cache::remember($cacheKey, 300, function () use ($search, $model, $year, $sort, $order, $limit) {
            return Volkswagen::with('user:id,name')
                ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
                ->when($model,  fn ($q) => $q->where('model', $model))
                ->when($year,   fn ($q) => $q->where('year', $year))
                ->orderBy($sort, $order)
                ->limit($limit)
                ->get()
                ->map(fn ($c) => [
                    'id'          => $c->id,
                    'title'       => $c->title,
                    'description' => $c->description,
                    'image'       => $c->image ? (str_starts_with($c->image, 'http') ? $c->image : asset('storage/' . $c->image)) : null,
                    'year'        => $c->year,
                    'model'       => $c->model,
                    'engine'      => $c->engine,
                    'mileage'     => $c->mileage,
                    'price'       => $c->price,
                    'color'       => $c->color,
                    'added_by'    => $c->user->name ?? 'Tundmatu',
                    'created_at'  => $c->created_at,
                ]);
        });

        return response()->json([
            'data'    => $cars,
            'count'   => $cars->count(),
            'filters' => compact('search', 'model', 'year', 'sort', 'order', 'limit'),
        ]);
    }

    public function show(Volkswagen $volkswagen)
    {
        return response()->json([
            'data' => [
                'id'          => $volkswagen->id,
                'title'       => $volkswagen->title,
                'description' => $volkswagen->description,
                'image'       => $volkswagen->image ? (str_starts_with($volkswagen->image, 'http') ? $volkswagen->image : asset('storage/' . $volkswagen->image)) : null,
                'year'        => $volkswagen->year,
                'model'       => $volkswagen->model,
                'engine'      => $volkswagen->engine,
                'mileage'     => $volkswagen->mileage,
                'price'       => $volkswagen->price,
                'color'       => $volkswagen->color,
                'added_by'    => $volkswagen->user->name ?? 'Tundmatu',
                'created_at'  => $volkswagen->created_at,
            ],
        ]);
    }

    public function models()
    {
        $models = Cache::remember('api-vw-models', 300, fn () =>
            Volkswagen::distinct()->orderBy('model')->pluck('model')
        );

        return response()->json(['data' => $models]);
    }
}