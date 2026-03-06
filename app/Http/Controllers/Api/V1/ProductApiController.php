<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    use ApiResponse;
    /**
     * GET /api/v1/products
     * Product list with stock and category.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category:id,name', 'baseUnit:id,name', 'inventoryStocks:id,product_id,quantity_in_base_unit'])
            ->whereNull('deleted_at');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%$q%")
                   ->orWhere('sku', 'like', "%$q%")
                   ->orWhere('barcode', $q);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate($request->input('per_page', 20));

        $products->getCollection()->transform(function ($p) {
            return [
                'id'           => $p->id,
                'name'         => $p->name,
                'sku'          => $p->sku,
                'barcode'      => $p->barcode,
                'category'     => $p->category?->name,
                'price'        => (float) $p->actual_price,
                'unit'         => $p->baseUnit?->name,
                'has_variants' => (bool) $p->has_variants,
                'total_stock'  => (float) $p->inventoryStocks->sum('quantity_in_base_unit'),
                'is_low_stock' => $p->is_low_stock,
            ];
        });

        return $this->paginated($products);
    }

    /**
     * GET /api/v1/products/{id}
     * Product detail with variants and per-warehouse stock.
     */
    public function show($id)
    {
        $product = Product::with([
            'category:id,name',
            'baseUnit:id,name',
            'variants:id,product_id,variant_name,actual_price',
            'inventoryStocks.warehouse:id,name',
        ])->whereNull('deleted_at')->findOrFail($id);

        return $this->success([
            'id'           => $product->id,
            'name'         => $product->name,
            'sku'          => $product->sku,
            'barcode'      => $product->barcode,
            'brand'        => $product->brand,
            'category'     => $product->category?->name,
            'price'        => (float) $product->actual_price,
            'unit'         => $product->baseUnit?->name,
            'has_variants' => (bool) $product->has_variants,
            'variants'     => $product->variants->map(fn($v) => [
                'id'    => $v->id,
                'name'  => $v->variant_name,
                'price' => (float) $v->actual_price,
            ]),
            'stock_by_warehouse' => $product->inventoryStocks->map(fn($s) => [
                'warehouse' => $s->warehouse?->name,
                'quantity'  => (float) $s->quantity_in_base_unit,
            ]),
            'total_stock'  => (float) $product->inventoryStocks->sum('quantity_in_base_unit'),
        ]);
    }
}
