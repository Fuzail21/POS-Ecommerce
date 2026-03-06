<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer; // Assuming Customer model exists
use App\Models\Warehouse; // Assuming Warehouse model exists
use App\Models\Product; // Assuming Product model exists
use App\Models\ProductVariant; // Assuming ProductVariant model exists
use App\Models\Category;
use App\Models\Branch;
use App\Models\Unit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryStock;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotationSentMail;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Quotation List";
        // Fetch all quotations with their customer and items, ordered by creation date
        $quotations = Quotation::with(['customer', 'warehouse', 'items.product', 'items.productVariant'])
                              ->latest()
                              ->paginate(10); // Paginate results for better performance

        return view('admin.quotations.list', compact('quotations', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */

    // public function create(Request $request)
    // {
    //     $title = "Create Sale Quotation"; // Changed title for clarity
    //     $categories = Category::all();
    //     $branches = Branch::all();
    //     $customers = Customer::all();
    //     $warehouses = Warehouse::all();
    //     $units = Unit::all();
    //     $setting = Setting::first();

    //     $search = $request->input('search');

    //     $products = collect(); // Initialize as an empty collection

    //     if ($search) {
    //         $products = Product::whereNull('deleted_at')
    //             ->where(function ($query) use ($search) {
    //                 $query->where('name', 'like', "%{$search}%")
    //                     ->orWhere('sku', 'like', "%{$search}%")
    //                     ->orWhere('barcode', 'like', "%{$search}%");
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
    //         $products = Product::whereNull('deleted_at')
    //             ->latest()
    //             ->take(10)
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

    //     if ($request->ajax()) {
    //         $html = '';
    //         foreach ($products as $product) {
    //             $isOutOfStock = !$product->in_stock && $product->variants->count() === 0;
    //             $productImgSrc = !empty($product->product_img) ? asset('storage/' . $product->product_img) : 'https://placehold.co/100x100/f0f0f0/808080?text=N/A';

    //             $html .= '<div class="col-md-3 mb-2 product-item d-flex">';
    //             $html .= '<div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 ' . ($isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '') . '">';

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
    //         return $html;
    //     }

    //     return view('admin.quotations.create', compact(
    //         'categories',
    //         'units',
    //         'products',
    //         'title',
    //         'customers',
    //         'branches',
    //         'warehouses',
    //         'setting'
    //     ));
    // }

    public function create(Request $request){
        $title       = "Create Sale Quotation";
        $categories  = Category::all();
        $branches    = Branch::all();
        $customers   = Customer::all();
        $units       = Unit::all();
        $setting     = Setting::first();
        $search      = $request->input('search');
        $branchId    = $request->input('branch_id');
        $warehouseId = null;
        $products    = collect();

        if ($branchId) {
            $branch      = Branch::find($branchId);
            $warehouseId = $branch?->warehouse_id;
        }

        // AJAX with no branch selected — return placeholder
        if ($request->ajax() && !$warehouseId) {
            return '<div class="col-12 text-center text-muted py-5">
                        <p class="mb-0"><i class="fas fa-store fa-2x mb-2 d-block"></i>Select a branch above to see available products.</p>
                    </div>';
        }

        if ($warehouseId) {
            $now             = now();
            $activeDiscounts = \App\Models\DiscountRule::where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->get();

            $query = Product::whereNull('deleted_at')
                ->where(function ($q) use ($warehouseId) {
                    $q->whereHas('inventoryStocks', function ($sq) use ($warehouseId) {
                        $sq->where('warehouse_id', $warehouseId)
                           ->where('quantity_in_base_unit', '>', 0);
                    })->orWhereHas('variants.inventoryStocks', function ($sq) use ($warehouseId) {
                        $sq->where('warehouse_id', $warehouseId)
                           ->where('quantity_in_base_unit', '>', 0);
                    });
                })
                ->with([
                    'baseUnit',
                    'variants.product.baseUnit',
                    'variants.inventoryStocks' => fn($q) => $q->where('warehouse_id', $warehouseId),
                    'inventoryStocks'          => fn($q) => $q->where('warehouse_id', $warehouseId),
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
                $conversionFactor        = $product->baseUnit->conversion_factor ?? 1;
                $baseQuantity            = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                $product->stock_quantity = $baseQuantity / $conversionFactor;
                $product->in_stock       = $product->stock_quantity > 0;
                $product->discounted_price = $product->actual_price;

                foreach ($activeDiscounts as $rule) {
                    $targets = collect(json_decode($rule->target_ids));
                    if (
                        ($rule->type === 'product'  && $targets->contains($product->id)) ||
                        ($rule->type === 'category' && $targets->contains($product->category_id))
                    ) {
                        $product->discounted_price = $product->actual_price - ($product->actual_price * ($rule->discount / 100));
                        break;
                    }
                }

                foreach ($product->variants as $variant) {
                    $variantConversionFactor  = $variant->product->baseUnit->conversion_factor ?? 1;
                    $variantQuantity          = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                    $variant->stock_quantity  = $variantQuantity / $variantConversionFactor;
                    $variant->in_stock        = $variant->stock_quantity > 0;
                    $variant->discounted_price = $variant->actual_price;

                    foreach ($activeDiscounts as $rule) {
                        $targets = collect(json_decode($rule->target_ids));
                        if (
                            ($rule->type === 'product'  && $targets->contains($product->id)) ||
                            ($rule->type === 'category' && $targets->contains($product->category_id))
                        ) {
                            $variant->discounted_price = $variant->actual_price - ($variant->actual_price * ($rule->discount / 100));
                            break;
                        }
                    }
                }

                return $product;
            });
        }

        // AJAX: return product card HTML
        if ($request->ajax()) {
            if ($products->isEmpty()) {
                return '<div class="col-12 text-center text-muted py-5">
                            <p class="mb-0">No products with available stock at this branch.</p>
                        </div>';
            }

            $html = '';
            foreach ($products as $product) {
                $isOutOfStock  = !$product->in_stock && $product->variants->count() === 0;
                $productImgSrc = !empty($product->product_img)
                    ? asset('storage/' . $product->product_img)
                    : null;

                $html .= '<div class="col-md-3 mb-2 product-item d-flex">';
                $html .= '<div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 '
                    . ($isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '') . '">';

                if ($productImgSrc) {
                    $html .= '<img src="' . $productImgSrc . '" alt="Product Image" style="width:70px;height:70px;object-fit:cover;border-radius:8px;margin:auto;">';
                } else {
                    $html .= '<div style="width:100px;height:100px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;border-radius:8px;margin:auto;">N/A</div>';
                }

                $html .= '<h6 class="mt-2 mb-1">' . htmlspecialchars($product->name) . '</h6>';

                if ($product->variants->count()) {
                    $html .= '<select class="form-control mb-2 variant-selector mt-auto" data-product-id="' . $product->id . '">';
                    $html .= '<option disabled selected>Choose Variant</option>';
                    foreach ($product->variants as $variant) {
                        $disabled    = !$variant->in_stock ? 'disabled' : '';
                        $stockText   = $variant->in_stock ? '(Stock: ' . (int)$variant->stock_quantity . ')' : '(Out of Stock)';
                        $finalPrice  = $variant->discounted_price;
                        $hasDiscount = $finalPrice < $variant->actual_price;
                        $label = htmlspecialchars($variant->variant_name) . ' - ';
                        if ($hasDiscount) {
                            $label .= '<del>' . $setting->currency_symbol . ' ' . number_format($variant->actual_price, 2) . '</del> ';
                            $label .= '<strong style="color:red;">' . $setting->currency_symbol . ' ' . number_format($finalPrice, 2) . '</strong>';
                        } else {
                            $label .= $setting->currency_symbol . ' ' . number_format($variant->actual_price, 2);
                        }
                        $label .= ' ' . $stockText;
                        $html .= '<option ' . $disabled . ' value="variant-' . $variant->id . '" '
                            . 'data-name="' . htmlspecialchars($product->name . ' - ' . $variant->variant_name) . '" '
                            . 'data-price="' . $finalPrice . '" '
                            . 'data-stock="' . (int)$variant->stock_quantity . '" '
                            . 'data-unit-id="' . $product->default_display_unit_id . '">'
                            . $label . '</option>';
                    }
                    $html .= '</select>';
                    $html .= '<button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>';
                } else {
                    $finalPrice  = $product->discounted_price;
                    $hasDiscount = $finalPrice < $product->actual_price;
                    $html .= '<p class="mb-1">';
                    if ($hasDiscount) {
                        $html .= '<del>' . $setting->currency_symbol . ' ' . number_format($product->actual_price, 2) . '</del> ';
                        $html .= '<strong style="color:red;">' . $setting->currency_symbol . ' ' . number_format($finalPrice, 2) . '</strong>';
                    } else {
                        $html .= $setting->currency_symbol . ' ' . number_format($product->actual_price, 2);
                    }
                    $html .= '<br><small>(Stock: ' . (int)$product->stock_quantity . ')</small></p>';
                    if ($product->in_stock) {
                        $html .= '<button '
                            . 'class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart" '
                            . 'data-id="product-' . $product->id . '" '
                            . 'data-name="' . htmlspecialchars($product->name) . '" '
                            . 'data-price="' . $finalPrice . '" '
                            . 'data-stock="' . (int)$product->stock_quantity . '" '
                            . 'data-unit-id="' . $product->default_display_unit_id . '">'
                            . 'Add to Cart</button>';
                    } else {
                        $html .= '<button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>';
                    }
                }

                $html .= '</div></div>';
            }

            return $html;
        }

        return view('admin.quotations.create', compact(
            'categories', 'units', 'products', 'title',
            'customers', 'branches', 'setting'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        // dd($request->all());
        // Decode the cart_data JSON string into an array
        $cartData = json_decode($request->input('cart_data'), true);

        // Map cartData to an 'items' structure that aligns with your validation rules
        $mappedItems = [];
        if (is_array($cartData)) {
            foreach ($cartData as $id => $item) {
                // Determine if it's a simple product or a variant
                $isVariant = str_starts_with($id, 'variant-');
                $productId = null;
                $productVariantId = null;

                if ($isVariant) {
                    $productVariantId = $item['id']; // This is the variant ID
                    // You might need to fetch the product_id from the variant
                    $variant = ProductVariant::find($productVariantId);
                    if ($variant) {
                        $productId = $variant->product_id;
                    }
                } else {
                    $productId = $item['id']; // This is the simple product ID
                    $productVariantId = null; // No variant for simple products
                }

                $mappedItems[] = [
                    'product_id' => $productId,
                    'product_variant_id' => $productVariantId, // Can be null for simple products
                    'unit_price' => $item['actual_price'],
                    'quantity' => $item['qty'],
                    // These fields are not directly provided per item in your current JS cart structure,
                    // so ensure they are handled if needed, or adjust validation/db.
                    // For now, setting to 0 or null as they are nullable.
                    'discount_amount' => 0, // Or retrieve if you add per-item discount logic
                    'tax_amount' => 0,      // Or retrieve if you add per-item tax logic
                ];
            }
        }

        // Add the mapped items back to the request for validation
        $request->merge(['items' => $mappedItems]);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'nullable|exists:branches,id', // Validating against branch_id
            'quotation_date' => 'required|date', // If not added to view, set it in controller
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0', // Now receiving discount amount
            'tax' => 'nullable|numeric|min:0',      // Now receiving tax amount
            'shipping' => 'nullable|numeric|min:0',
            'total_payable' => 'required|numeric|min:0', // Use the final total from the view
            'status' => 'required|string|in:pending,sent', // Aligned with view options. Add others if needed.
            'note' => 'nullable|string',
            'cart_data' => 'required|json', // Validate the raw JSON string
            'items' => 'required|array|min:1', // Validate the processed items array
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id', // Make nullable for simple products
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
        ]);

        // Generate a unique quotation number
        $quotationNumber = 'QTN-' . date('Ymd') . '-' . uniqid();

        DB::beginTransaction();
        try {
            // Use the calculated totals directly from the view
            $subtotal = $request->input('subtotal');
            $discount = $request->input('discount') ?? 0;
            $tax = $request->input('tax') ?? 0;
            $taxPercentage = $request->input('taxrate') ?? 0;
            $shipping = $request->input('shipping') ?? 0;
            $finalGrandTotal = $request->input('total_payable');

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'customer_id' => $request->customer_id,
                'branch_id' => $request->branch_id, // Using warehouse_id from the request
                'quotation_date' => $request->quotation_date,
                'tax_percentage' => $taxPercentage,
                'order_tax_amount' => $tax,
                'discount_percentage' => $discount,
                'discount_type' => $request->input('discount_type', 'fixed'),
                'shipping_cost' => $shipping,
                'grand_total' => $finalGrandTotal,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            foreach ($mappedItems as $itemData) {
                // Item subtotal should reflect just unit_price * quantity for the item
                // The global discount and tax are handled at the quotation level.
                $itemSubtotal = $itemData['unit_price'] * $itemData['quantity'];

                $quotation->items()->create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $itemData['product_id'],
                    'product_variant_id' => $itemData['product_variant_id'],
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'discount_amount' => $itemData['discount_amount'] ?? 0, // From mapped item
                    'tax_amount' => $itemData['tax_amount'] ?? 0, // From mapped item
                    'subtotal' => $itemSubtotal, // This is the line item subtotal before global adjustments
                ]);
            }

            DB::commit();
            if ($quotation->status === 'sent') {
                try {
                    $quotation->load(['customer', 'items.product', 'items.productVariant']);
                    if ($quotation->customer?->email) {
                        Mail::to($quotation->customer->email)->send(new QuotationSentMail($quotation));
                    }
                } catch (\Exception $mailEx) {
                    \Log::warning('Quotation email failed: ' . $mailEx->getMessage());
                }
            }
            return redirect()->route('quotations.index')->with('success', 'Quotation created successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create quotation: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation){
        // Load relationships for display
        $title = "Quotation";
        $quotation->load([
            'customer',
            'warehouse',
            'items' => function ($query) {
                $query->select(
                    'quotation_items.*', // Select all columns from the quotation_items table
                    // COALESCE selects the first non-null expression.
                    // If product_variant_id is present, it will try to get the product name via the variant.
                    // If product_variant_id is null, it will get the product name directly from the simple product.
                    DB::raw('COALESCE(p_variant.name, p_simple.name) as product_display_name'),
                    'pv.variant_name as variant_display_name'
                )
                // Left join for simple products (where quotation_items.product_id is not null)
                ->leftJoin('products as p_simple', 'quotation_items.product_id', '=', 'p_simple.id')
                // Left join for product variants (where quotation_items.product_variant_id is not null)
                ->leftJoin('product_variants as pv', 'quotation_items.product_variant_id', '=', 'pv.id')
                // Left join to get the product details for the product variant
                ->leftJoin('products as p_variant', 'pv.product_id', '=', 'p_variant.id');
            }
        ]);

        return view('admin.quotations.show', compact('quotation', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Quotation $quotation)
    {
        $title = "Edit Sale Quotation"; // Changed title for clarity
        $categories = Category::all();
        $branches = Branch::all();
        $customers = Customer::all();
        $warehouses = Warehouse::all();
        $units = Unit::all();
        $setting = Setting::first();

        $search = $request->input('search');

        // Resolve warehouse from the quotation's branch (same logic as create/sale)
        $warehouseId = null;
        $branchId    = $request->input('branch_id', $quotation->branch_id);
        if ($branchId) {
            $branch      = Branch::find($branchId);
            $warehouseId = $branch?->warehouse_id;
        }

        $products = collect();

        $buildQuery = function ($base) use ($search, $warehouseId) {
            if ($search) {
                $base->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            } else {
                $base->latest()->take(10);
            }

            if ($warehouseId) {
                $base->where(function ($q) use ($warehouseId) {
                    $q->whereHas('inventoryStocks', fn($sq) =>
                        $sq->where('warehouse_id', $warehouseId)->where('quantity_in_base_unit', '>', 0)
                    )->orWhereHas('variants.inventoryStocks', fn($sq) =>
                        $sq->where('warehouse_id', $warehouseId)->where('quantity_in_base_unit', '>', 0)
                    );
                });
                return $base->with([
                    'baseUnit',
                    'variants.product.baseUnit',
                    'variants.inventoryStocks' => fn($q) => $q->where('warehouse_id', $warehouseId),
                    'inventoryStocks'          => fn($q) => $q->where('warehouse_id', $warehouseId),
                ]);
            }

            return $base->with(['baseUnit', 'variants.inventoryStocks', 'variants.product.baseUnit', 'inventoryStocks']);
        };

        $products = $buildQuery(Product::whereNull('deleted_at'))
            ->get()
            ->map(function ($product) {
                $conversionFactor        = $product->baseUnit->conversion_factor ?? 1;
                $baseQuantity            = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                $product->stock_quantity = $baseQuantity / $conversionFactor;
                $product->in_stock       = $product->stock_quantity > 0;

                foreach ($product->variants as $variant) {
                    $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
                    $variantQuantity         = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                    $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
                    $variant->in_stock       = $variant->stock_quantity > 0;
                }
                return $product;
            });

        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                $isOutOfStock = !$product->in_stock && $product->variants->count() === 0;
                $productImgSrc = !empty($product->product_img) ? asset('storage/' . $product->product_img) : 'https://placehold.co/100x100/f0f0f0/808080?text=N/A';

                $html .= '<div class="col-md-3 mb-2 product-item d-flex">';
                $html .= '<div class="card p-2 text-center h-100 d-flex flex-column justify-content-between w-100 ' . ($isOutOfStock ? 'bg-light text-muted pointer-events-none opacity-50' : '') . '">';

                if (!empty($product->product_img)) {
                    $html .= '<img src="' . asset('storage/' . $product->product_img) . '" alt="Product Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin: auto;">';
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
                        $html .= '<option ' . $disabled . ' value="variant-' . $variant->id . '" ' .
                                 'data-name="' . htmlspecialchars($product->name . ' - ' . $variant->variant_name) . '" ' .
                                 'data-price="' . $variant->actual_price . '" ' .
                                 'data-stock="' . $variant->stock_quantity . '" ' .
                                 'data-unit-id="' . $product->default_display_unit_id . '">' .
                                 htmlspecialchars($variant->variant_name) . ' - ' . $setting->currency_symbol . ' ' . number_format($variant->actual_price, 2) . ' ' . $stockText .
                                 '</option>';
                    }
                    $html .= '</select>';
                    $html .= '<button class="btn btn-sm btn-success w-100 add-variant-to-cart mb-2" disabled>Add to Cart</button>';
                } else {
                    $html .= '<p class="mb-1">' . $setting->currency_symbol . ' ' . number_format($product->actual_price, 2) .
                             '<br><small>(Stock: ' . $product->stock_quantity . ')</small></p>';
                    if ($product->in_stock) {
                        $html .= '<button ' .
                                 'class="btn btn-sm btn-success w-100 mt-auto add-simple-to-cart" ' .
                                 'data-id="product-' . $product->id . '" ' .
                                 'data-name="' . htmlspecialchars($product->name) . '" ' .
                                 'data-price="' . $product->actual_price . '" ' .
                                 'data-stock="' . $product->stock_quantity . '" ' .
                                 'data-unit-id="' . $product->default_display_unit_id . '">' .
                                 'Add to Cart' .
                                 '</button>';
                    } else {
                        $html .= '<button class="btn btn-sm btn-secondary w-100 mt-auto" disabled>Out of Stock</button>';
                    }
                }
                $html .= '</div>';
                $html .= '</div>';
            }
            return $html;
        }

        // Prepare initial cart data from the existing quotation
        $initialCart = [];
        foreach ($quotation->items as $item) {
            $productId = $item->product_id;
            $variantId = $item->product_variant_id;
            $type = $variantId ? 'variant' : 'product';
            $id = $type . '-' . ($variantId ?? $productId);

            $product = Product::find($productId);
            $stock = 0;
            $name = '';
            $salePrice = $item->unit_price; // Use the unit price from the quotation item

            if ($variantId) {
                $variant = $product->variants->where('id', $variantId)->first();
                if ($variant) {
                    $stock = $variant->stock_quantity; // Assuming stock is calculated on variant model
                    $name = $product->name . ' - ' . $variant->variant_name;
                }
            } else {
                $stock = $product->stock_quantity; // Assuming stock is calculated on product model
                $name = $product->name;
            }

            $initialCart[$id] = [
                'name' => $name,
                'actual_price' => $salePrice,
                'unit_id' => $item->unit_id,
                'qty' => $item->quantity,
                'stock' => $stock,
            ];
        }

        // Pass the quotation object and initial cart data to the view
        return view('admin.quotations.create', compact(
            'categories',
            'units',
            'products',
            'title',
            'customers',
            'branches',
            'warehouses',
            'setting',
            'quotation', // Pass the existing quotation object
            'initialCart' // Pass the prepared cart data for edit
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        // Decode the cart_data JSON string into an array
        $cartData = json_decode($request->input('cart_data'), true);

        // Map cartData to an 'items' structure that aligns with your validation rules
        $mappedItems = [];
        if (is_array($cartData)) {
            foreach ($cartData as $id => $item) {
                // Determine if it's a simple product or a variant
                $isVariant = str_starts_with($id, 'variant-');
                $productId = null;
                $productVariantId = null;

                if ($isVariant) {
                    $productVariantId = $item['id']; // This is the variant ID
                    // You might need to fetch the product_id from the variant
                    $variant = ProductVariant::find($productVariantId);
                    if ($variant) {
                        $productId = $variant->product_id;
                    }
                } else {
                    $productId = $item['id']; // This is the simple product ID
                    $productVariantId = null; // No variant for simple products
                }

                $mappedItems[] = [
                    'product_id' => $productId,
                    'product_variant_id' => $productVariantId, // Can be null for simple products
                    'unit_price' => $item['actual_price'],
                    'quantity' => $item['qty'],
                    'discount_amount' => 0, // Or retrieve if you add per-item discount logic
                    'tax_amount' => 0,      // Or retrieve if you add per-item tax logic
                ];
            }
        }

        // Add the mapped items back to the request for validation
        $request->merge(['items' => $mappedItems]);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'nullable|exists:branches,id',
            'quotation_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'total_payable' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,sent',
            'note' => 'nullable|string',
            'cart_data' => 'required|json', // Validate the raw JSON string
            'items' => 'required|array|min:1', // Validate the processed items array
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Use the calculated totals directly from the view
            $subtotal = $request->input('subtotal');
            $discount = $request->input('discount') ?? 0;
            $tax = $request->input('tax') ?? 0;
            $taxPercentage = $request->input('taxrate') ?? 0;
            $shipping = $request->input('shipping') ?? 0;
            $finalGrandTotal = $request->input('total_payable');

            // Update the existing quotation
            $quotation->update([
                'customer_id' => $request->customer_id,
                'branch_id' => $request->branch_id,
                'quotation_date' => $request->quotation_date,
                'order_tax_amount' => $tax,
                'tax_percentage' => $taxPercentage,
                'discount_percentage' => $discount,
                'discount_type' => $request->input('discount_type', 'fixed'),
                'shipping_cost' => $shipping,
                'grand_total' => $finalGrandTotal,
                'status' => $request->status,
                'note' => $request->note,
            ]);

            // Delete existing quotation items to replace them with the new ones
            // Assuming the relationship method on Quotation model is 'quotationItems()'
            $quotation->items()->delete();

            // Create new quotation items
            foreach ($mappedItems as $itemData) {
                $itemSubtotal = $itemData['unit_price'] * $itemData['quantity'];

                $quotation->items()->create([ // Use the correct relationship name
                    'quotation_id' => $quotation->id, // Ensure it's explicitly linked
                    'product_id' => $itemData['product_id'],
                    'product_variant_id' => $itemData['product_variant_id'],
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'discount_amount' => $itemData['discount_amount'] ?? 0,
                    'tax_amount' => $itemData['tax_amount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            DB::commit();
            if ($quotation->status === 'sent') {
                try {
                    $quotation->load(['customer', 'items.product', 'items.productVariant']);
                    if ($quotation->customer?->email) {
                        Mail::to($quotation->customer->email)->send(new QuotationSentMail($quotation));
                    }
                } catch (\Exception $mailEx) {
                    \Log::warning('Quotation email failed: ' . $mailEx->getMessage());
                }
            }
            return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update quotation: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        DB::beginTransaction();
        try {
            // Soft delete the quotation and its items automatically via cascade on 'quotation_id' in items
            // However, since items also have soft deletes, explicitly deleting items ensures their deleted_at is set.
            $quotation->items()->delete(); // Soft delete all associated items
            $quotation->delete(); // Soft delete the quotation itself

            DB::commit();
            return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete quotation: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted quotation.
     * This is a custom method, not part of standard resource controller.
     */
    // public function restore($id)
    // {
    //     $quotation = Quotation::onlyTrashed()->find($id);

    //     if (!$quotation) {
    //         return back()->with('error', 'Quotation not found or not soft-deleted.');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $quotation->restore(); // Restore the quotation
    //         $quotation->items()->onlyTrashed()->restore(); // Restore associated items

    //         DB::commit();
    //         return redirect()->route('quotations.index')->with('success', 'Quotation restored successfully!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Failed to restore quotation: ' . $e->getMessage());
    //     }
    // }

    /**
     * Permanently delete a quotation (force delete).
     * This is a custom method, use with caution.
     */
    // public function forceDelete($id)
    // {
    //     $quotation = Quotation::onlyTrashed()->find($id);

    //     if (!$quotation) {
    //         return back()->with('error', 'Quotation not found or not soft-deleted.');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $quotation->items()->onlyTrashed()->forceDelete(); // Force delete associated items
    //         $quotation->forceDelete(); // Force delete the quotation

    //         DB::commit();
    //         return redirect()->route('quotations.index')->with('success', 'Quotation permanently deleted!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Failed to permanently delete quotation: ' . $e->getMessage());
    //     }
    // }

    public function convertToSale(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'This quotation has already been converted to a sale.');
        }

        $quotation->load(['items.product.baseUnit', 'items.productVariant']);

        $branch      = Branch::find($quotation->branch_id);
        $warehouseId = $branch->warehouse_id ?? null;

        if (!$warehouseId) {
            return back()->with('error', 'The quotation branch does not have an associated warehouse.');
        }

        DB::beginTransaction();
        try {
            // PRE-FLIGHT STOCK CHECK — abort conversion if any item is short
            foreach ($quotation->items as $item) {
                $productId = $item->product_id;
                $variantId = $item->product_variant_id;

                // Use product base unit conversion to compute base qty
                $conversionFactor = $item->product->baseUnit->conversion_factor ?? 1;
                $baseQtyNeeded    = $item->quantity * $conversionFactor;

                $available = InventoryStock::where('product_id', $productId)
                    ->where('variant_id', $variantId)
                    ->where('warehouse_id', $warehouseId)
                    ->value('quantity_in_base_unit') ?? 0;

                if ($available < $baseQtyNeeded) {
                    throw new \Exception(
                        'Insufficient stock for "' . ($item->product->name ?? 'product') .
                        '". Available: ' . (int)$available . ', Requested: ' . (int)$baseQtyNeeded . '.'
                    );
                }
            }

            // Generate invoice number
            $year       = date('Y');
            $lastSale   = Sale::whereYear('created_at', $year)
                ->where('invoice_number', 'like', "{$year}-invoice-%")
                ->orderBy('id', 'desc')->first();
            $nextNumber = 1;
            if ($lastSale && preg_match("/{$year}-invoice-(\d+)/", $lastSale->invoice_number, $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            }
            $invoiceNo = "{$year}-invoice-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'customer_id'     => $quotation->customer_id,
                'branch_id'       => $quotation->branch_id,
                'invoice_number'  => $invoiceNo,
                'sale_date'       => now(),
                'total_amount'    => $quotation->grand_total,
                'discount_amount' => 0,
                'tax_amount'      => $quotation->items->sum('tax_amount'),
                'final_amount'    => $quotation->grand_total,
                'shipping'        => $quotation->shipping_cost,
                'paid_amount'     => 0,
                'due_amount'      => $quotation->grand_total,
                'sale_origin'     => 'Quotation',
                'status'          => 'pending',
                'created_by'      => auth()->id(),
            ]);

            foreach ($quotation->items as $item) {
                $productId        = $item->product_id;
                $variantId        = $item->product_variant_id;
                $conversionFactor = $item->product->baseUnit->conversion_factor ?? 1;
                $baseQtyNeeded    = $item->quantity * $conversionFactor;

                SaleItem::create([
                    'sale_id'               => $sale->id,
                    'product_id'            => $productId,
                    'variant_id'            => $variantId,
                    'unit_id'               => null,
                    'quantity'              => $item->quantity,
                    'unit_price'            => $item->unit_price,
                    'total_price'           => $item->subtotal,
                    'quantity_in_base_unit' => $baseQtyNeeded,
                    'discount'              => $item->discount_amount,
                    'tax'                   => $item->tax_amount,
                ]);

                $stock = InventoryStock::where([
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'warehouse_id' => $warehouseId,
                ])->first();

                $stock->decrement('quantity_in_base_unit', $baseQtyNeeded);

                StockLedger::create([
                    'product_id'                   => $productId,
                    'variant_id'                   => $variantId,
                    'warehouse_id'                 => $warehouseId,
                    'ref_type'                     => 'sale',
                    'ref_id'                       => $sale->id,
                    'quantity_change_in_base_unit' => $baseQtyNeeded,
                    'unit_cost'                    => $item->unit_price,
                    'direction'                    => 'out',
                    'created_by'                   => auth()->id(),
                ]);
            }

            // Mark quotation as converted
            $quotation->update(['status' => 'converted']);

            DB::commit();
            return redirect()->route('sales.invoice', $sale->id)
                             ->with('success', 'Quotation converted to sale #' . $invoiceNo . ' successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to convert quotation: ' . $e->getMessage());
        }
    }

    public function downloadPdf(Quotation $quotation)
    {
        $quotation->load(['customer', 'branch', 'items.product', 'items.productVariant']);
        $setting = \App\Models\Setting::first();
        $currencySymbol = $setting->currency_symbol ?? '$';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.quotation', compact('quotation', 'setting', 'currencySymbol'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
    }
}