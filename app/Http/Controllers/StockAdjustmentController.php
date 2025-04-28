<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $title = "Stocks List";
        $adjustments = StockAdjustment::with('product')->latest()->get();
        return view('admin.stock_adjustments.list', compact('adjustments', 'title'));
    }

    public function create()
    {
        $title = "Add Stocks";
        $products = Product::all();
        return view('admin.stock_adjustments.add', compact('products', 'title'));
    }

    public function store(Request $request)
    {
        $adjustment = new StockAdjustment();
        $adjustment->product_id = $request->product_id;
        $adjustment->adjustment_type = $request->adjustment_type;
        $adjustment->quantity = $request->quantity;
        $adjustment->reason = $request->reason;
        $adjustment->save();

        return redirect()->route('stock_adjustments.list')->with('success', 'Stock adjustment created successfully.');
    }

    public function edit($id)
    {
        $title = "Edit Stocks";
        $adjustment = StockAdjustment::findOrFail($id);
        $products = Product::all();
        return view('admin.stock_adjustments.add', compact('adjustment', 'products', 'title'));
    }

    public function update(Request $request, $id)
    {
        $adjustment = StockAdjustment::findOrFail($id);
        $adjustment->product_id = $request->product_id;
        $adjustment->adjustment_type = $request->adjustment_type;
        $adjustment->quantity = $request->quantity;
        $adjustment->reason = $request->reason;
        $adjustment->save();

        return redirect()->route('stock_adjustments.list')->with('success', 'Stock adjustment updated successfully.');
    }

    public function destroy($id)
    {
        $adjustment = StockAdjustment::findOrFail($id);
        $adjustment->delete();

        return redirect()->route('stock_adjustments.list')->with('success', 'Stock adjustment deleted successfully.');
    }
}
