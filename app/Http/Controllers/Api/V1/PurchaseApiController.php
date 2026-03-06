<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\InventoryStock;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseApiController extends Controller
{
    use ApiResponse;
    /**
     * POST /api/v1/purchases
     * Create a purchase order from the mobile app.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'   => 'required|exists:suppliers,id',
            'warehouse_id'  => 'required|exists:warehouses,id',
            'items'         => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.variant_id'  => 'nullable|exists:product_variants,id',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_cost'   => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_cost']);
            $invoiceNo   = 'PUR-' . strtoupper(substr(uniqid(), -6));

            $purchase = Purchase::create([
                'supplier_id'    => $request->supplier_id,
                'warehouse_id'   => $request->warehouse_id,
                'invoice_number' => $invoiceNo,
                'purchase_date'  => now(),
                'total_amount'   => $totalAmount,
                'paid_amount'    => 0,
                'due_amount'     => $totalAmount,
                'notes'          => $request->notes,
                'created_by'     => $request->user()->id,
            ]);

            foreach ($request->items as $item) {
                // Add stock
                InventoryStock::firstOrCreate(
                    ['product_id' => $item['product_id'], 'variant_id' => $item['variant_id'] ?? null, 'warehouse_id' => $request->warehouse_id],
                    ['quantity_in_base_unit' => 0]
                )->increment('quantity_in_base_unit', $item['quantity']);

                StockLedger::create([
                    'product_id'                   => $item['product_id'],
                    'variant_id'                   => $item['variant_id'] ?? null,
                    'warehouse_id'                 => $request->warehouse_id,
                    'ref_type'                     => 'purchase',
                    'ref_id'                       => $purchase->id,
                    'quantity_change_in_base_unit' => $item['quantity'],
                    'unit_cost'                    => $item['unit_cost'],
                    'direction'                    => 'in',
                    'created_by'                   => $request->user()->id,
                ]);
            }

            return $this->success([
                'purchase_id'    => $purchase->id,
                'invoice_number' => $invoiceNo,
                'total_amount'   => $totalAmount,
            ], 'Purchase order created successfully.', 201);
        });
    }
}
