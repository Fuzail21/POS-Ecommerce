<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\InventoryStock;
use App\Models\StockLedger;
use App\Models\ProductVariant;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Unit;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceSentMail;
use App\Models\DiscountRule;
use Auth;

class SaleController extends Controller
{
    public function index(){
        $title = "Sales List";
        $sales = Sale::with(['customer', 'items', 'payments', 'branch'])
                     ->withCount('salesReturns')
                     ->latest()
                     ->paginate(10);
        return view('admin.sale.list', compact('sales', 'title'));
    }

    // public function create(Request $request){
    //     $title = "Add Sale";
    //     $categories = Category::all();
    //     $branches = Branch::all();
    //     $customers = Customer::all();
    //     $units = Unit::all();
    //     $setting = Setting::first(); // Retrieve the setting for currency symbol, etc.

    //     $search = $request->input('search');

    //     $products = collect(); // Initialize as an empty collection

    //     if ($search) {
    //         // Logic for when a search query is present
    //         $products = Product::whereNull('deleted_at')
    //             ->where(function ($query) use ($search) {
    //                 $query->where('name', 'like', "%{$search}%")
    //                       ->orWhere('sku', 'like', "%{$search}%")
    //                       ->orWhere('barcode', 'like', "%{$search}%");
    //             })
    //             ->with([
    //                 'baseUnit',
    //                 'variants.inventoryStocks',
    //                 'variants.product.baseUnit',
    //                 'inventoryStocks',
    //             ])
    //             ->get()
    //             ->map(function ($product) {
    //                 $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
    //                 $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //                 $product->stock_quantity = $baseQuantity / $conversionFactor;
    //                 $product->in_stock = $product->stock_quantity > 0;

    //                 foreach ($product->variants as $variant) {
    //                     $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
    //                     $variantQuantity = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //                     $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
    //                     $variant->in_stock = $variant->stock_quantity > 0;
    //                 }
    //                 return $product;
    //             });
    //     } else {
    //         // Logic for initial page load (no search query)
    //         $products = Product::whereNull('deleted_at')
    //             ->latest()
    //             ->take(10) // Display the 10 latest products by default
    //             ->with([
    //                 'baseUnit',
    //                 'variants.inventoryStocks',
    //                 'variants.product.baseUnit',
    //                 'inventoryStocks',
    //             ])
    //             ->get()
    //             ->map(function ($product) {
    //                 $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
    //                 $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //                 $product->stock_quantity = $baseQuantity / $conversionFactor;
    //                 $product->in_stock = $product->stock_quantity > 0;

    //                 foreach ($product->variants as $variant) {
    //                     $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
    //                     $variantQuantity = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //                     $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
    //                     $variant->in_stock = $variant->stock_quantity > 0;
    //                 }
    //                 return $product;
    //             });
    //     }

    //     // Check if the request is an AJAX request
    //     if ($request->ajax()) {
    //         // If it's an AJAX request, build the HTML string for the product list directly.
    //         // This replaces the need for a separate partial view file.
    //         $html = '';
    //         foreach ($products as $product) {
    //             $isOutOfStock = !$product->in_stock && $product->variants->count() === 0;
    //             $productImgSrc = !empty($product->product_img) ? asset('storage/' . $product->product_img) : 'https://placehold.co/100x100/f0f0f0/808080?text=N/A'; // Placeholder if no image

    //             $html .= '<div class="col-md-3 mb-2 product-item d-flex">';
    //             $html .= '<div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 ' . ($isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '') . '">';

    //             // Product Image or Placeholder
    //             if (!empty($product->product_img)) {
    //                 $html .= '<img src="' . asset('storage/' . $product->product_img) . '" alt="Product Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">';
    //             } else {
    //                 $html .= '<div style="width: 100px; height: 100px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin: auto;">N/A</div>';
    //             }

    //             $html .= '<h6 class="mt-2 mb-1">' . htmlspecialchars($product->name) . '</h6>';

