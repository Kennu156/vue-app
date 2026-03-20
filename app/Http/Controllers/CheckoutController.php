<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart  = session('cart', []);
        $items = [];
        $total = 0;

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

        if (empty($items)) {
            return redirect()->route('shop');
        }

        return Inertia::render('Shop/Checkout', [
            'items'      => $items,
            'total'      => round($total, 2),
            'stripeKey'  => config('services.stripe.key'),
        ]);
    }

    public function createIntent(Request $request)
    {
        $cart  = session('cart', []);
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * $quantity;
            }
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'   => (int) round($total * 100), 
            'currency' => 'eur',
        ]);

        return response()->json(['clientSecret' => $intent->client_secret]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'            => 'required|string|max:100',
            'last_name'             => 'required|string|max:100',
            'email'                 => 'required|email',
            'phone'                 => 'required|string|max:20',
            'payment_intent_id'     => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::retrieve($request->payment_intent_id);

        $cart  = session('cart', []);
        $total = 0;
        $items = [];

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * $quantity;
                $items[] = ['product' => $product, 'quantity' => $quantity];
            }
        }

        $status = $intent->status === 'succeeded' ? 'paid' : 'failed';

        $order = Order::create([
            'user_id'                => auth()->id(),
            'first_name'             => $request->first_name,
            'last_name'              => $request->last_name,
            'email'                  => $request->email,
            'phone'                  => $request->phone,
            'total'                  => round($total, 2),
            'status'                 => $status,
            'stripe_payment_intent'  => $intent->id,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product']->id,
                'quantity'   => $item['quantity'],
                'price'      => $item['product']->price,
            ]);
        }

        if ($status === 'paid') {
            session()->forget('cart');
            return redirect()->route('checkout.success', $order->id);
        }

        return redirect()->route('checkout.failed', $order->id);
    }

    public function success(Order $order)
    {
        return Inertia::render('Shop/OrderSuccess', ['order' => $order->load('items.product')]);
    }

    public function failed(Order $order)
    {
        return Inertia::render('Shop/OrderFailed', ['order' => $order]);
    }
}