<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockLedger;
use App\Models\Supplier;
use App\Models\Category;

class StockAdjustmentController extends Controller
{
    public function stockIndex(Request $request)
    {
        $title = 'Stock Inventory';
        $categories = Category::all();

        $query = Product::with([
            'category',
            'baseUnit',
            'variants.inventoryStock',
            'variants.displayUnit',
            'inventoryStock',
            'branch'
        ]);

        // Always ensure products have an inventory stock record (either directly or via variants)
        // This is the core change to only show products "inside inventory"
        $query->where(function ($q) {
            $q->whereHas('inventoryStock') // Product itself has an inventory record
              ->orWhereHas('variants.inventoryStock'); // Or at least one of its variants has an inventory record
        });


        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }

        if ($request->filled('search_sku')) {
            // Adjust this if SKU is only on variants or both
            $query->where('sku', 'like', '%' . $request->search_sku . '%');
        }

        if ($request->filled('search_category')) {
            $query->where('category_id', $request->search_category);
        }

        if ($request->filled('search_status')) {
            if ($request->search_status == 'low_stock') {
                $query->where(function ($q) {
                    // Condition for products without variants (only if they have inventoryStock)
                    $q->where(function ($subQuery) {
                        $subQuery->whereDoesntHave('variants')
                                 ->whereHas('inventoryStock', function ($invStockQuery) {
                                     $invStockQuery->whereColumn('quantity_in_base_unit', '<=', 'products.low_stock');
                                 });
                    });

                    // Condition for products with variants (only if variants have inventoryStock)
                    $q->orWhere(function ($subQuery) {
                        $subQuery->whereHas('variants', function ($variantQuery) {
                            $variantQuery->whereHas('inventoryStock', function ($invStockQuery) {
                                $invStockQuery->whereColumn('quantity_in_base_unit', '<=', 'product_variants.low_stock');
                            });
                        });
                    });
                });
            } elseif ($request->search_status === 'ok') {
                $query->where(function ($q) {
                    // Condition for products without variants (only if they have inventoryStock)
                    $q->where(function ($subQuery) {
                        $subQuery->whereDoesntHave('variants')
                                 ->whereHas('inventoryStock', function ($invStockQuery) {
                                     $invStockQuery->whereColumn('quantity_in_base_unit', '>', 'products.low_stock');
                                 });
                    });

                    // Condition for products with variants (only if variants have inventoryStock)
                    $q->orWhere(function ($subQuery) {
                        $subQuery->whereHas('variants', function ($variantQuery) {
                            $variantQuery->whereHas('inventoryStock', function ($invStockQuery) {
                                $invStockQuery->whereColumn('quantity_in_base_unit', '>', 'product_variants.low_stock');
                            });
                        });
                    });
                });
            }
        }

        $products = $query->paginate(15);