    //             if ($product->variants->count()) {
    //                 $html .= '<select class="form-control mb-2 variant-selector mt-auto" data-product-id="' . $product->id . '">';
    //                 $html .= '<option disabled selected>Choose Variant</option>';
    //                 foreach ($product->variants as $variant) {
    //                     $disabled = !$variant->in_stock ? 'disabled' : '';
    //                     $stockText = !$variant->in_stock ? '(Out of Stock)' : '(Stock: ' . $variant->stock_quantity . ')';
    //                     $html .= '<option ' . $disabled . ' value="variant-' . $variant->id . '" ' .
    //                              'data-name="' . htmlspecialchars($product->name . ' - ' . $variant->variant_name) . '" ' .
    //                              'data-price="' . $variant->actual_price . '" ' .
    //                              'data-stock="' . $variant->stock_quantity . '" ' .
    //                              'data-unit-id="' . $product->default_display_unit_id . '">' .
    //                              htmlspecialchars($variant->variant_name) . ' - ' . $setting->currency_symbol . ' ' . number_format($variant->actual_price, 2) . ' ' . $stockText .
    //                              '</option>';
    //                 }
    //                 $html .= '</select>';
    //                 $html .= '<button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>';
    //             } else {
    //                 $html .= '<p class="mb-1">' . $setting->currency_symbol . ' ' . number_format($product->actual_price, 2) .
    //                          '<br><small>(Stock: ' . $product->stock_quantity . ')</small></p>';
    //                 if ($product->in_stock) {
    //                     $html .= '<button ' .
    //                              'class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart" ' .
    //                              'data-id="product-' . $product->id . '" ' .
    //                              'data-name="' . htmlspecialchars($product->name) . '" ' .
    //                              'data-price="' . $product->actual_price . '" ' .
    //                              'data-stock="' . $product->stock_quantity . '" ' .
    //                              'data-unit-id="' . $product->default_display_unit_id . '">' .
    //                              'Add to Cart' .
    //                              '</button>';
    //                 } else {
    //                     $html .= '<button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>';
    //                 }
    //             }
    //             $html .= '</div>';
    //             $html .= '</div>';
    //         }
    //         return $html; // Return the generated HTML string
    //     }

    //     // If it's a regular (non-AJAX) request, return the full create sale view.
    //     return view('admin.sale.create', compact(
    //         'categories',
    //         'units',
    //         'products',
    //         'title',
    //         'customers',
    //         'branches',
    //         'setting' // Ensure the setting variable is passed to the main view
    //     ));
    // }

