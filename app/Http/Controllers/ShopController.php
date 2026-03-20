<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function index()
    {
        return Inertia::render('Shop/Index', [
            'products' => Product::all(),
        ]);
    }
}