        return view('admin.stock.inventory', compact('products', 'categories', 'title'));
    }

    public function stockLedger(Request $request){
        $title = 'Stock Ledger';

        // Get filters from request
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $product = $request->get('product');
        $name = $request->get('name');
        $refType = $request->get('ref_type');
        $warehouse = $request->get('warehouse');

        // Check if any filters are applied
        $isFiltered = $dateFrom || $dateTo || $product || $name || $refType || $warehouse;

        // Start query
        $ledgersQuery = StockLedger::with([
            'product.baseUnit',
            'product.displayUnit',
            'variant.baseUnit',
            'variant.inventoryStock',
            'variant.displayUnit',
            'warehouse'
        ]);

        // Apply filters only if any are set
        if ($isFiltered) {
            $ledgersQuery
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    $query->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    $query->whereDate('created_at', '<=', $dateTo);
                })
                ->when($product, function ($query) use ($product) {
                    $query->whereHas('product', function ($subQuery) use ($product) {
                        $subQuery->where('name', 'like', "%{$product}%");
                    });
                })
                ->when($name, function ($query) use ($name) {
                    $query->whereHas('variant', function ($subQuery) use ($name) {
                        $subQuery->where('variant_name', 'like', "%{$name}%");
                    });
                })
                ->when($refType, function ($query) use ($refType) {
                    $query->where('ref_type', 'like', "%{$refType}%");
                })
                ->when($warehouse, function ($query) use ($warehouse) {
                    $query->whereHas('warehouse', function ($subQuery) use ($warehouse) {
                        $subQuery->where('name', 'like', "%{$warehouse}%");
                    });
                });
        }

        // Final result with pagination
        $ledgers = $ledgersQuery
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->only(['date_from', 'date_to', 'product', 'name', 'ref_type', 'warehouse']));

        // Convert qty and attach unit
        foreach ($ledgers as $ledger) {
            $conversionFactor = 1;
            $unitName = '';

            if ($ledger->variant && $ledger->variant->baseUnit) {
                $conversionFactor = $ledger->variant->baseUnit->conversion_factor ?? 1;
                $unitName = $ledger->variant->baseUnit->name ?? '';
            } elseif ($ledger->product && $ledger->product->baseUnit) {
                $conversionFactor = $ledger->product->baseUnit->conversion_factor ?? 1;
                $unitName = $ledger->product->baseUnit->name ?? '';
            }

            $ledger->converted_qty = $ledger->quantity_change_in_base_unit / $conversionFactor;
            $ledger->unit_name = $unitName;
        }

        return view('admin.stock.stock_ledger', compact('ledgers', 'title'));
    }

    public function supplierProductReport(Request $request){
        $title = "Products Supplier";
        // Fetch all suppliers to populate the dropdown filter
        $suppliers = Supplier::all();

        // Get the selected supplier ID and filter type from the request
        $selectedSupplier = $request->supplier_id;
        $filter = $request->filter;

        // Initialize products as an empty collection; it will be populated if a supplier is selected
        $products = collect();

        // Only proceed with fetching products if a supplier has been selected
        if ($selectedSupplier) {
            // Start a query on the Product model
            $productsQuery = Product::whereHas('suppliers', function ($q) use ($selectedSupplier) {
                // Filter products that are associated with the selected supplier
                $q->where('suppliers.id', $selectedSupplier);
            });

            // Define the eager loads. The 'variants' relationship eager load is conditional.
            $eagerLoads = [
                'inventoryStock', // For main product's stock
                'baseUnit',       // For conversion factor to base unit
                'variants.inventoryStock', // For variant's stock (will be loaded for all variants initially)
                'variants.displayUnit'     // For variant's display unit (if used in blade)
            ];

            // If low stock filter is active, add a constraint to eager load only low stock variants
            if ($filter === 'low_stock') {
                $eagerLoads['variants'] = function ($query) {
                    $query->where(function ($variantQuery) {
                        $variantQuery->whereHas('inventoryStock', function ($invStockQuery) {
                            $invStockQuery->whereColumn('quantity_in_base_unit', '<=', 'product_variants.low_stock');
                        })
                        ->orWhereDoesntHave('inventoryStock'); // Include variants with no inventory record
                    });
                };

                // Apply the low stock filter to the main product query as well
                $productsQuery->where(function ($query) {
                    // Condition 1: Products without variants that are low stock or have no stock record
                    $query->where(function ($subQuery) {
                        $subQuery->whereDoesntHave('variants') // Ensures we are looking at non-variant products
                                 ->where(function ($noVariantStockQuery) {
                                     $noVariantStockQuery->whereHas('inventoryStock', function ($invStockQuery) {
                                         // Compare the quantity in base unit with the product's low_stock threshold
                                         $invStockQuery->whereColumn('quantity_in_base_unit', '<=', 'products.low_stock');
                                     })
                                     ->orWhereDoesntHave('inventoryStock'); // Include products with no inventory record (effectively zero stock)
                                 });
                    });

                    // Condition 2: Products that have variants, and at least one of their variants is low stock or has no stock record
                    $query->orWhere(function ($subQuery) {
                        $subQuery->whereHas('variants', function ($variantQuery) {
                            $variantQuery->where(function ($variantStockCheckQuery) {
                                $variantStockCheckQuery->whereHas('inventoryStock', function ($invStockQuery) {
                                    // Compare the quantity in base unit with the variant's low_stock threshold
                                    $invStockQuery->whereColumn('quantity_in_base_unit', '<=', 'product_variants.low_stock');
                                })
                                ->orWhereDoesntHave('inventoryStock'); // Include variants with no inventory record
                            });
                        });
                    });
                });
            }

            // Apply the eager loads to the query
            $productsQuery->with($eagerLoads);

            // Execute the query and get the results
            $products = $productsQuery->get();
        }

        // Return the view with the necessary data
        return view('admin.stock.supplier_products', compact(
            'suppliers', 'products', 'selectedSupplier', 'filter', 'title'
        ));
    }
}
