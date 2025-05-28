<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\StockLedger;
use App\Models\InventoryStock;
use App\Models\Payment;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\Unit;

class PurchaseController extends Controller
{
    public function index(){
        $title = "Purchases List";
        $purchases = Purchase::with(['supplier', 'branch'])->latest()->paginate(20);
        return view('admin.purchase.list', compact('purchases', 'title'));
    }

    public function create(){
        $title = "Add Purchase";
        $suppliers = Supplier::all();
        $products = Product::with('baseUnit')->get(); 
        $productsMapped = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price,
                'unit' => $product->baseUnit->name,
                'unit_id' => $product->baseUnit->id, // <-- Add this line
            ];
        });
                    // dd($productsMapped);

        return view('admin.purchase.create', compact('title', 'suppliers', 'products', 'productsMapped'));
    }

    public function store(Request $request){
        dd($request->all());
        DB::beginTransaction();

        try {
            // Step 1: Insert into purchases
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'branch_id' => null, // $request->branch_id
                'invoice_number' => $request->invoice_no,
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
                    'quantity_in_base_unit' => $baseQty,
                    'unit_cost' => $unitCost,
                    'total_cost' => $subTotal,
                    'created_at' => auth()->id(),
                ]);

                // Insert into stock_ledger
                StockLedger::create([
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'warehouse_id' => null, // $request->branch_id
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
                    'warehouse_id' => null, // $request->branch_id
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
            }

            // // Step 7: Create supplier ledger entry (optional)
            // SupplierLedger::create([
            //     'supplier_id' => $request->supplier_id,
            //     'ref_type' => 'purchase',
            //     'ref_id' => $purchase->id,
            //     'debit' => $request->grand_total,
            //     'credit' => $request->paid_amount,
            //     'balance' => Supplier::find($request->supplier_id)->balance,
            // ]);

            DB::commit();

            return redirect()->route('purchases.list')->with('success', 'Purchase created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
        }
    }

    public function edit($id){
        $title = "Edit Purchase";
        $suppliers = Supplier::all();
        $products = Product::with('baseUnit')->get(); 
        $productsMapped = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price,
                'unit' => $product->baseUnit->name,
                'unit_id' => $product->baseUnit->id, // <-- Add this line
            ];
        });
        $purchase = Purchase::with('items')->findOrFail($id);
        return view('admin.purchase.create', compact('purchase', 'title', 'suppliers', 'products', 'productsMapped'));
    }

    public function update(Request $request, $id){
        $purchase = Purchase::findOrFail($id);
        $purchase->vendor_id = $request->vendor_id;
        $purchase->total_amount = $request->total_amount;
        $purchase->save();

        // Remove old items
        $purchase->items()->delete();

        // Add updated items
        foreach ($request->items as $item) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->purchase_id = $purchase->id;
            $purchaseItem->product_id = $item['product_id'];
            $purchaseItem->quantity = $item['quantity'];
            $purchaseItem->cost = $item['cost'];
            $purchaseItem->save();
        }

        return redirect()->route('purchase.list')->with('success', 'Purchase updated successfully.');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $purchase = Purchase::with('purchaseItems')->findOrFail($id);

            // Loop through items and reverse stock
            foreach ($purchase->purchaseItems as $item) {
                // 1. Decrease inventory stock
                $stock = InventoryStock::where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->where('warehouse_id', $purchase->warehouse_id)
                    ->first();

                if ($stock) {
                    $stock->quantity_in_base_unit -= $item->quantity_in_base_unit; // ensure this is base unit
                    $stock->quantity_in_base_unit = max(0, $stock->quantity_in_base_unit); // prevent negative
                    $stock->save();
                }

                // 2. Delete stock ledger entries
                StockLedger::where('ref_type', 'purchase')
                    ->where('ref_id', $purchase->id)
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->delete();
            }

            // 3. Delete purchase items
            $purchase->purchaseItems()->delete();

            // 4. Delete payments if any
            if (method_exists($purchase, 'payments')) {
                $purchase->payments()->delete();
            }

            // 5. Delete supplier ledger entry (optional)
            SupplierLedger::where('ref_type', 'purchase')
                ->where('ref_id', $purchase->id)
                ->delete();

            // 6. Adjust supplier balance
            if ($purchase->due_amount > 0) {
                $supplier = $purchase->supplier;
                $supplier->balance -= $purchase->due_amount;
                $supplier->save();
            }

            // 7. Delete the purchase
            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.list')->with('success', 'Purchase and all related data deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchases.list')->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }


    public function showItems($id) {
        $title = "Purchase Items";
    
        // Eager loads the purchase with its items and each item's product
        $purchase = Purchase::with('items.product')->findOrFail($id);
    
        // Passes the data to the Blade view
        return view('admin.purchase_items.list', compact('purchase', 'title'));
    }

    public function invoice($id)
    {
        $purchase = Purchase::with(['supplier', 'items'])
                            ->findOrFail($id);

        return view('admin.purchase.invoice', compact('purchase'));
    }


}
