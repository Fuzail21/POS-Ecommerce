<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function add(Request $request) {
        $cart = session()->get('cart', []);
        $id = $request->product_id;
        $qty = $request->quantity ?? 1;

        $product = Product::findOrFail($id);
        $cart[$id] = [
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => isset($cart[$id]) ? $cart[$id]['quantity'] + $qty : $qty,
            'image' => $product->image,
        ];

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function view() {
        $cart = session()->get('cart', []);
        return view('store.cart', compact('cart'));
    }

    public function remove(Request $request) {
        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Item removed!');
    }
}
