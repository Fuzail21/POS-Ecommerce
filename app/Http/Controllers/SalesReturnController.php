<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    public function create($sale_id)
    {
        $sale = Sale::with('items.product', 'items.variant')->findOrFail($sale_id);
        $customers = Customer::all();
        $branches = Branch::all();
        $saleItems = $sale->items;

        return view('admin.sale.sales_return.create', compact('sale', 'customers', 'branches', 'saleItems'));
    }


}
