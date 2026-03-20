<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cart    = session('cart', []);
        $items   = [];
        $total   = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $items[] = [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return Inertia::render('Shop/Cart', [
            'items' => $items,
            'total' => round($total, 2),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $cart = session('cart', []);
        $id   = $request->product_id;

        $cart[$id] = ($cart[$id] ?? 0) + $request->quantity;
        session(['cart' => $cart]);

        return back()->with('success', 'Toode lisatud ostukorvi!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:0|max:99',
        ]);

        $cart = session('cart', []);
        $id   = $request->product_id;

        if ($request->quantity === 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $request->quantity;
        }

        session(['cart' => $cart]);
        return back();
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required']);
        $cart = session('cart', []);
        unset($cart[$request->product_id]);
        session(['cart' => $cart]);
        return back();
    }

    public function clear()
    {
        session()->forget('cart');
        return back();
    }
}