    public function create(Request $request){
        $title = "Add Sale";
        $categories = Category::all();
        $branches = Branch::all();
        $customers = Customer::all();
        $units = Unit::all();
        $setting = Setting::first();

        $search = $request->input('search');
        $products = collect();

        // ✅ Load active discount rules
        $now = now();
        $activeDiscounts = \App\Models\DiscountRule::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        $query = Product::whereNull('deleted_at')
            ->with([
                'baseUnit',
                'variants.inventoryStocks',
                'variants.product.baseUnit',
                'inventoryStocks',
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        } else {
            $query->latest()->take(12);
        }

            $products = $query->get()->map(function ($product) use ($activeDiscounts) {
                $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
                $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                $product->stock_quantity = $baseQuantity / $conversionFactor;
                $product->in_stock = $product->stock_quantity > 0;

                $product->discounted_price = $product->actual_price;

                // ✅ Check if this product or its category has a discount
                foreach ($activeDiscounts as $rule) {
                    $targets = collect(json_decode($rule->target_ids));
                    if (
                        ($rule->type === 'product' && $targets->contains($product->id)) ||
                        ($rule->type === 'category' && $targets->contains($product->category_id))
                    ) {
                        $product->discounted_price = $product->actual_price - ($product->actual_price * ($rule->discount / 100));
                        break;
                    }
                }

                foreach ($product->variants as $variant) {
                    $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
                    $variantQuantity = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                    $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
                    $variant->in_stock = $variant->stock_quantity > 0;

                    $variant->discounted_price = $variant->actual_price;

                    foreach ($activeDiscounts as $rule) {
                        $targets = collect(json_decode($rule->target_ids));
                        if (
                            ($rule->type === 'product' && $targets->contains($product->id)) ||
                            ($rule->type === 'category' && $targets->contains($product->category_id))
                        ) {
                            $variant->discounted_price = $variant->actual_price - ($variant->actual_price * ($rule->discount / 100));
                            break;
                        }
                    }
                }

                return $product;
            });

            // ✅ AJAX: Return product cards with discounted prices
            if ($request->ajax()) {
                $html = '';

                foreach ($products as $product) {
                    $isOutOfStock = !$product->in_stock && $product->variants->count() === 0;
                    $productImgSrc = !empty($product->product_img)
                    ? asset('storage/' . $product->product_img)
                    : 'https://placehold.co/100x100/f0f0f0/808080?text=N/A';
                    $html .= '<div class="col-md-3 mb-2 product-item d-flex">';
                    $html .= '<div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 ' . ($isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '') . '">';
                    // Product image
                    if (!empty($product->product_img)) {
                        $html .= '<img src="' . $productImgSrc . '" alt="Product Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">';
                    } else {
                        $html .= '<div style="width: 100px; height: 100px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin: auto;">N/A</div>';
                    }
                    $html .= '<h6 class="mt-2 mb-1">' . htmlspecialchars($product->name) . '</h6>';
                    if ($product->variants->count()) {
                        $html .= '<select class="form-control mb-2 variant-selector mt-auto" data-product-id="' . $product->id . '">';
                        $html .= '<option disabled selected>Choose Variant</option>';
                        foreach ($product->variants as $variant) {
                            $disabled = !$variant->in_stock ? 'disabled' : '';
                            $stockText = !$variant->in_stock ? '(Out of Stock)' : '(Stock: ' . $variant->stock_quantity . ')';
                            $finalVariantPrice = $variant->discounted_price;
                            $hasVariantDiscount = $finalVariantPrice < $variant->actual_price;
                            $label = htmlspecialchars($variant->variant_name) . ' - ';
                            if ($hasVariantDiscount) {
                                $label .= '<del>' . $setting->currency_symbol . ' ' . number_format($variant->actual_price, 2) . '</del> ';
                                $label .= '<strong style="color:red;">' . $setting->currency_symbol . ' ' . number_format($finalVariantPrice, 2) . '</strong>';
                            } else {
                                $label .= $setting->currency_symbol . ' ' . number_format($variant->actual_price, 2);
                            }
                            $label .= ' ' . $stockText;
                            $html .= '<option ' . $disabled . ' value="variant-' . $variant->id . '" ' .
                                'data-name="' . htmlspecialchars($product->name . ' - ' . $variant->variant_name) . '" ' .
                                'data-price="' . $finalVariantPrice . '" ' .
                                'data-stock="' . $variant->stock_quantity . '" ' .
                                'data-unit-id="' . $product->default_display_unit_id . '">' .
                                $label .
                                '</option>';
                        }
                        $html .= '</select>';
                        $html .= '<button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>';
                    } else {
                        $finalPrice = $product->discounted_price;
                        $hasDiscount = $finalPrice < $product->actual_price;
                        $html .= '<p class="mb-1">';
                        if ($hasDiscount) {
                            $html .= '<del>' . $setting->currency_symbol . ' ' . number_format($product->actual_price, 2) . '</del> ';
                            $html .= '<strong style="color:red;">' . $setting->currency_symbol . ' ' . number_format($finalPrice, 2) . '</strong>';
                        } else {
                            $html .= $setting->currency_symbol . ' ' . number_format($product->actual_price, 2);
                        }
                        $html .= '<br><small>(Stock: ' . $product->stock_quantity . ')</small></p>';
                        if ($product->in_stock) {
                            $html .= '<button ' .
                                'class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart" ' .
                                'data-id="product-' . $product->id . '" ' .
                                'data-name="' . htmlspecialchars($product->name) . '" ' .
                                'data-price="' . $finalPrice . '" ' .
                                'data-stock="' . $product->stock_quantity . '" ' .
                                'data-unit-id="' . $product->default_display_unit_id . '">' .
                                'Add to Cart' .
                                '</button>';
                        } else {
                            $html .= '<button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>';
                        }
                    }

                    $html .= '</div></div>';
                }

                return $html;
            }

            return view('admin.sale.create', compact(
                'categories',
                'units',
                'products',
                'title',
                'customers',
                'branches',
                'setting'
            ));
    }

    public function process(Request $request){
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

        $branch = Branch::find($request->branch_id);
        $warehouse_id = $branch->warehouse_id ?? null;

        if (!$warehouse_id) {
            return back()->withInput()->with('error', 'Branch does not have an associated warehouse.');
        }

        DB::beginTransaction();

        try {
            $cart = json_decode($request->cart_data, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart)) {
                throw new \Exception('Invalid cart data.');
            }

            // Create the sale
            $sale = Sale::create([
                'customer_id'     => $request->customer_id,
                'branch_id'       => $request->branch_id,
                'invoice_number'  => $invoiceNo,
                'sale_date'       => now(),
                'total_amount'    => $request->subtotal,
                'discount_amount' => $request->discount,
                'tax_amount'      => $request->tax,
                'shipping'        => $request->shipping,
                'final_amount'    => $request->total_payable,
                'paid_amount'     => $request->amount_paid,
                'due_amount'      => $request->balance_due,
                'payment_method'  => $request->payment_method,
                'created_by'      => auth()->id(),
            ]);

            foreach ($cart as $item) {
                $variantId = $item['type'] === 'variant' ? $item['id'] : null;
                $productId = $variantId ? ProductVariant::find($variantId)?->product_id : $item['id'];

                if (!$productId || !Product::find($productId)) {
                    continue; // Skip invalid product
                }

                $unitId     = $item['unit_id'] ?? null;
                $quantity   = $item['qty'];
                $unitPrice  = $item['actual_price'];
                $totalPrice = $unitPrice * $quantity;

                $unit = Unit::find($unitId);
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

                // Update stock
                $stock = InventoryStock::where([
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'warehouse_id' => $warehouse_id,
                ])->first();

                if (!$stock || $stock->quantity_in_base_unit < $baseQty) {
                    // skip this product, do not fail the whole transaction
                    continue;
                }

                $stock->decrement('quantity_in_base_unit', $baseQty);

                StockLedger::create([
                    'product_id'                   => $productId,
                    'variant_id'                   => $variantId,
                    'warehouse_id'                 => $warehouse_id,
                    'ref_type'                     => 'sale',
                    'ref_id'                       => $sale->id,
                    'quantity_change_in_base_unit' => $baseQty,
                    'unit_cost'                    => $unitPrice,
                    'direction'                    => 'out',
                    'created_by'                   => auth()->id(),
                ]);
            }

            // Handle payment
            if ($request->amount_paid > 0) {
                Payment::create([
                    'entity_type'      => 'customer',
                    'entity_id'        => $request->customer_id,
                    'transaction_type' => 'out',
                    'ref_type'         => 'sale',
                    'ref_id'           => $sale->id,
                    'amount'           => $request->amount_paid,
                    'method'           => $request->payment_method,
                    'created_by'       => auth()->id(),
                    'note'             => null,
                ]);
            }

            // Adjust balance
            $dueAmount = $request->balance_due;
            if ($dueAmount > 0) {
                Customer::where('id', $request->customer_id)->increment('balance', $dueAmount);
            } elseif ($dueAmount < 0) {
                Customer::where('id', $request->customer_id)->decrement('balance', abs($dueAmount));
            }

            DB::commit();

            $sale = Sale::with(['customer', 'branch', 'items.product', 'items.variant', 'items.unit'])->latest()->first();
            $sale->currency_symbol = Setting::first()?->currency_symbol ?? 'PKR';
            Mail::to($sale->customer->email)->send(new InvoiceSentMail($sale));

            return redirect()->route('sales.list')->with('success', 'Sale recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }
   
    public function destroy($id){
        $sale = Sale::with('items')->findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($sale->items as $item) {
                $productId = $item->product_id;
                $variantId = $item->variant_id;
                $baseQty   = $item->quantity_in_base_unit;

                // Restore inventory stock
                $stock = InventoryStock::where([
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'warehouse_id' => null,
                ])->first();

                if ($stock) {
                    $stock->increment('quantity_in_base_unit', $baseQty);
                }

                // Delete stock ledger
                StockLedger::where([
                    'ref_type'     => 'sale',
                    'ref_id'       => $sale->id,
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'warehouse_id' => null,
                ])->delete();

                // Delete sale item
                $item->delete();
            }

            // Delete payment record(s)
            Payment::where([
                'ref_type' => 'sale',
                'ref_id'   => $sale->id,
            ])->delete();

            // Adjust customer balance
            if ($sale->due_amount > 0) {
                Customer::where('id', $sale->customer_id)->decrement('balance', $sale->due_amount);
            } elseif ($sale->due_amount < 0) {
                Customer::where('id', $sale->customer_id)->increment('balance', abs($sale->due_amount));
            }

            // Delete the sale record
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.list')->with('success', 'Sale deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Sale Deletion Failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'sale_id' => $id,
            ]);

            return redirect()->route('sales.list')->with('error', 'An error occurred while deleting the sale.');
        }
    }

