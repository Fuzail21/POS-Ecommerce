<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Setting;
use Session;

class CartController extends Controller
{
    public function add(Request $request){   
        $cart = session()->get('cart', []);
        $id = $request->product_id;
        $qty = $request->quantity ?? 1;

        $product = Product::findOrFail($id);
        $availableStock = $request->stock;

        // Use hidden input price if sent, fallback to actual_price
        $price = $request->input('price', $product->actual_price);

        $currentCartQuantity = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newDesiredQuantity = $currentCartQuantity + $qty;

        if ($newDesiredQuantity > $availableStock) {
            return redirect()->back()->with('error', "Cannot add {$qty} units. Only {$availableStock} of {$product->name} in stock. You already have {$currentCartQuantity} in your cart. You can add " . ($availableStock - $currentCartQuantity) . " more.");
        }

        $cart[$id] = [
            'id' => $id,
            'name' => $product->name,
            'stock' => $availableStock,
            'price' => $price, // ✅ Discounted or final price
            'actual_price' => $product->actual_price, // ✅ Store original price
            'quantity' => $newDesiredQuantity,
            'image' => $product->product_img,
        ];

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function view(){
        $cart = session()->get('cart', []);
        $setting = Setting::first();
        return view('store.cart', compact('cart', 'setting'));
    }

    public function update(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $productId = $request->input('product_id');
        $newQuantity = (int) $request->input('quantity');

        $cart = Session::get('cart', []);

        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Use the total_stock accessor from the Product model
        $availableStock = $product->inventoryStock->quantity_in_base_unit;

        $message = '';

        if ($newQuantity > 0) {
            if ($newQuantity > $availableStock) {
                // If the requested quantity exceeds available stock, adjust it to the maximum available
                $adjustedQuantity = $availableStock;
                // If current quantity in cart is more than available, remove it
                if (isset($cart[$productId]) && $cart[$productId]['quantity'] > $availableStock) {
                    $adjustedQuantity = $availableStock;
                }
                if ($adjustedQuantity < 0) $adjustedQuantity = 0; // Ensure not negative

                return response()->json([
                    'success' => false,
                    'message' => "Only {$availableStock} of {$product->name} in stock. Quantity adjusted.",
                    'new_quantity' => $adjustedQuantity, // Send back the adjusted quantity
                    'item_total' => number_format(($product->actual_price ?? 0) * $adjustedQuantity, 2),
                    // Pass the whole cart and adjusted quantity for this product to calculateCartSubtotal
                    'cart_subtotal' => number_format($this->calculateCartSubtotal($cart, $productId, $adjustedQuantity), 2),
                    'cart_grand_total' => number_format($this->calculateCartSubtotal($cart, $productId, $adjustedQuantity), 2),
                ], 400); // Use 400 Bad Request for client-side adjustment
            }

            $cart[$productId] = [
                'id' => $productId,
                'name' => $product->name,
                'stock' => $availableStock, // Use the actual product total stock
                'price' => $product->actual_price,
                'quantity' => $newQuantity,
                'image' => $product->product_img,
            ];
            $message = 'Cart updated successfully.';
        } else { // newQuantity is 0, means removing the item
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
                $message = 'Product removed from cart.';
            } else {
                $message = 'Product not in cart.';
            }
        }

        Session::put('cart', $cart);

        // Recalculate totals after cart modification, using the potentially modified $cart
        $cartSubtotal = $this->calculateCartSubtotal($cart);
        $cartGrandTotal = $cartSubtotal;

        return response()->json([
            'success' => true,
            'message' => $message,
            'new_quantity' => $newQuantity,
            'item_total' => number_format(($product->actual_price ?? 0) * $newQuantity, 2),
            'cart_subtotal' => number_format($cartSubtotal, 2),
            'cart_grand_total' => number_format($cartGrandTotal, 2),
        ]);
    }

    public function remove(Request $request) {
        $cart = session()->get('cart', []);
        if(isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Item removed!');
        }
        return redirect()->back()->with('error', 'Item not found in cart!');
    }

    private function calculateCartSubtotal(array $cart, $productId = null, $quantity = null){
        $subtotal = 0;
        $tempCart = $cart;

        if ($productId !== null && $quantity !== null && isset($tempCart[$productId])) {
            $tempCart[$productId]['quantity'] = $quantity;
        }

        foreach ($tempCart as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        }
        return $subtotal;
    }
}