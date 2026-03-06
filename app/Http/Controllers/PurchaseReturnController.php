<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\StockLedger;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        $title = 'Purchase Returns';

        $returns = PurchaseReturn::with(['purchase', 'supplier', 'warehouse', 'createdBy'])
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('admin.purchase.returns-list', compact('returns', 'title'));
    }

    public function create(Purchase $purchase)
    {
        $title = 'Create Purchase Return';

        $purchase->load([
            'items.product.baseUnit',
            'items.variant',
            'items.unit',
            'supplier',
            'warehouse',
        ]);

        // Calculate already-returned quantities per item
        $alreadyReturnedMap = [];
        foreach ($purchase->items as $item) {
            $key = $item->product_id . '_' . ($item->variant_id ?? 0);
            $alreadyReturned = PurchaseReturnItem::where('purchase_id', $purchase->id)
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->sum('quantity');
            $alreadyReturnedMap[$key] = (float) $alreadyReturned;
        }

        return view('admin.purchase.return-create', compact('purchase', 'alreadyReturnedMap', 'title'));
    }

    public function store(Request $request, Purchase $purchase)
    {
        $request->validate([
            'return_items'   => 'required|array|min:1',
            'return_items.*.quantity' => 'required|numeric|min:0',
            'return_reason'  => 'nullable|string|max:1000',
            'refund_amount'  => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $warehouse_id = $purchase->warehouse_id;
        if (!$warehouse_id) {
            return back()->withInput()->with('error', 'The purchase has no associated warehouse.');
        }

        // Filter items where quantity > 0
        $returnItems = array_filter($request->return_items, fn($item) => ($item['quantity'] ?? 0) > 0);

        if (empty($returnItems)) {
            return back()->withInput()->with('error', 'Please enter a return quantity for at least one item.');
        }

        DB::beginTransaction();
        try {
            $totalReturnAmount = 0;

            // Validate each item's return qty
            foreach ($returnItems as $purchaseItemId => $itemData) {
                $purchaseItem = PurchaseItem::where('id', $purchaseItemId)
                    ->where('purchase_id', $purchase->id)
                    ->firstOrFail();

                $alreadyReturned = PurchaseReturnItem::where('purchase_id', $purchase->id)
                    ->where('product_id', $purchaseItem->product_id)
                    ->where('variant_id', $purchaseItem->variant_id)
                    ->sum('quantity');

                $requestedQty = (float) ($itemData['quantity'] ?? 0);

                if (($alreadyReturned + $requestedQty) > $purchaseItem->quantity) {
                    throw new \Exception(
                        'Return qty for "' . ($purchaseItem->product->name ?? 'item') .
                        '" (' . $requestedQty . ') exceeds returnable qty. ' .
                        'Original: ' . $purchaseItem->quantity . ', Already returned: ' . $alreadyReturned . '.'
                    );
                }

                $totalReturnAmount += $requestedQty * ($purchaseItem->unit_cost ?? 0);
            }

            // Create PurchaseReturn record
            $purchaseReturn = PurchaseReturn::create([
                'purchase_id'         => $purchase->id,
                'supplier_id'         => $purchase->supplier_id,
                'warehouse_id'        => $warehouse_id,
                'return_date'         => now(),
                'return_reason'       => $request->return_reason,
                'total_return_amount' => $totalReturnAmount,
                'refund_amount'       => $request->refund_amount,
                'payment_method'      => $request->payment_method,
                'created_by'          => auth()->id(),
            ]);

            foreach ($returnItems as $purchaseItemId => $itemData) {
                $purchaseItem = PurchaseItem::find($purchaseItemId);
                $requestedQty = (float) ($itemData['quantity'] ?? 0);

                $unit    = $purchaseItem->unit;
                $baseQty = $requestedQty * ($unit->conversion_factor ?? 1);

                PurchaseReturnItem::create([
                    'purchase_return_id'    => $purchaseReturn->id,
                    'purchase_id'           => $purchase->id,
                    'product_id'            => $purchaseItem->product_id,
                    'variant_id'            => $purchaseItem->variant_id,
                    'unit_id'               => $purchaseItem->unit_id,
                    'quantity'              => $requestedQty,
                    'quantity_in_base_unit' => $baseQty,
                    'unit_cost'             => $purchaseItem->unit_cost,
                    'total_cost'            => $requestedQty * ($purchaseItem->unit_cost ?? 0),
                ]);

                // Decrement inventory stock (items go back to supplier)
                $stock = InventoryStock::firstOrCreate(
                    [
                        'product_id'   => $purchaseItem->product_id,
                        'variant_id'   => $purchaseItem->variant_id,
                        'warehouse_id' => $warehouse_id,
                    ],
                    ['quantity_in_base_unit' => 0]
                );

                if ($stock->quantity_in_base_unit < $baseQty) {
                    throw new \Exception(
                        'Insufficient stock to return for "' . ($purchaseItem->product->name ?? 'item') .
                        '". Available: ' . $stock->quantity_in_base_unit . ', Requested: ' . $baseQty . '.'
                    );
                }

                $stock->decrement('quantity_in_base_unit', $baseQty);

                // Stock ledger: OUT (items leaving warehouse back to supplier)
                StockLedger::create([
                    'product_id'                   => $purchaseItem->product_id,
                    'variant_id'                   => $purchaseItem->variant_id,
                    'warehouse_id'                 => $warehouse_id,
                    'ref_type'                     => 'purchase_return',
                    'ref_id'                       => $purchaseReturn->id,
                    'quantity_change_in_base_unit' => $baseQty,
                    'unit_cost'                    => $purchaseItem->unit_cost,
                    'direction'                    => 'out',
                    'created_by'                   => auth()->id(),
                ]);
            }

            // Adjust the purchase due amount (supplier owes us a credit)
            $refundAmount = (float) $request->refund_amount;
            if ($refundAmount > 0 && $purchase->due_amount > 0) {
                $reduction = min($refundAmount, $purchase->due_amount);
                $purchase->decrement('due_amount', $reduction);
                $purchase->decrement('total_amount', $reduction);
            }

            DB::commit();

            return redirect()->route('purchase_returns.index')
                ->with('success', 'Purchase return processed successfully! ' . number_format($totalReturnAmount, 2) . ' worth of goods returned.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Return Error: ' . $e->getMessage(), [
                'trace'        => $e->getTraceAsString(),
                'purchase_id'  => $purchase->id,
                'request_data' => $request->all(),
            ]);
            return back()->withInput()->with('error', 'Failed to process return: ' . $e->getMessage());
        }
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load([
            'items.product',
            'items.variant',
            'items.unit',
            'purchase',
            'supplier',
            'warehouse',
            'createdBy',
        ]);

        $title = 'Purchase Return #' . $purchaseReturn->id;

        return view('admin.purchase.return-show', compact('purchaseReturn', 'title'));
    }
}
