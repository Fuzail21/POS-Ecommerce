<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\InventoryStock;
use App\Models\StockLedger;
use App\Models\ProductVariant;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SaleController extends Controller
{
    public function index(){
        $title = "Sales List";
        $sales = Sale::with(['customer', 'items', 'payments', 'branch'])->latest()->paginate(10);
        return view('admin.sale.list', compact('sales', 'title'));
    }

    public function create(Request $request){
        $title = "Add Sale";
        $categories = Category::all();
        $customers = Customer::all();
        $units = Unit::all();

        $search = $request->input('search');

        if ($search) {
            $products = Product::whereNull('deleted_at')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%")
                          ->orWhere('barcode', 'like', "%{$search}%");
                })
                ->with([
                    'baseUnit',
                    'variants.inventoryStock',
                    'variants.product.baseUnit',
                    'inventoryStock',
                ])
                ->get()
                ->map(function ($product) {
                    $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
                    $baseQuantity = $product->inventoryStock?->quantity_in_base_unit ?? 0;

                    $product->stock_quantity = $baseQuantity / $conversionFactor;
                    $product->in_stock = $product->stock_quantity > 0;

                    foreach ($product->variants as $variant) {
                        $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
                        $variantQuantity = $variant->inventoryStock?->quantity_in_base_unit ?? 0;

                        $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
                        $variant->in_stock = $variant->stock_quantity > 0;
                    }

                    return $product;
                });
        } else {
            $products = Product::whereNull('deleted_at')
                ->latest()
                ->take(10)
                ->with([
                    'baseUnit',
                    'variants.inventoryStock',
                    'variants.product.baseUnit',
                    'inventoryStock',
                ])
                ->get()
                ->map(function ($product) {
                    $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
                    $baseQuantity = $product->inventoryStock?->quantity_in_base_unit ?? 0;

                    $product->stock_quantity = $baseQuantity / $conversionFactor;
                    $product->in_stock = $product->stock_quantity > 0;

                    foreach ($product->variants as $variant) {
                        $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
                        $variantQuantity = $variant->inventoryStock?->quantity_in_base_unit ?? 0;

                        $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
                        $variant->in_stock = $variant->stock_quantity > 0;
                    }

                    return $product;
                });
        }

        return view('admin.sale.create', compact(
            'categories',
            'units',
            'products',
            'title',
            'customers'
        ));
    }

    public function process(Request $request){
        // dd($request->all());
        $year = date('Y');

        // Generate the next invoice number
        $lastSale = Sale::whereYear('created_at', $year)
            ->where('invoice_number', 'like', "{$year}-invoice-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastSale && preg_match("/{$year}-invoice-(\d+)/", $lastSale->invoice_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $invoiceNo = "{$year}-invoice-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            $cart = json_decode($request->cart_data, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid cart data JSON: ' . json_last_error_msg());
            }

            // Create the sale record
            $sale = Sale::create([
                'customer_id'     => $request->customer_id,
                'warehouse_id'    => null,
                'invoice_number'  => $invoiceNo,
                'sale_date'       => now(),
                'total_amount'    => $request->subtotal,
                'discount_amount' => $request->discount,
                'tax_amount'      => $request->tax,
                'final_amount'    => $request->total_payable,
                'paid_amount'     => $request->amount_paid,
                'due_amount'      => $request->balance_due,
                'payment_method'  => $request->payment_method,
                'created_by'      => auth()->id(),
            ]);

            foreach ($cart as $itemKey => $item) {
                $variantId = null;
                $productId = $item['id'];

                if (($item['type'] ?? null) === 'variant') {
                    $variantId = $item['id'];
                    $variant = ProductVariant::find($variantId);
                
                    if (!$variant) {
                        throw new \Exception("Variant with ID {$variantId} does not exist.");
                    }
                
                    $productId = $variant->product_id;
                }
            
                $product = Product::find($productId);
                if (!$product) {
                    throw new \Exception("Product with ID {$productId} does not exist.");
                }

                $unitId     = $item['unit_id'] ?? null;
                $quantity   = $item['qty'];
                $unitPrice  = $item['sale_price'];
                $totalPrice = $unitPrice * $quantity;

                $unit = Unit::find($unitId);
                $baseQty = $quantity * ($unit->conversion_factor ?? 1);

                SaleItem::create([
                    'sale_id'               => $sale->id,
                    'product_id'            => $productId,
                    'variant_id'            => $variantId,
                    'branch_id'             => null,
                    'unit_id'               => $unitId,
                    'quantity'              => $quantity,
                    'unit_price'            => $unitPrice,
                    'total_price'           => $totalPrice,
                    'quantity_in_base_unit' => $baseQty,
                    'discount'              => null,
                    'tax'                   => null,
                ]);

                // Update inventory stock
                $stock = InventoryStock::where([
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'warehouse_id' => null,
                ])->first();
                
                if ($stock) {
                    if ($stock->quantity_in_base_unit < $baseQty) {
                        return redirect()->route('sales.list')->with('error', 'Stock exceeded. Only ' . $stock->quantity_in_base_unit . ' units available.');
                    }
                    $stock->decrement('quantity_in_base_unit', $baseQty);
                } else {
                    return redirect()->route('sales.list')->with('error', 'No stock record found.');
                
                }

                StockLedger::create([
                    'product_id'                   => $productId,
                    'variant_id'                   => $variantId,
                    'warehouse_id'                 => null,
                    'ref_type'                     => 'sale',
                    'ref_id'                       => $sale->id,
                    'quantity_change_in_base_unit' => $baseQty,
                    'unit_cost'                    => $unitPrice,
                    'direction'                    => 'out',
                    'created_by'                   => auth()->id(),
                ]);
            }

            // Handle payment
            if ($request->amount_paid > 0) {
                Payment::create([
                    'entity_type'      => 'customer',
                    'entity_id'        => $request->customer_id,
                    'transaction_type' => 'out',
                    'ref_type'         => 'sale',
                    'ref_id'           => $sale->id,
                    'amount'           => $request->amount_paid,
                    'method'           => $request->payment_method,
                    'created_by'       => auth()->id(),
                    'note'             => null,
                ]);
            }

            // Adjust customer balance
            $dueAmount = $request->balance_due;
            if ($dueAmount > 0) {
                Customer::where('id', $request->customer_id)->increment('balance', $dueAmount);
            } elseif ($dueAmount < 0) {
                Customer::where('id', $request->customer_id)->decrement('balance', abs($dueAmount));
            }

            DB::commit();

            return redirect()->route('sales.list')->with('success', 'Sale recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Sale Processing Failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()->withInput()->with('error', 'An error occurred while processing the sale: ' . $e->getMessage());
        }
    }

    public function destroy($id){
        $sale = Sale::with('items')->findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($sale->items as $item) {
                $productId = $item->product_id;
                $variantId = $item->variant_id;
                $baseQty   = $item->quantity_in_base_unit;

                // Restore inventory stock
                $stock = InventoryStock::where([
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'warehouse_id' => null,
                ])->first();

                if ($stock) {
                    $stock->increment('quantity_in_base_unit', $baseQty);
                }

                // Delete stock ledger
                StockLedger::where([
                    'ref_type'     => 'sale',
                    'ref_id'       => $sale->id,
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'warehouse_id' => null,
                ])->delete();

                // Delete sale item
                $item->delete();
            }

            // Delete payment record(s)
            Payment::where([
                'ref_type' => 'sale',
                'ref_id'   => $sale->id,
            ])->delete();

            // Adjust customer balance
            if ($sale->due_amount > 0) {
                Customer::where('id', $sale->customer_id)->decrement('balance', $sale->due_amount);
            } elseif ($sale->due_amount < 0) {
                Customer::where('id', $sale->customer_id)->increment('balance', abs($sale->due_amount));
            }

            // Delete the sale record
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.list')->with('success', 'Sale deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Sale Deletion Failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'sale_id' => $id,
            ]);

            return redirect()->route('sales.list')->with('error', 'An error occurred while deleting the sale.');
        }
    }

    public function invoice($id){
        $title = 'Invoice';
        $sale = Sale::with(['customer', 'warehouse', 'items.unit', 'items.product', 'items.variant'])
                    ->findOrFail($id);

        return view('admin.sale.invoice', compact('sale', 'title')); // passes $purchase to the view
    }
}
