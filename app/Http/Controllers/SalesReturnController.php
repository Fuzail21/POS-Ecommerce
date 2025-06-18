<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem; // Assuming you have this model for original sale items
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Unit;
use App\Models\InventoryStock;
use App\Models\StockLedger;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Branch; // Assuming Branch model is used to get warehouse_id
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesReturnController extends Controller
{
    public function list(){
        $title = "Sales Returns List";

        $salesReturns = SalesReturn::with(['customer', 'branch', 'sale', 'user']) // Eager load related models
                                  ->withCount('salesReturnItems') // Count the associated return items
                                  ->latest() // Order by latest returns
                                  ->paginate(10); // Paginate the results

        return view('admin.sale.sales_return_list', compact('salesReturns', 'title'));
    }

    public function create(Sale $sale, Request $request){
        $title = "Create Sale Return"; // Specific title for the return page
        $categories = Category::all();
        $branches = Branch::all();
        $customers = Customer::all();
        $units = Unit::all();

        // Eager load the sale items with their product and variant details,
        // and crucially, the product associated with the variant.
        $sale->load('items.product', 'items.variant.product');
        $saleItems = $sale->items->filter(function ($item) {
            return $item->product_id || $item->product_variant_id;
        })->values();

        $hasExistingReturn = SalesReturn::where('sale_id', $sale->id)->exists();

        // Product search logic for the right-hand product selection panel
        $search = $request->input('search');
        $products = collect(); // Initialize as an empty collection

        if ($search) {
            $products = Product::whereNull('deleted_at')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%")
                          ->orWhere('barcode', 'like', "%{$search}%");
                })
                ->with([
                    'baseUnit',
                    'variants.inventoryStocks', // Assuming 'inventoryStocks' is HasMany
                    'variants.product.baseUnit',
                    'inventoryStocks', // Assuming 'inventoryStocks' is HasMany on Product
                ])
                ->get()
                ->map(function ($product) {
                    // Calculate stock for simple products - Robust null check for sum()
                    $baseQuantity = $product->inventoryStocks ? $product->inventoryStocks->sum('quantity_in_base_unit') : 0;
                    $product->stock_quantity = ($product->baseUnit->conversion_factor ?? 1) > 0 ? $baseQuantity / ($product->baseUnit->conversion_factor ?? 1) : 0;
                    $product->in_stock = $product->stock_quantity > 0;

                    // Calculate stock for variants - Robust null check for sum()
                    foreach ($product->variants as $variant) {
                        $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
                        $variantQuantity = $variant->inventoryStocks ? $variant->inventoryStocks->sum('quantity_in_base_unit') : 0;
                        $variant->stock_quantity = $variantConversionFactor > 0 ? $variantQuantity / $variantConversionFactor : 0;
                        $variant->in_stock = $variant->stock_quantity > 0;
                    }
                    return $product;
                });
        } else {
            // Load latest 10 products if no search query, similar to your original create method
            $products = Product::whereNull('deleted_at')
                ->latest()
                ->take(10)
                ->with([
                    'baseUnit',
                    'variants.inventoryStocks', // Corrected from inventoryStock to inventoryStocks - Assuming HasMany
                    'variants.product.baseUnit',
                    'inventoryStocks', // Assuming 'inventoryStocks' is HasMany on Product
                ])
                ->get()
                ->map(function ($product) {
                    // Calculate stock for simple products - Robust null check for sum()
                    $baseQuantity = $product->inventoryStocks ? $product->inventoryStocks->sum('quantity_in_base_unit') : 0;
                    $product->stock_quantity = ($product->baseUnit->conversion_factor ?? 1) > 0 ? $baseQuantity / ($product->baseUnit->conversion_factor ?? 1) : 0;
                    $product->in_stock = $product->stock_quantity > 0;

                    // Calculate stock for variants - Robust null check for sum()
                    foreach ($product->variants as $variant) {
                        $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
                        $variantQuantity = $variant->inventoryStocks ? $variant->inventoryStocks->sum('quantity_in_base_unit') : 0;
                        $variant->stock_quantity = $variantConversionFactor > 0 ? $variantQuantity / $variantConversionFactor : 0;
                        $variant->in_stock = $variant->stock_quantity > 0;
                    }
                    return $product;
                });
        }

        // Pass all necessary data to the view
        return view('admin.sale.sales_return_create', compact(
            'sale',         // The original Sale model instance
            'saleItems',    // Collection of products from the original sale to pre-fill the cart
            'categories',   // For product filtering (if you implement that)
            'units',        // For product display
            'products',     // Products for the search/selection panel
            'title',        // Page title
            'customers',    // For customer dropdown
            'branches',      // For branch dropdown
            'hasExistingReturn'
        ));
    }

    public function store(Request $request, Sale $sale){
        // 1. Validate incoming request data
        $request->validate([
            'cart_data'       => 'required|json', // JSON string containing returned items
            'total_payable'   => 'required|numeric', // This is the total value calculated on frontend (items + tax - discount + shipping)
            'amount_paid'     => 'required|numeric', // Amount actually refunded to customer
            'balance_due'     => 'required|numeric', // Frontend calculated balance/change
            'payment_method'  => 'required|string',
            'customer_id'     => 'required|exists:customers,id',
            'branch_id'       => 'required|exists:branches,id',
            // 'return_note'     => 'nullable|string', // Add this validation if you add a 'return_note' field to your form
            // 'subtotal'        => 'required|numeric', // Although not directly used for total_return_amount, useful for record
            // 'tax'             => 'required|numeric',
            // 'discount'        => 'required|numeric',
            // 'shipping'        => 'required|numeric',
        ]);

        // Get warehouse ID from branch
        $branch = Branch::find($request->branch_id);
        $warehouse_id = $branch->warehouse_id ?? null;

        if (!$warehouse_id) {
            return back()->withInput()->with('error', 'Branch does not have an associated warehouse.');
        }

        DB::beginTransaction(); // Start database transaction

        try {
            // 2. Decode cart data
            $cart = json_decode($request->cart_data, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart)) {
                throw new \Exception('Invalid cart data received: ' . json_last_error_msg());
            }

            // Filter out items with zero return quantity before processing
            $returnedItems = array_filter($cart, function($item) {
                return (isset($item['qty']) && $item['qty'] > 0);
            });

            if (empty($returnedItems)) {
                // If no items are selected for return and no specific return note, might be an error or just a cancelled return
                // You might adjust this based on whether a return can exist with no items.
                return back()->with('error', 'No items selected for return with a quantity greater than zero.');
            }

            // 3. Calculate total return amount (gross value of items returned)
            $totalReturnAmount = 0;
            foreach ($returnedItems as $item) {
                // Use the sale_price sent from the frontend cart data,
                // which represents the price the item was sold for or its current sale price.
                $itemPrice = $item['sale_price'] ?? 0;
                $itemQuantity = $item['qty'] ?? 0;
                $totalReturnAmount += ($itemPrice * $itemQuantity);
            }

            // 4. Create the SalesReturn record
            $salesReturn = SalesReturn::create([
                'sale_id'             => $sale->id, // Original sale ID from route model binding
                'customer_id'         => $request->customer_id,
                'branch_id'           => $request->branch_id,
                'return_date'         => now(),
                'return_reason'       => $request->return_note ?? null, // 'return_note' should be the name of the textarea in your form
                'total_return_amount' => $totalReturnAmount, // The calculated gross value of returned items
                'refund_amount'       => $request->amount_paid, // Amount *actually* refunded to customer
                'payment_method'      => $request->payment_method,
                'created_by'          => auth()->id(),
            ]);

            // 5. Process Each Returned Item (Loop)
            foreach ($returnedItems as $item) {
                // Determine if it's a variant or simple product
                $variantId = ($item['type'] === 'variant' && isset($item['id'])) ? $item['id'] : null;
                // For a variant, get the product_id from the ProductVariant model
                // For a simple product, the product_id is directly item['id']
                $productId = ($item['type'] === 'variant' && $variantId) ? (ProductVariant::find($variantId)?->product_id) : ($item['id'] ?? null);

                if (!$productId || !Product::find($productId)) {
                    Log::warning("Sales Return: Skipping invalid product/variant. Product ID: {$productId}, Variant ID: {$variantId}, Item data: " . json_encode($item));
                    continue; // Skip invalid products/variants to avoid breaking the transaction
                }

                $unit = Unit::find($item['unit_id'] ?? null);
                $quantityInBaseUnit = ($item['qty'] ?? 0) * ($unit->conversion_factor ?? 1);

                // Create SalesReturnItem record
                SalesReturnItem::create([
                    'sales_return_id'       => $salesReturn->id,
                    'sale_id'               => $sale->id, // Original sale ID
                    'product_id'            => $productId,
                    'variant_id'            => $variantId,
                    'quantity'              => $item['qty'],
                    'unit_id'               => $item['unit_id'] ?? null,
                    'quantity_in_base_unit' => $quantityInBaseUnit,
                    'unit_price'            => $item['sale_price'], // Use the sale_price from the cart_data
                    'discount'              => $item['discount'] ?? 0.00, // Assuming these might be in cart_data if granular
                    'tax'                   => $item['tax'] ?? 0.00,
                    'total_price'           => ($item['sale_price'] ?? 0) * ($item['qty'] ?? 0),
                ]);

                // Update Inventory Stock (increment quantity for returned items)
                $stock = InventoryStock::firstOrCreate(
                    [
                        'product_id'   => $productId,
                        'variant_id'   => $variantId,
                        'warehouse_id' => $warehouse_id,
                    ],
                    // If not found, initialize with zero quantity
                    [
                        'quantity_in_base_unit' => 0,
                    ]
                );
                $stock->increment('quantity_in_base_unit', $quantityInBaseUnit);

                // Create StockLedger entry for 'in' movement
                StockLedger::create([
                    'product_id'                => $productId,
                    'variant_id'                => $variantId,
                    'warehouse_id'              => $warehouse_id,
                    'ref_type'                  => 'return', // Reference type for the ledger
                    'ref_id'                    => $salesReturn->id, // ID of the sales return record
                    'quantity_change_in_base_unit' => $quantityInBaseUnit,
                    'unit_cost'                 => $item['sale_price'], // Use the original sale price as a cost basis for return
                    'direction'                 => 'in', // Stock is coming back into inventory
                    'created_by'                => auth()->id(),
                ]);
            }

            // 6. Handle Payment & Customer Balance Adjustment
            $refundAmount = $request->amount_paid; // The amount actually given back to the customer
            $balanceDueRaw = $request->balance_due; // Calculated frontend: Total returned value - Amount Refunded

            // Record a payment if an actual refund happened (amount_paid > 0)
            if ($refundAmount > 0) {
                Payment::create([
                    'entity_type'      => 'customer',
                    'entity_id'        => $request->customer_id,
                    'transaction_type' => 'out', // Money flowed *out* from your business to the customer (refund)
                    'ref_type'         => 'sales_return',
                    'ref_id'           => $salesReturn->id,
                    'amount'           => $refundAmount,
                    'method'           => $request->payment_method,
                    'created_by'       => auth()->id(),
                    'note'             => 'Refund for sales return ' . $salesReturn->id . ' (Original Sale: ' . $sale->invoice_number . ')',
                ]);
            }

            // Adjust customer balance
            // If balanceDueRaw < 0: It means the customer received more refund than the value of returned items.
            // This would DECREMENT their outstanding balance, or increase their credit with you.
            // If balanceDueRaw > 0: It means the customer owes you money (e.g., returned less value than refunded).
            // This would INCREMENT their outstanding balance.
            if ($balanceDueRaw < 0) {
                Customer::where('id', $request->customer_id)->decrement('balance', abs($balanceDueRaw));
            } elseif ($balanceDueRaw > 0) {
                Customer::where('id', $request->customer_id)->increment('balance', $balanceDueRaw);
            }
            // If balanceDueRaw is 0, no change to customer balance is needed.

            DB::commit(); // Commit the transaction

            return redirect()->route('sales.list')->with('success', 'Sale return processed successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback all changes if an error occurs
            Log::error('Sales Return Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all() // Log the full request data for debugging
            ]);
            return back()->withInput()->with('error', 'Something went wrong while processing the return. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function details(){
        $title = "Sales Returns List";

        $salesReturns = SalesReturn::with(['customer', 'branch', 'sale', 'user']) // Eager load related models
                                  ->withCount('salesReturnItems') // Count the associated return items
                                  ->latest() // Order by latest returns
                                  ->paginate(10); // Paginate the results

        return view('admin.sale.sales_return_list', compact('salesReturns', 'title'));
    }

    public function show(SalesReturn $salesReturn){
        // Eager load all necessary relationships for display
        $salesReturn->load([
            'salesReturnItems.product', // For simple products in return items
            'salesReturnItems.variant.product', // For variant products in return items
            'salesReturnItems.unit', // For the display unit of return items
            'sale', // The original sale details
            'customer',
            'branch',
            'user' // The user who processed the return
        ]);

        $title = "Sales Return Details: RET-" . $salesReturn->id;

        return view('admin.sale.sale_return_items', compact('salesReturn', 'title'));
    }

}
