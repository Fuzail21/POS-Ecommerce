<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;

class PurchaseController extends Controller
{
    public function index()
    {
        $title = "Purchases List";
        $purchases = Purchase::with('vendor')->latest()->paginate(20);
        return view('admin.purchase.list', compact('purchases', 'title'));
    }

    public function create()
    {
        $title = "Add Purchase";
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('admin.purchase.create', compact('title', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $purchase = new Purchase();
        $purchase->vendor_id = $request->vendor_id;
        $purchase->total_amount = $request->total_amount;
        $purchase->save();

        foreach ($request->items as $item) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->purchase_id = $purchase->id;
            $purchaseItem->product_id = $item['product_id'];
            $purchaseItem->quantity = $item['quantity'];
            $purchaseItem->cost = $item['cost'];
            $purchaseItem->save();
        }

        return redirect()->route('purchase.list')->with('success', 'Purchase created successfully.');
    }

    public function edit($id)
    {
        $title = "Edit Purchase";
        $purchase = Purchase::with('items')->findOrFail($id);
        $vendors = Vendor::all();
        $products = Product::all();

        return view('admin.purchase.add', compact('purchase', 'vendors', 'products', 'title'));
    }

    public function update(Request $request, $id)
    {
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
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        return redirect()->route('purchase.list')->with('success', 'Purchase deleted successfully.');
    }

    public function showItems($id) {
        $title = "Purchase Items";
    
        // Eager loads the purchase with its items and each item's product
        $purchase = Purchase::with('items.product')->findOrFail($id);
    
        // Passes the data to the Blade view
        return view('admin.purchase_items.list', compact('purchase', 'title'));
    }

}
