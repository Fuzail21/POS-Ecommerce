<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryStock;
use App\Models\StockLedger;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleApiController extends Controller
{
    use ApiResponse;
    /**
     * POST /api/v1/sales
     * Create a POS sale from the mobile app.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'    => 'nullable|exists:customers,id',
            'branch_id'      => 'required|exists:branches,id',
            'payment_method' => 'required|string|max:50',
            'items'          => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.variant_id'  => 'nullable|exists:product_variants,id',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $invoiceNo   = 'POS-' . strtoupper(substr(uniqid(), -6));

            $sale = Sale::create([
                'customer_id'    => $request->customer_id,
                'branch_id'      => $request->branch_id,
                'invoice_number' => $invoiceNo,
                'sale_date'      => now(),
                'total_amount'   => $totalAmount,
                'discount_amount' => 0,
                'tax_amount'     => 0,
                'shipping'       => 0,
                'final_amount'   => $totalAmount,
                'paid_amount'    => $totalAmount,
                'due_amount'     => 0,
                'payment_method' => $request->payment_method,
                'sale_origin'    => 'POS',
                'status'         => 'completed',
                'created_by'     => $request->user()->id,
            ]);

            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Deduct stock
                InventoryStock::where('product_id', $item['product_id'])
                    ->where('variant_id', $item['variant_id'] ?? null)
                    ->decrement('quantity_in_base_unit', $item['quantity']);
            }

            return $this->success([
                'sale_id'        => $sale->id,
                'invoice_number' => $invoiceNo,
                'total_amount'   => $totalAmount,
            ], 'Sale created successfully.', 201);
        });
    }
}
