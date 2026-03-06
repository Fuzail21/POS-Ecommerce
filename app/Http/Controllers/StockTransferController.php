<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockLedger;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index()
    {
        $title     = 'Stock Transfers';
        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'product', 'variant', 'creator'])
            ->latest()
            ->paginate(20);

        // Map warehouse_id → branch for display in the list
        $branchByWarehouseId = Branch::all()->keyBy('warehouse_id');

        return view('admin.stock.transfers', compact('transfers', 'title', 'branchByWarehouseId'));
    }

    public function create()
    {
        $title      = 'New Stock Transfer';
        $warehouses = Warehouse::orderBy('name')->get();
        $branches   = Branch::with('warehouse')->orderBy('name')->get();
        $products   = Product::whereNull('deleted_at')->with('variants')->get();

        // Build stock map: product_id → variant_key (0 = no variant) → array of { warehouse_id, qty }
        $stockMap = [];
        InventoryStock::where('quantity_in_base_unit', '>', 0)->get()
            ->each(function ($stock) use (&$stockMap) {
                $variantKey = $stock->variant_id ?? 0;
                $stockMap[$stock->product_id][$variantKey][] = [
                    'warehouse_id' => $stock->warehouse_id,
                    'qty'          => (float) $stock->quantity_in_base_unit,
                ];
            });

        return view('admin.stock.transfer-create', compact('warehouses', 'branches', 'products', 'title', 'stockMap'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id'   => 'required|exists:warehouses,id',
            'product_id'        => 'required|exists:products,id',
            'variant_id'        => 'nullable|exists:product_variants,id',
            'quantity'          => 'required|numeric|min:0.01',
        ]);

        $sourceStock = InventoryStock::where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->where('warehouse_id', $request->from_warehouse_id)
            ->first();

        if (!$sourceStock || $sourceStock->quantity_in_base_unit < $request->quantity) {
            return back()->withInput()->withErrors([
                'quantity' => 'Insufficient stock in the source warehouse. Available: ' . ($sourceStock->quantity_in_base_unit ?? 0),
            ]);
        }

        DB::transaction(function () use ($request, $sourceStock) {
            $qty       = $request->quantity;
            $reference = 'TRF-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));

            // Deduct from source warehouse
            $sourceStock->decrement('quantity_in_base_unit', $qty);

            // Add to destination warehouse (create if not exists)
            InventoryStock::firstOrCreate(
                [
                    'product_id'   => $request->product_id,
                    'variant_id'   => $request->variant_id,
                    'warehouse_id' => $request->to_warehouse_id,
                ],
                ['quantity_in_base_unit' => 0]
            )->increment('quantity_in_base_unit', $qty);

            // Save transfer record FIRST so we have the ID for ledger entries
            $transfer = StockTransfer::create([
                'from_warehouse_id'  => $request->from_warehouse_id,
                'to_warehouse_id'    => $request->to_warehouse_id,
                'product_id'         => $request->product_id,
                'variant_id'         => $request->variant_id,
                'quantity'           => $qty,
                'transfer_reference' => $reference,
                'notes'              => $request->notes,
                'created_by'         => auth()->id(),
            ]);

            // Stock ledger: OUT from source — linked to transfer record
            StockLedger::create([
                'product_id'                   => $request->product_id,
                'variant_id'                   => $request->variant_id,
                'warehouse_id'                 => $request->from_warehouse_id,
                'ref_type'                     => 'transfer',
                'ref_id'                       => $transfer->id,
                'quantity_change_in_base_unit' => $qty,
                'unit_cost'                    => 0,
                'direction'                    => 'out',
                'created_by'                   => auth()->id(),
            ]);

            // Stock ledger: IN to destination — linked to same transfer record
            StockLedger::create([
                'product_id'                   => $request->product_id,
                'variant_id'                   => $request->variant_id,
                'warehouse_id'                 => $request->to_warehouse_id,
                'ref_type'                     => 'transfer',
                'ref_id'                       => $transfer->id,
                'quantity_change_in_base_unit' => $qty,
                'unit_cost'                    => 0,
                'direction'                    => 'in',
                'created_by'                   => auth()->id(),
            ]);
        });

        return redirect()->route('stock.transfers.index')
                         ->with('success', 'Stock transferred successfully!');
    }
}
