<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\StockLedger;
use App\Models\InventoryStock;
use App\Models\Warehouse;

use App\Models\Payment;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\Unit;

class PurchaseController extends Controller
{
    public function index(){
        $title = "Purchases List";
        $purchases = Purchase::with(['supplier', 'branch', 'warehouse'])->latest()->paginate(20);
        return view('admin.purchase.list', compact('purchases', 'title'));
    }

    public function create(){
        $title = "Add Purchase";
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $products = Product::with('baseUnit')->get(); 
        $productsMapped = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price,
                'unit' => $product->baseUnit->name,
                'unit_id' => $product->baseUnit->id,
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->variant_name,
                        'barcode' => $variant->barcode,
                        'sku' => $variant->sku,
                        'price' => $variant->sale_price,
                    ];
                }),
            ];
        });

        return view('admin.purchase.create', compact('title', 'suppliers', 'products', 'productsMapped', 'warehouses'));
    }

    public function store(Request $request){
        // dd($request->all());

        $year = date('Y');

        // Get the last sale for the current year
        $lastPurchase = Purchase::whereYear('created_at', $year)
            ->where('invoice_number', 'like', "{$year}-invoice-%")
            ->orderBy('id', 'desc')
            ->first();
            
        if ($lastPurchase && preg_match("/{$year}-invoice-(\d+)/", $lastPurchase->invoice_number, $matches)) {
            $lastNumber = (int)$matches[1];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $invoiceNo = $year . '-invoice-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            // Step 1: Insert into purchases
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id, // $request->warehouse_id
                'invoice_number' => $invoiceNo,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $request->subtotal,
                // 'discount' => $request->discount,
                // 'tax' => $request->tax,
                'paid_amount' => $request->payment_now,
                'due_amount' => $request->due,
                'payment_method' => $request->payment_method,
                'created_by' => auth()->id(),
            ]);

            // Step 2–4: Insert purchase_items, stock_ledger, update inventory_stocks
            foreach ($request->products as $product) {
                $productId = $product['id'];
                $variantId = $product['variant_id'] ?? null;
                $unitId = $product['unit'];
                $quantity = $product['quantity'];
                $unitCost = $product['unit_cost'];
                $subTotal = $product['subtotal'];

                // Calculate base quantity
                $unit = Unit::find($unitId);
                $baseQty = $quantity * ($unit->conversion_factor ?? 1);

                // Insert into purchase_items
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'batch_no' => null,
                    'expiry_date' => null,
                    'quantity' => $product['quantity'],
                    'unit_id' => $unitId,
                    'quantity_in_base_unit' => $baseQty,
                    'unit_cost' => $unitCost,
                    'total_cost' => $subTotal,
                    'created_at' => auth()->id(),
                ]);

                // Insert into stock_ledger
                StockLedger::create([
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'warehouse_id' => $purchase->warehouse_id, // $request->warehouse_id
                    'ref_type' => 'purchase',
                    'ref_id' => $purchase->id,
                    'quantity_change_in_base_unit' => $baseQty,
                    'unit_cost' => $unitCost,
                    'direction' => 'in',
                    'created_by' => auth()->id(),
                ]);

                // Update or insert inventory stock
                $stock = InventoryStock::firstOrNew([
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'warehouse_id' => $purchase->warehouse_id, // $request->warehouse_id
                ]);
                $stock->quantity_in_base_unit += $baseQty;
                $stock->save();
            }

            // Step 5: Record payment if applicable
            if ($request->payment_now > 0) {
                Payment::create([
                    'entity_type' => 'supplier',
                    'entity_id' => $request->supplier_id,
                    'transaction_type' => 'in',
                    'amount' => $request->payment_now,
                    'payment_method' => $request->payment_mode,
                    'ref_type' => 'purchase',
                    'ref_id' => $purchase->id,
                    'note' => null,
                    'created_by' => auth()->id(),
                ]);
            }

            // Step 6: Update supplier balance
           $dueAmount = $request->due;

            if ($dueAmount > 0) {
                Supplier::where('id', $request->supplier_id)->increment('balance', $dueAmount);
            } elseif ($dueAmount < 0) {
                Supplier::where('id', $request->supplier_id)->decrement('balance', abs($dueAmount));
            }


            DB::commit();

            return redirect()->route('purchases.list')->with('success', 'Purchase created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
        }
    }

    // public function edit($id){
    //     $title = "Edit Purchase";
    //     $suppliers = Supplier::all();
    //     $products = Product::with('baseUnit')->get(); 
    //     $productsMapped = $products->map(function ($product) {
    //         return [
    //             'id' => $product->id,
    //             'name' => $product->name,
    //             'price' => $product->sale_price,
    //             'unit' => $product->baseUnit->name,
    //             'unit_id' => $product->baseUnit->id, // <-- Add this line
    //         ];
    //     });
    //     $purchase = Purchase::with('items')->findOrFail($id);
    //     return view('admin.purchase.create', compact('purchase', 'title', 'suppliers', 'products', 'productsMapped'));
    // }

    // public function update(Request $request, $id){
    //     $purchase = Purchase::findOrFail($id);
    //     $purchase->vendor_id = $request->vendor_id;
    //     $purchase->total_amount = $request->total_amount;
    //     $purchase->save();

    //     // Remove old items
    //     $purchase->items()->delete();

    //     // Add updated items
    //     foreach ($request->items as $item) {
    //         $purchaseItem = new PurchaseItem();
    //         $purchaseItem->purchase_id = $purchase->id;
    //         $purchaseItem->product_id = $item['product_id'];
    //         $purchaseItem->quantity = $item['quantity'];
    //         $purchaseItem->cost = $item['cost'];
    //         $purchaseItem->save();
    //     }

    //     return redirect()->route('purchase.list')->with('success', 'Purchase updated successfully.');
    // }

    public function destroy($id){
        DB::beginTransaction();

        try {
            $purchase = Purchase::with('items')->findOrFail($id);

            // Loop through purchase items to reverse stock changes
            foreach ($purchase->items as $item) {
                $stock = InventoryStock::where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->where('warehouse_id', $purchase->warehouse_id) // warehouse_id from purchase, might be null if not set
                    ->first();

                if ($stock) {
                    // Reduce stock quantity by purchased quantity (base unit)
                    $stock->quantity_in_base_unit -= $item->quantity_in_base_unit;
                    $stock->quantity_in_base_unit = max(0, $stock->quantity_in_base_unit);
                    $stock->save();
                }

                // Delete related stock ledger entries
                StockLedger::where('ref_type', 'purchase')
                    ->where('ref_id', $purchase->id)
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->delete();
            }

            // Delete purchase items
            $purchase->items()->delete();

            // Delete payments related to this purchase
            Payment::where('ref_type', 'purchase')
                ->where('ref_id', $purchase->id)
                ->delete();

            // Delete the inventory stock entry if you want to fully remove the stock record
            // Optional: Remove if you just want to adjust quantity, not delete record
            InventoryStock::where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->where('warehouse_id', $purchase->warehouse_id)
                ->delete();

            // Adjust supplier balance if due_amount exists
            if ($purchase->due_amount > 0) {
                $supplier = $purchase->supplier;
                if ($supplier) {
                    $supplier->balance -= $purchase->due_amount;
                    $supplier->save();
                }
            }

            // Finally, delete the purchase itself
            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.list')->with('success', 'Purchase and all related data deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchases.list')->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }


    // public function showItems($id) {
    //     $title = "Purchase Items";
    
    //     // Eager loads the purchase with its items and each item's product
    //     $purchase = Purchase::with('items.product')->findOrFail($id);
    
    //     // Passes the data to the Blade view
    //     return view('admin.purchase_items.list', compact('purchase', 'title'));
    // }

    public function invoice($id){
        $title = 'Invoice';
        $purchase = Purchase::with(['supplier', 'warehouse', 'items.unit', 'items.product', 'items.variant'])
                    ->findOrFail($id);


        return view('admin.purchase.invoice', compact('purchase', 'title')); // passes $purchase to the view
    }

}