    public function invoice($id){
        $title = 'Invoice';
        $sale = Sale::with(['customer', 'warehouse', 'items.unit', 'items.product', 'items.variant'])
                    ->findOrFail($id);

        return view('admin.sale.invoice', compact('sale', 'title')); // passes $purchase to the view
    }

    // public function pos(Request $request){
    //     $title = "Point of Sale"; // Title for the POS page
    //     $categories = Category::all();
    //     $branches = Branch::all();
    //     $customers = Customer::all();
    //     $units = Unit::all();
    //     $setting = Setting::first(); // Essential for currency symbol etc.

    //     $search = $request->input('search');

    //     $products = collect(); // Initialize as an empty collection

    //     if ($search) {
    //         // Logic for when a search query is present
    //         $products = Product::whereNull('deleted_at')
    //             ->where(function ($query) use ($search) {
    //                 $query->where('name', 'like', "%{$search}%")
    //                       ->orWhere('sku', 'like', "%{$search}%")
    //                       ->orWhere('barcode', 'like', "%{$search}%");
    //             })
    //             ->with([
    //                 'baseUnit',
    //                 'variants.inventoryStocks',
    //                 'variants.product.baseUnit',
    //                 'inventoryStocks',
    //             ])
    //             ->get()
    //             ->map(function ($product) {
    //                 // Calculate stock for the main product
    //                 $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
    //                 $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //                 $product->stock_quantity = $baseQuantity / $conversionFactor;
    //                 $product->in_stock = $product->stock_quantity > 0;

