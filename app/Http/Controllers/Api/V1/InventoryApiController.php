<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Models\InventoryStock;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    use ApiResponse;
    /**
     * GET /api/v1/inventory/stock
     * Current stock levels per product/warehouse.
     */
    public function stock(Request $request)
    {
        $query = InventoryStock::with([
            'product:id,name,sku,barcode,actual_price',
            'variant:id,variant_name',
            'warehouse:id,name',
        ])->whereHas('product', fn($q) => $q->whereNull('deleted_at'))
          ->where('quantity_in_base_unit', '>', 0);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $stocks = $query->paginate($request->input('per_page', 50));

        $stocks->getCollection()->transform(fn($s) => [
            'product'   => $s->product?->name,
            'sku'       => $s->product?->sku,
            'variant'   => $s->variant?->variant_name,
            'warehouse' => $s->warehouse?->name,
            'quantity'  => (float) $s->quantity_in_base_unit,
            'unit_cost' => (float) $s->product?->actual_price,
            'value'     => round($s->quantity_in_base_unit * $s->product?->actual_price, 2),
        ]);

        return $this->paginated($stocks);
    }
}
