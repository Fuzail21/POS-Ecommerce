<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Product;
use App\Models\SalesDiscountTax;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // Show list of all sales
    public function index(){
        $title = "Sales List";
        $sales = Sale::with(['customer', 'user'])->latest()->paginate(10);
        return view('admin.sale.list', compact('sales', 'title'));
    }

    public function create(Request $request){
        $title = "Add Sale";
        $categories = Category::all();
        $customers = Customer::all();
        $units = Unit::all();
    
        $search = $request->input('search');
    
        if ($search) {
            // Search mode: fetch all matching products (no pagination)
            $products = Product::whereNull('deleted_at')
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%")
                          ->orWhere('barcode', 'like', "%{$search}%");
                })
                ->get();
        } else {
            // No search: show latest 10 products with pagination
            $products = Product::whereNull('deleted_at')
                ->latest()->get();
        }
    
        return view('admin.sale.create', compact('categories', 'units', 'products', 'title', 'customers'));
    }

    public function process(Request $request){

        dd($request->all());

        $year = date('Y');

        // Get the last sale for the current year
        $lastSale = Sale::whereYear('created_at', $year)
            ->where('invoice_number', 'like', "{$year}-invoice-%")
            ->orderBy('id', 'desc')
            ->first();
            
        if ($lastSale && preg_match("/{$year}-invoice-(\d+)/", $lastSale->invoice_number, $matches)) {
            $lastNumber = (int)$matches[1];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $invoiceNo = $year . '-invoice-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            // Calculate totals
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['unit_price'] * $item['quantity'];
            }

            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $grandTotal = $totalAmount - $discount + $tax;
            $paidAmount = $request->paid_amount ?? 0;
            $dueAmount = $grandTotal - $paidAmount;

            // Generate invoice number (you can customize this)
            $invoiceNo = 'INV-' . time();

            // 1. Create Sale
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'warehouse_id' => null, // $request->warehouse_id
                'invoice_no' => $invoiceNo,
                'sale_date' => Carbon::parse($request->sale_date),
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'note' => $request->note,
            ]);

            // 2. Sale Items
            foreach ($request->items as $item) {
                $totalPrice = $item['unit_price'] * $item['quantity'];

                $unit = Unit::find($unitId);
                $baseQty = $quantity * ($unit->conversion_factor ?? 1);

                // Insert into sale_items
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'unit_id' => $item['unit_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                ]);

                // 3. Update inventory_stocks (decrease)
                $stock = InventoryStock::where([
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'warehouse_id' => $request->warehouse_id,
                ])->first();


                if ($stock) {
                    $stock->decrement('quantity_in_base_unit', $baseQty);
                }

                // 4. Insert into stock_ledger
                StockLedger::create([
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'warehouse_id' => $request->warehouse_id,
                    'ref_type' => 'sale',
                    'ref_id' => $sale->id,
                    'quantity_change_in_base_unit' => $baseQty,
                    'unit_cost' => $item['unit_price'], // Optional: You can use purchase cost instead
                    'direction' => 'out',
                    'created_by' => auth()->id(),
                ]);
            }

            // 5. Insert payment if any
            if ($paidAmount > 0) {
                Payment::create([
                    'customer_id' => $request->customer_id,
                    'ref_type' => 'sale',
                    'ref_id' => $sale->id,
                    'amount' => $paidAmount,
                    'method' => $request->method ?? 'cash',
                    'paid_by' => auth()->id(),
                    'payment_date' => now(),
                    'note' => $request->payment_note,
                ]);
            }

            // 6. Update customer balance
            if ($dueAmount > 0) {
                Customer::where('id', $request->customer_id)->increment('balance', $dueAmount);
            }

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    // Delete a sale and its items
    public function destroy($id){
        $sale = Sale::findOrFail($id);

        DB::transaction(function () use ($sale) {
            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sale.list')->with('success', 'Sale deleted successfully.');
    }

    public function showItems($saleId)
    {
        $title = "Sales-Items";
        $sale = Sale::with(['customer', 'items.product'])->findOrFail($saleId);
        return view('admin.sale_items.list', compact('sale', 'title'));
    }
}