    //                 // Calculate stock for each variant
    //                 foreach ($product->variants as $variant) {
    //                     $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
    //                     $variantQuantity = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //                     $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
    //                     $variant->in_stock = $variant->stock_quantity > 0;
    //                 }
    //                 return $product;
    //             });
    //     } else {
    //         // Logic for initial page load (no search query)
    //         $products = Product::whereNull('deleted_at')
    //             ->latest()
    //             ->take(10) // Display the 10 latest products by default
    //             ->with([
    //                 'baseUnit',
    //                 'variants.inventoryStocks',
    //                 'variants.product.baseUnit',
    //                 'inventoryStocks',
    //             ])
    //             ->get()
    //             ->map(function ($product) {
    //                 $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
    //                 $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //                 $product->stock_quantity = $baseQuantity / $conversionFactor;
    //                 $product->in_stock = $product->stock_quantity > 0;

    //                 foreach ($product->variants as $variant) {
    //                     $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
    //                     $variantQuantity = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //                     $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
    //                     $variant->in_stock = $variant->stock_quantity > 0;
    //                 }
    //                 return $product;
    //             });
    //     }

    //     // Check if the request is an AJAX request
    //     if ($request->ajax()) {
    //         // If it's an AJAX request, build the HTML string for the product list directly.
    //         // This replaces the need for a separate partial view file.
    //         $html = '';
    //         foreach ($products as $product) {
    //             $isOutOfStock = !$product->in_stock && $product->variants->count() === 0;
    //             // Using a placeholder image for direct HTML generation if product_img is empty
    //             $productImgSrc = !empty($product->product_img) ? asset('storage/' . $product->product_img) : 'https://placehold.co/70x70/f0f0f0/808080?text=N/A';

