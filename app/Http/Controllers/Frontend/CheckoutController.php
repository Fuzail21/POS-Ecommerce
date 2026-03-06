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
use App\Services\SmsService;
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

        DB::beginTransaction();

        try {
            $cart = json_decode($request->cart_data, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart) || empty($cart)) {
                throw new \Exception('Invalid or empty cart data provided.');
            }

            $customerId = Auth::id();
            if (!$customerId) {
                throw new \Exception('User not authenticated. Cannot process sale.');
            }

            // Retrieve values from the request
            $discountAmount = $request->coupon_discount_amount ?? 0;
            $taxAmount      = $request->tax_amount ?? 0;
            $shippingCost   = $request->shipping ?? 0;
            $finalAmount    = $request->total_payable;
            $paidAmount     = $request->amount_paid ?? 0;
            $paymentMethod  = $request->payment_method ?? 'cash';

            // PRE-FLIGHT STOCK CHECK — verify every cart item before any DB write
            foreach ($cart as $item) {
                $variantId = $item['variant_id'] ?? null;
                $itemId    = $item['id'] ?? null;
                $productId = $variantId ? optional(ProductVariant::find($variantId))->product_id : $itemId;
                if (!$productId) continue;

                $unit    = isset($item['unit_id']) ? Unit::find($item['unit_id']) : null;
                $baseQty = ($item['quantity'] ?? 0) * ($unit->conversion_factor ?? 1);

                $available = InventoryStock::where('product_id', $productId)
                    ->where('variant_id', $variantId)
                    ->sum('quantity_in_base_unit');

                if ($available < $baseQty) {
                    throw new \Exception(
                        'Sorry, "' . ($item['name'] ?? 'a product') .
                        '" is out of stock. Available: ' . (int)$available . '.'
                    );
                }
            }

            $sale = Sale::create([
                'customer_id'    => $customerId,
                'branch_id'      => $branchId,
                'invoice_number' => $invoiceNo,
                'sale_date'      => now(),
                'total_amount'   => $finalAmount,
                'discount_amount'=> $discountAmount,
                'tax_amount'     => $taxAmount,
                'shipping'       => $shippingCost,
                'final_amount'   => $finalAmount,
                'paid_amount'    => $paidAmount,
                'due_amount'     => $finalAmount - $paidAmount,
                'payment_method' => $paymentMethod,
                'sale_origin'    => 'E-commerce',
                'status'         => 'pending',
                'created_by'     => auth()->id(),
            ]);

            foreach ($cart as $item) {
                $variantId = $item['variant_id'] ?? null;
                $itemId    = $item['id'] ?? null;
                $productId = $variantId ? ProductVariant::find($variantId)?->product_id : $itemId;

                if (!$productId || !Product::find($productId)) {
                    \Log::warning("Skipping invalid product/variant during checkout. Item: " . json_encode($item));
                    continue;
                }

                $unitId      = $item['unit_id'] ?? null;
                $quantity    = $item['quantity'] ?? 0;
                $unitPrice   = $item['price'] ?? 0;
                $actualPrice = $item['actual_price'] ?? $unitPrice;
                $totalPrice  = $unitPrice * $quantity;

                $unit    = $unitId ? Unit::find($unitId) : null;
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

                // Deduct stock greedily from whichever warehouses have available qty
                $stocks = InventoryStock::where('product_id', $productId)
                    ->where('variant_id', $variantId)
                    ->where('quantity_in_base_unit', '>', 0)
                    ->orderBy('quantity_in_base_unit', 'desc')
                    ->lockForUpdate()
                    ->get();

                $remaining = $baseQty;
                foreach ($stocks as $stockRow) {
                    if ($remaining <= 0) break;
                    $deduct = min($remaining, $stockRow->quantity_in_base_unit);
                    $stockRow->decrement('quantity_in_base_unit', $deduct);
                    StockLedger::create([
                        'product_id'                   => $productId,
                        'variant_id'                   => $variantId,
                        'warehouse_id'                 => $stockRow->warehouse_id,
                        'ref_type'                     => 'sale',
                        'ref_id'                       => $sale->id,
                        'quantity_change_in_base_unit' => $deduct,
                        'unit_cost'                    => $actualPrice,
                        'direction'                    => 'out',
                        'created_by'                   => auth()->id(),
                    ]);
                    $remaining -= $deduct;
                }
            }

            // Record payment if money was actually received at checkout
            // 'in' = money enters the business from the customer
            if ($paidAmount > 0) {
                Payment::create([
                    'entity_type'      => 'customer',
                    'entity_id'        => $customerId,
                    'transaction_type' => 'in',
                    'ref_type'         => 'sale',
                    'ref_id'           => $sale->id,
                    'amount'           => $paidAmount,
                    'payment_method'   => $paymentMethod,
                    'created_by'       => auth()->id(),
                    'note'             => 'E-commerce payment for ' . $invoiceNo,
                ]);
            }

            // Update customer balance for any outstanding due amount
            $dueAmount = $finalAmount - $paidAmount;
            if ($dueAmount > 0) {
                Customer::where('id', $customerId)->increment('balance', $dueAmount);
            }

            DB::commit();

            Session::forget(['cart', 'coupon_code', 'coupon_discount', 'coupon_percentage']);

            $sale = Sale::with(['customer', 'branch', 'items.product', 'items.variant', 'items.unit'])->find($sale->id);

            $sale->currency_symbol = Setting::first()?->currency_symbol ?? '$';
            try {
                Mail::to($sale->customer->email)->send(new InvoiceSentMail($sale));
            } catch (\Exception $mailEx) {
                \Log::warning('Checkout invoice email failed: ' . $mailEx->getMessage());
            }

            // SMS confirmation
            if ($sale->customer->phone) {
                try {
                    app(SmsService::class)->sendOrderPlaced(
                        $sale->customer->phone,
                        $sale->invoice_number,
                        $sale->final_amount
                    );
                } catch (\Exception $smsEx) {
                    \Log::warning('Checkout SMS failed: ' . $smsEx->getMessage());
                }
            }

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

    public function orders()
    {
        $orders = Sale::where('customer_id', auth('customer')->id())
            ->where('sale_origin', 'E-commerce')
            ->with(['items.product', 'items.variant'])
            ->latest('sale_date')
            ->paginate(10);

        $setting = Setting::first();

        return view('store.orders', compact('orders', 'setting'));
    }

    public function orderDetail($id)
    {
        $order = Sale::where('customer_id', auth('customer')->id())
            ->where('sale_origin', 'E-commerce')
            ->with(['items.product', 'items.variant'])
            ->findOrFail($id);

        $setting = Setting::first();

        return view('store.order-detail', compact('order', 'setting'));
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