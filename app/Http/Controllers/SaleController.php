<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Product;
use App\Models\Customer;    
use Illuminate\Http\Request;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // Show list of all sales
    public function index()
    {
        $title = "Sales List";
        $sales = Sale::with(['customer', 'user'])->latest()->paginate(10);
        return view('admin.sale.list', compact('sales', 'title'));
    }

    // Show create sale form
    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $title = "Add Sale";
        return view('admin.sale.add', compact('customers', 'products', 'title'));
    }

    // Store new sale and sale items
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'payment_status' => 'required|in:pending,paid',
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|numeric|min:1',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $sale = new Sale();
            $sale->customer_id = $request->customer_id;
            $sale->user_id = auth()->id();
            $sale->total_amount = $request->total_amount;
            $sale->discount = $request->discount ?? 0;
            $sale->tax = $request->tax ?? 0;
            $sale->payment_status = $request->payment_status;
            $sale->save();

            foreach ($request->products as $index => $productId) {
                $item = new SalesItem();
                $item->sale_id = $sale->id;
                $item->product_id = $productId;
                $item->quantity = $request->quantities[$index];
                $item->price = $request->prices[$index];
                $item->save();
            }
        });

        return redirect()->route('sale.list')->with('success', 'Sale created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        $title = "Edit Sale";
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.sale.add', compact('sale', 'customers', 'products', 'title'));
    }

    // Update sale and sale items
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'payment_status' => 'required|in:pending,paid',
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|numeric|min:1',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {
            $sale = Sale::findOrFail($id);
            $sale->customer_id = $request->customer_id;
            $sale->user_id = auth()->id();
            $sale->total_amount = $request->total_amount;
            $sale->discount = $request->discount ?? 0;
            $sale->tax = $request->tax ?? 0;
            $sale->payment_status = $request->payment_status;
            $sale->save();

            // Delete old sale items
            SalesItem::where('sale_id', $sale->id)->delete();

            // Add new sale items
            foreach ($request->products as $index => $productId) {
                $item = new SalesItem();
                $item->sale_id = $sale->id;
                $item->product_id = $productId;
                $item->quantity = $request->quantities[$index];
                $item->price = $request->prices[$index];
                $item->save();
            }
        });

        return redirect()->route('sale.list')->with('success', 'Sale updated successfully.');
    }

    // Delete a sale and its items
    public function destroy($id)
    {
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