    //             $html .= '<div class="col-md-3 mb-2 product-item d-flex">';
    //             $html .= '<div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 ' . ($isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '') . '">';

    //             // Product Image or Placeholder
    //             if (!empty($product->product_img)) {
    //                 $html .= '<img src="' . asset('storage/' . $product->product_img) . '" alt="Product Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">';
    //             } else {
    //                 // Use a placeholder image URL for consistency
    //                 $html .= '<div style="width: 70px; height: 70px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; margin: auto;">N/A</div>';
    //             }


    //             $html .= '<h6 class="mt-2 mb-1">' . htmlspecialchars($product->name) . '</h6>';

    //             if ($product->variants->count()) {
    //                 $html .= '<select class="form-control mb-2 variant-selector mt-auto" data-product-id="' . $product->id . '">';
    //                 $html .= '<option disabled selected>Choose Variant</option>';
    //                 foreach ($product->variants as $variant) {
    //                     $disabled = !$variant->in_stock ? 'disabled' : '';
    //                     $stockText = !$variant->in_stock ? '(Out of Stock)' : '(Stock: ' . $variant->stock_quantity . ')';
    //                     $html .= '<option ' . $disabled . ' value="variant-' . $variant->id . '" ' .
    //                              'data-name="' . htmlspecialchars($product->name . ' - ' . $variant->variant_name) . '" ' .
    //                              'data-price="' . $variant->actual_price . '" ' .
    //                              'data-stock="' . $variant->stock_quantity . '" ' .
    //                              'data-unit-id="' . $product->default_display_unit_id . '">' .
    //                              htmlspecialchars($variant->variant_name) . ' - ' . htmlspecialchars($setting->currency_symbol) . ' ' . number_format($variant->actual_price, 2) . ' ' . $stockText .
    //                              '</option>';
    //                 }
    //                 $html .= '</select>';
    //                 $html .= '<button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>';
    //             } else {
    //                 $html .= '<p class="mb-1">' . htmlspecialchars($setting->currency_symbol) . ' ' . number_format($product->actual_price, 2) .
    //                          '<br><small>(Stock: ' . $product->stock_quantity . ')</small></p>';
    //                 if ($product->in_stock) {
    //                     $html .= '<button ' .
    //                              'class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart" ' .
    //                              'data-id="product-' . $product->id . '" ' .
    //                              'data-name="' . htmlspecialchars($product->name) . '" ' .
    //                              'data-price="' . $product->actual_price . '" ' .
    //                              'data-stock="' . $product->stock_quantity . '" ' .
    //                              'data-unit-id="' . $product->default_display_unit_id . '">' .
    //                              'Add to Cart' .
    //                              '</button>';
    //                 } else {
    //                     $html .= '<button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>';
    //                 }
    //             }
    //             $html .= '</div>';
    //             $html .= '</div>';
    //         }
    //         return $html; // Return the generated HTML string
    //     }

    //     // If it's a regular (non-AJAX) request, return the full POS view.
    //     return view('pos', compact(
    //         'categories',
    //         'units',
    //         'products',
    //         'title',
    //         'customers',
    //         'branches',
    //         'setting' // Ensure the setting variable is passed to the main view
    //     ));
    // }

