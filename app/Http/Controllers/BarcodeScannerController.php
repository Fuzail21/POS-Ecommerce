<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Assuming your Product model
use App\Models\ProductVariant; // Assuming your ProductVariant model
use Illuminate\Support\Facades\Session; // For cart management

class BarcodeScannerController extends Controller
{
    /**
     * Display the POS scanning interface.
     */
    public function showScanner()
    {
        // Fetch the current cart to display it
        $cart = Session::get('cart', []); // Use 'cart' key to match CheckoutController
        return view('pos.scan', compact('cart'));
    }

    /**
     * Handle barcode scan request, find product or variant, and add to cart.
     */
    public function scanProduct(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $barcode = $request->input('barcode');

        $product = null;
        $variant = null;
        $itemDetails = []; // To store details for the cart item

        // --- Attempt to find ProductVariant by barcode first ---
        $variant = ProductVariant::where('barcode', $barcode)->first();

        if ($variant) {
            $product = $variant->product; // Get the parent product of the variant

            if (!$product) {
                // This scenario should ideally not happen if relationships are correct
                return response()->json([
                    'success' => false,
                    'message' => 'Variant found, but associated product not found.'
                ], 404);
            }

            // Use variant's details
            $itemDetails = [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'name' => $product->name . ' - ' . $variant->name, // Combine product and variant name
                'price' => $variant->price, // Use variant's price
                'stock' => $variant->stock, // Use variant's stock
                'barcode' => $barcode,
                'quantity' => 1,
                'variant_name' => $variant->name,
                'variant_img' => $variant->image ?? null, // Use variant image if available, else null
                'image' => $product->product_img, // Keep product's main image as fallback
            ];
            $cartKey = "{$product->id}-{$variant->id}"; // Unique key for variant in cart

        } else {
            // --- If no variant found, attempt to find Product by barcode ---
            $product = Product::where('barcode', $barcode)->first();

            if ($product) {
                // Use product's details
                $itemDetails = [
                    'product_id' => $product->id,
                    'variant_id' => null, // No variant
                    'name' => $product->name,
                    'price' => $product->price, // Use product's price
                    'stock' => $product->stock, // Use product's stock
                    'barcode' => $barcode,
                    'quantity' => 1,
                    'variant_name' => null,
                    'variant_img' => null,
                    'image' => $product->product_img, // Product's main image
                ];
                $cartKey = $product->id; // Key for main product in cart

            } else {
                // --- Neither product nor variant found ---
                return response()->json([
                    'success' => false,
                    'message' => 'Product or Variant not found for barcode: ' . $barcode
                ], 404);
            }
        }

        // --- Stock Check (applies to both product and variant) ---
        $availableStock = $itemDetails['stock'];
        $cart = Session::get('cart', []);
        $currentCartQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $newDesiredQuantity = $currentCartQuantity + 1; // Always adding 1 per scan

        if ($newDesiredQuantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add more. Only ' . $availableStock . ' of ' . $itemDetails['name'] . ' in stock. You already have ' . $currentCartQuantity . ' in your cart.'
            ], 400);
        }

        // --- Add/Update item in cart session ---
        $itemDetails['quantity'] = $newDesiredQuantity; // Set the updated quantity
        $cart[$cartKey] = $itemDetails; // Store the full item details

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Added "' . $itemDetails['name'] . '" to cart.',
            'product' => $itemDetails, // Return the added item details
            'cart' => $cart // Return the updated full cart
        ]);
    }

    /**
     * Clears the POS session cart.
     */
    public function clearCart(Request $request)
    {
        Session::forget('cart'); // Use 'cart' key
        return response()->json([
            'success' => true,
            'message' => 'POS cart cleared successfully.'
        ]);
    }
}