<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceSentMail;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\InventoryStock;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\StockLedger;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; // Import Auth facade


class CheckoutController extends Controller
{
    public function index(){
        // Get the authenticated user's ID
        $userId = Auth::id();

        // If the user is not authenticated, redirect them or show an error
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to proceed to checkout.');
        }

        // Find the customer using the authenticated user's ID
        $user = Customer::find($userId);

        // If the customer record is not found for the authenticated user, handle it
        if (!$user) {
            return redirect()->back()->with('error', 'Customer profile not found.');
        }

        // Get the cart data from the session
        $cart = session()->get('cart', []);

        // Calculate the initial subtotal from the cart items
        $subtotal = 0;
        foreach ($cart as $item) {
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'] ?? 0;
            $subtotal += ($price * $quantity);
        }

        // Retrieve coupon data from session
        $couponCode = session('coupon_code', null);
        $couponDiscount = session('coupon_discount', 0);

        // Calculate the total after applying discount (if any)
        $subtotalAfterCoupon = $subtotal - $couponDiscount;
        if ($subtotalAfterCoupon < 0) {
            $subtotalAfterCoupon = 0; // Ensure subtotal doesn't go negative
        }

        // Fetch the general settings for currency symbol
        $setting = Setting::first();

        // Return the view, passing all necessary variables
        return view('store.checkout', compact('cart', 'user', 'subtotal', 'subtotalAfterCoupon', 'setting', 'couponCode', 'couponDiscount'));
    }

    public function process(Request $request){
         // Get the current year for invoice number generation`
        $year = date('Y');
        // Generate the next invoice number
        $lastSale = Sale::whereYear('created_at', $year)
            ->where('invoice_number', 'like', "{$year}-invoice-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastSale && preg_match("/{$year}-invoice-(\d+)/", $lastSale->invoice_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $invoiceNo = "{$year}-invoice-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Determine the branch and warehouse
        $branch = Branch::where('name', '=', 'Ecommerce-store')->first();
        if (!$branch) {
            return back()->withInput()->with('error', 'The "Ecommerce-store" branch was not found. Please ensure it exists.');
        }
        $branchId = $branch->id;
        
        $warehouse_id = $branch->warehouse->id ?? null;
        // Ensure a warehouse ID was successfully retrieved for stock operations.
        if (!$warehouse_id) {
            return back()->withInput()->with('error', 'The selected branch does not have an associated warehouse. Please configure the branch.');
        }

        DB::beginTransaction();

        try {
            $cart = json_decode($request->cart_data, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart) || empty($cart)) {
                throw new \Exception('Invalid or empty cart data provided.');
            }

            $calculatedTotalAmount = 0;
            foreach ($cart as $item) {
                // Safely access 'price' and 'quantity' here too
                $finalPrice = $item['price'] ?? 0;
                $quantity = $item['quantity'] ?? 0;
                $calculatedTotalAmount += ($finalPrice * $quantity);
            }

            $customerId = Auth::id();
            if (!$customerId) {
                throw new \Exception('User not authenticated. Cannot process sale.');
            }

            // Retrieve values from the request, which come from hidden inputs in checkout.blade.php
            $discountAmount = $request->coupon_discount_amount ?? 0;
            $taxAmount = $request->tax_amount ?? 0;
            $shippingCost = $request->shipping ?? 0;
            $finalAmount = $request->total_payable; // This should be the grand total after all calculations
            $paidAmount = $request->amount_paid;   // This should also be the final amount for full payment
            $paymentMethod = 'cash';

            $sale = Sale::create([
                'customer_id'       => $customerId,
                'branch_id'         => $branchId,
                'invoice_number'    => $invoiceNo,
                'sale_date'         => now(),
                'total_amount'      => $finalAmount, // This is the sum of item prices before coupon discount
                'discount_amount'   => $discountAmount,     // Use the discount amount from the request
                'tax_amount'        => $taxAmount,         // Use tax amount from the request
                'shipping'          => $shippingCost,       // Use shipping cost from the request
                'final_amount'      => $finalAmount,         // Use the final_amount passed from the frontend
                'paid_amount'       => 0,          // Use the paid_amount passed from the frontend
                'due_amount'        => $paidAmount, // Calculate due amount
                'payment_method'    => $paymentMethod,
                'sale_origin'       => 'E-commerce',
                'status'            => 'pending',
                'created_by'        => auth()->id(),
            ]);

            foreach ($cart as $item) {
                // Safely access 'type' key. If it's not present, default to empty string.
                $itemType = $item['type'] ?? '';
                // Safely access 'id' key.
                $itemId = $item['id'] ?? null;


                $variantId = $item['variant_id'] ?? null;
                $productId = $variantId ? ProductVariant::find($variantId)?->product_id : $itemId;

                // Safely access 'name' for error messages
                $itemName = $item['name'] ?? 'product';

                if (!$productId || !Product::find($productId)) {
                    \Log::warning("Skipping invalid product/variant during sale processing. Item: " . json_encode($item));
                    continue;
                }

                // Safely access other item properties
                $unitId     = $item['unit_id'] ?? null;
                $quantity   = $item['quantity'] ?? 0;
                $unitPrice  = $item['price'] ?? 0;
                $actualPrice = $item['actual_price'] ?? $unitPrice;
                $totalPrice = $unitPrice * $quantity;

                $unit = $unitId ? Unit::find($unitId) : null;
                $baseQty = $quantity * ($unit->conversion_factor ?? 1);

                SaleItem::create([
                    'sale_id'               => $sale->id,
                    'product_id'            => $productId,
                    'variant_id'            => $variantId,
                    'unit_id'               => $unitId,
                    'quantity'              => $quantity,
                    'unit_price'            => $unitPrice,
                    'total_price'           => $totalPrice,
                    'quantity_in_base_unit' => $baseQty,
                    'discount'              => null,
                    'tax'                   => null,
                ]);
                $stock = InventoryStock::where([
                    'product_id'    => $productId,
                    'variant_id'    => $variantId ?? NULL,
                    // 'warehouse_id'  => $warehouse_id,
                    
                ])->first();
                if (!$stock || $stock->quantity_in_base_unit < $baseQty) {
                    throw new \Exception("Insufficient stock for " . $itemName . ".");
                }

                $stock->decrement('quantity_in_base_unit', $baseQty);

                StockLedger::create([
                    'product_id'                => $productId,
                    'variant_id'                => $variantId,
                    'warehouse_id'              => $warehouse_id,
                    'ref_type'                  => 'sale',
                    'ref_id'                    => $sale->id,
                    'quantity_change_in_base_unit' => $baseQty,
                    'unit_cost'                 => $actualPrice,
                    'direction'                 => 'out',
                    'created_by'                => auth()->id(),
                ]);
            }

            // $amountPaid = $sale->final_amount;
            // if ($amountPaid > 0) {
            //     Payment::create([
            //         'entity_type'      => 'customer',
            //         'entity_id'        => $customerId,
            //         'transaction_type' => 'out',
            //         'ref_type'         => 'sale',
            //         'ref_id'           => $sale->id,
            //         'amount'           => $amountPaid,
            //         'method'           => $request->payment_method ?? 'Cash On Delivery',
            //         'created_by'       => auth()->id(),
            //         'note'             => 'Payment for Sale ' . $invoiceNo,
            //     ]);
            // }

            DB::commit();

            Session::forget(['cart', 'coupon_code', 'coupon_discount', 'coupon_percentage']);

            $sale = Sale::with(['customer', 'branch', 'items.product', 'items.variant', 'items.unit'])->find($sale->id);

            $sale->currency_symbol = Setting::first()?->currency_symbol ?? '$';
            Mail::to($sale->customer->email)->send(new InvoiceSentMail($sale));

            // Redirect to the new thank you page, passing invoice number and total amount
            return redirect()->route('store.thankyou', [
                'invoiceNumber' => $sale->invoice_number,
                'totalAmount' => $sale->final_amount
            ])->with('success', 'Sale recorded successfully and cart cleared!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout process failed: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return back()->withInput()->with('error', 'Something went wrong during checkout. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function thankYou(Request $request){
        // Retrieve data from the redirect (query parameters or session flash data)
        $invoiceNumber = $request->query('invoiceNumber');
        $totalAmount = $request->query('totalAmount');
        // You might want to fetch the setting here if not already available globally
        $setting = Setting::first();

        return view('store.thankyou', compact('invoiceNumber', 'totalAmount', 'setting'));
    }
}