    public function pos(Request $request){
        $title = "Point of Sale";
        $categories = Category::all();
        $branches = Branch::all();
        $customers = Customer::all();
        $units = Unit::all();
        $setting = Setting::first();

        $search = $request->input('search');

        $now = now();
        $activeDiscounts = \App\Models\DiscountRule::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        $query = Product::whereNull('deleted_at')
            ->with([
                'baseUnit',
                'variants.inventoryStocks',
                'variants.product.baseUnit',
                'inventoryStocks',
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        } else {
            $query->latest()->take(10);
        }

        $products = $query->get()->map(function ($product) use ($activeDiscounts) {
            $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
            $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
            $product->stock_quantity = $baseQuantity / $conversionFactor;
            $product->in_stock = $product->stock_quantity > 0;

            $product->final_price = $product->actual_price;

            foreach ($activeDiscounts as $rule) {
                $targets = collect(json_decode($rule->target_ids));
                if (
                    ($rule->type === 'product' && $targets->contains($product->id)) ||
                    ($rule->type === 'category' && $targets->contains($product->category_id))
                ) {
                    $product->final_price = $product->actual_price - ($product->actual_price * ($rule->discount / 100));
                    break;
                }
            }

            foreach ($product->variants as $variant) {
                $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
                $variantQuantity = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
                $variant->in_stock = $variant->stock_quantity > 0;

                $variant->final_price = $variant->actual_price;

                foreach ($activeDiscounts as $rule) {
                    $targets = collect(json_decode($rule->target_ids));
                    if (
                        ($rule->type === 'product' && $targets->contains($product->id)) ||
                        ($rule->type === 'category' && $targets->contains($product->category_id))
                    ) {
                        $variant->final_price = $variant->actual_price - ($variant->actual_price * ($rule->discount / 100));
                        break;
                    }
                }
            }

            return $product;
        });

        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                $isOutOfStock = !$product->in_stock && $product->variants->count() === 0;
                $productImgSrc = !empty($product->product_img) ? asset('storage/' . $product->product_img) : 'https://placehold.co/70x70/f0f0f0/808080?text=N/A';

                $html .= '<div class="col-md-3 mb-2 product-item d-flex">';
                $html .= '<div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 ' . ($isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '') . '">';
                $html .= '<img src="' . $productImgSrc . '" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">';
                $html .= '<h6 class="mt-2 mb-1">' . htmlspecialchars($product->name) . '</h6>';

                if ($product->variants->count()) {
                    $html .= '<select class="form-control mb-2 variant-selector mt-auto" data-product-id="' . $product->id . '">';
                    $html .= '<option disabled selected>Choose Variant</option>';
                    foreach ($product->variants as $variant) {
                        $disabled = !$variant->in_stock ? 'disabled' : '';
                        $stockText = !$variant->in_stock ? '(Out of Stock)' : '(Stock: ' . $variant->stock_quantity . ')';
                        $html .= '<option ' . $disabled . ' value="variant-' . $variant->id . '" ' .
                            'data-name="' . htmlspecialchars($product->name . ' - ' . $variant->variant_name) . '" ' .
                            'data-price="' . $variant->final_price . '" ' .
                            'data-stock="' . $variant->stock_quantity . '" ' .
                            'data-unit-id="' . $product->default_display_unit_id . '">' .
                            htmlspecialchars($variant->variant_name) . ' - ' . htmlspecialchars($setting->currency_symbol) . ' ' . number_format($variant->final_price, 2) . ' ' . $stockText .
                            '</option>';
                    }
                    $html .= '</select>';
                    $html .= '<button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>';
                } else {
                    $html .= '<p class="mb-1">';
                    if ($product->actual_price != $product->final_price) {
                        $html .= '<del>' . $setting->currency_symbol . ' ' . number_format($product->actual_price, 2) . '</del> '; 
                    }
                    $html .= $setting->currency_symbol . ' ' . number_format($product->final_price, 2);
                    $html .= '<br><small>(Stock: ' . $product->stock_quantity . ')</small></p>';

                    if ($product->in_stock) {
                        $html .= '<button class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart" ' .
                            'data-id="product-' . $product->id . '" ' .
                            'data-name="' . htmlspecialchars($product->name) . '" ' .
                            'data-price="' . $product->final_price . '" ' .
                            'data-stock="' . $product->stock_quantity . '" ' .
                            'data-unit-id="' . $product->default_display_unit_id . '">' .
                            'Add to Cart</button>';
                    } else {
                        $html .= '<button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>';
                    }
                }
                $html .= '</div></div>';
            }
            return $html;
        }

