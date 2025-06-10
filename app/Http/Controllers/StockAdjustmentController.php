<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockLedger;
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

    if ($request->filled('search_name')) {
        $query->where('name', 'like', '%'.$request->search_name.'%');
    }

    if ($request->filled('search_sku')) {
        $query->where('sku', 'like', '%'.$request->search_sku.'%');
    }

    if ($request->filled('search_category')) {
        $query->where('category_id', $request->search_category);
    }

    if ($request->filled('search_status')) {
        if ($request->search_status == 'low_stock') {
            $query->where(function ($query) {
                $query->whereHas('inventoryStock', function ($q) {
                    $q->where('quantity_in_base_unit', '<=', 5);
                })
                ->orWhereDoesntHave('inventoryStock')
                ->orWhereHas('variants.inventoryStock', function ($q) {
                    $q->where('quantity_in_base_unit', '<=', 5);
                })
                ->orWhereDoesntHave('variants.inventoryStock');
            });
        } elseif ($request->search_status === 'ok') {
            $query->where(function ($query) {
                $query->whereHas('inventoryStock', function ($q) {
                    $q->where('quantity_in_base_unit', '>', 5);
                })
                ->orWhereHas('variants.inventoryStock', function ($q) {
                    $q->where('quantity_in_base_unit', '>', 5);
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
}
