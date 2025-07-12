<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\DiscountRule;
use Session;
use Carbon\Carbon;

class CartController extends Controller
{
    // public function add(Request $request){   
    //     $cart = session()->get('cart', []);
    //     dd($request->all());
    //     $id = $request->product_id;
    //     $qty = $request->quantity ?? 1;

    //     $product = Product::findOrFail($id);
    //     $availableStock = $request->stock;

    //     // Use hidden input price if sent, fallback to actual_price
    //     $price = $request->input('price', $product->actual_price);

    //     $currentCartQuantity = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
    //     $newDesiredQuantity = $currentCartQuantity + $qty;

    //     if ($newDesiredQuantity > $availableStock) {
    //         return redirect()->back()->with('error', "Cannot add {$qty} units. Only {$availableStock} of {$product->name} in stock. You already have {$currentCartQuantity} in your cart. You can add " . ($availableStock - $currentCartQuantity) . " more.");
    //     }

    //     $cart[$id] = [
    //         'id' => $id,
    //         'name' => $product->name,
    //         'stock' => $availableStock,
    //         'price' => $price, // ✅ Discounted or final price
    //         'actual_price' => $product->actual_price, // ✅ Store original price
    //         'quantity' => $newDesiredQuantity,
    //         'image' => $product->product_img,
    //     ];

    //     session()->put('cart', $cart);

    //     return redirect()->back()->with('success', 'Product added to cart!');
    // }

    public function add(Request $request)
    {
        $cart = session()->get('cart', []);
        $id = $request->product_id;
        $qty = $request->quantity ?? 1;

        $product = Product::findOrFail($id);
        $availableStock = $request->stock;

        // Use hidden input price if sent, fallback to actual_price
        $price = $request->input('price', $product->actual_price);

        // --- New: Get Variant Information ---
        $variantId = $request->variant_id;
        $variantName = null; // Initialize variant name
        $variantImg = null;

        if ($variantId) {

            $variant = $product->variants()->find($variantId);
            if ($variant) {
                $variantName = $variant->variant_name;
                $variantImg = $variant->product_img;
            }
        }

        $currentCartQuantity = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newDesiredQuantity = $currentCartQuantity + $qty;

        if ($newDesiredQuantity > $availableStock) {
            return redirect()->back()->with('error', "Cannot add {$qty} units. Only {$availableStock} of {$product->name} in stock. You already have {$currentCartQuantity} in your cart. You can add " . ($availableStock - $currentCartQuantity) . " more.");
        }

        $cartKey = $id; // Default to product ID
        if ($variantId) {
            $cartKey = "{$id}-{$variantId}";
            $currentCartQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
            $newDesiredQuantity = $currentCartQuantity + $qty;

            // Re-check stock for the specific variant
            if ($newDesiredQuantity > $availableStock) {
                return redirect()->back()->with('error', "Cannot add {$qty} units. Only {$availableStock} of {$product->name} " . ($variantName ? "({$variantName})" : "") . " in stock. You already have {$currentCartQuantity} in your cart. You can add " . ($availableStock - $currentCartQuantity) . " more.");
            }
        }


        $cart[$cartKey] = [ // Use the potentially new $cartKey
            'id' => $id,
            'name' => $product->name,
            'stock' => $availableStock,
            'price' => $price, // ✅ Discounted or final price
            'actual_price' => $product->actual_price, // ✅ Store original price
            'quantity' => $newDesiredQuantity,
            'image' => $product->product_img, // ✅ Product's main image
            // --- New: Add variant information ---
            'variant_id' => $variantId,
            'variant_name' => $variantName,
            'variant_img' => $variantImg,
           
        ];

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function view(){
        $cart = session()->get('cart', []);
        $setting = Setting::first();

        // Calculate subtotal from cart items
        $subtotal = 0;
        foreach ($cart as $item) {
            // Ensure you're using the correct price and quantity keys
            $subtotal += ($item['price'] * $item['quantity']);
        }

        // Retrieve current coupon info from session for initial page load
        $couponDiscount = session('coupon_discount', 0);
        $appliedCouponCode = session('coupon_code', null);

        // Pass all necessary data to the view
        return view('store.cart', compact('cart', 'setting', 'subtotal', 'couponDiscount', 'appliedCouponCode'));
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

    public function applyCoupon(Request $request){
        $couponCode = $request->input('coupon_code');
        $cartSubtotal = $request->input('subtotal'); // Get subtotal from frontend for current calculation

        if (!$couponCode) {
            return response()->json(['success' => false, 'message' => 'Coupon code is required.']);
        }

        $couponRule = DiscountRule::where('coupon_code', $couponCode)
                                  ->where('type', 'coupon')
                                  ->where('start_date', '<=', Carbon::now())
                                  ->where('end_date', '>=', Carbon::now())
                                  ->first();

        if (!$couponRule) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon code.']);
        }

        // Check if a coupon is already applied and if it's the same one
        if (session('coupon_code') === $couponCode) {
            return response()->json(['success' => false, 'message' => 'Coupon already applied.']);
        }

        // Calculate the discount amount
        $discountPercentage = $couponRule->discount;
        $discountAmount = ($cartSubtotal * $discountPercentage) / 100;

        // Store coupon details in session
        session(['coupon_code' => $couponCode]);
        session(['coupon_discount' => $discountAmount]);
        session(['coupon_percentage' => $discountPercentage]); // Store percentage too if useful

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount_amount' => $discountAmount,
            'coupon_code' => $couponCode
        ]);
    }

    public function removeCoupon(Request $request){
        // Remove coupon details from session
        session()->forget('coupon_code');
        session()->forget('coupon_discount');
        session()->forget('coupon_percentage');

        return response()->json(['success' => true, 'message' => 'Coupon removed successfully.']);
    }

}