        return view('pos', compact('categories', 'units', 'products', 'title', 'customers', 'branches', 'setting'));
    }

    public function posProcess(Request $request){
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

        $branch = Branch::find($request->branch_id);
        $warehouse_id = $branch->warehouse_id ?? null;

        if (!$warehouse_id) {
            return back()->withInput()->with('error', 'Branch does not have an associated warehouse.');
        }

        DB::beginTransaction();

        try {
            $cart = json_decode($request->cart_data, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart)) {
                throw new \Exception('Invalid cart data.');
            }

            // Create the sale
            $sale = Sale::create([
                'customer_id'     => $request->customer_id,
                'branch_id'       => $request->branch_id,
                'invoice_number'  => $invoiceNo,
                'sale_date'       => now(),
                'total_amount'    => $request->subtotal,
                'discount_amount' => $request->discount,
                'tax_amount'      => $request->tax,
                'shipping'        => $request->shipping,
                'final_amount'    => $request->total_payable,
                'paid_amount'     => $request->amount_paid,
                'due_amount'      => $request->balance_due,
                'payment_method'  => $request->payment_method,
                'created_by'      => auth()->id(),
            ]);

            foreach ($cart as $item) {
                $variantId = $item['type'] === 'variant' ? $item['id'] : null;
                $productId = $variantId ? ProductVariant::find($variantId)?->product_id : $item['id'];

                if (!$productId || !Product::find($productId)) {
                    continue; // Skip invalid product
                }

                $unitId     = $item['unit_id'] ?? null;
                $quantity   = $item['qty'];
                $unitPrice  = $item['actual_price'];
                $totalPrice = $unitPrice * $quantity;

                $unit = Unit::find($unitId);
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

                // Update stock
                $stock = InventoryStock::where([
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'warehouse_id' => $warehouse_id,
                ])->first();

                if (!$stock || $stock->quantity_in_base_unit < $baseQty) {
                    // skip this product, do not fail the whole transaction
                    continue;
                }

                $stock->decrement('quantity_in_base_unit', $baseQty);

                StockLedger::create([
                    'product_id'                   => $productId,
                    'variant_id'                   => $variantId,
                    'warehouse_id'                 => $warehouse_id,
                    'ref_type'                     => 'sale',
                    'ref_id'                       => $sale->id,
                    'quantity_change_in_base_unit' => $baseQty,
                    'unit_cost'                    => $unitPrice,
                    'direction'                    => 'out',
                    'created_by'                   => auth()->id(),
                ]);
            }

            // Handle payment
            if ($request->amount_paid > 0) {
                Payment::create([
                    'entity_type'      => 'customer',
                    'entity_id'        => $request->customer_id,
                    'transaction_type' => 'out',
                    'ref_type'         => 'sale',
                    'ref_id'           => $sale->id,
                    'amount'           => $request->amount_paid,
                    'method'           => $request->payment_method,
                    'created_by'       => auth()->id(),
                    'note'             => null,
                ]);
            }

            // Adjust balance
            $dueAmount = $request->balance_due;
            if ($dueAmount > 0) {
                Customer::where('id', $request->customer_id)->increment('balance', $dueAmount);
            } elseif ($dueAmount < 0) {
                Customer::where('id', $request->customer_id)->decrement('balance', abs($dueAmount));
            }

            DB::commit();
            session([
                'show_invoice' => true,
                'sale_id' => $sale->id,
            ]);

            $sale = Sale::with(['customer', 'branch', 'items.product', 'items.variant', 'items.unit'])->latest()->first();
            $sale->currency_symbol = Setting::first()?->currency_symbol ?? 'PKR';
            Mail::to($sale->customer->email)->send(new InvoiceSentMail($sale));

            return redirect()->route('pos.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
