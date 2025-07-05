<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SalesDiscountTax;

class SalesDiscountTaxController extends Controller
{
    public function edit($saleId){
        $sale = Sale::findOrFail($saleId);
        $tax = SalesDiscountTax::firstOrNew(['sale_id' => $saleId]);
        return view('admin.discount_tax.form', compact('sale', 'tax'));
    }

    public function update(Request $request, $saleId){
        $request->validate([
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);

        $tax = SalesDiscountTax::firstOrNew(['sale_id' => $saleId]);
        $tax->discount = $request->discount ?? 0;
        $tax->tax = $request->tax ?? 0;
        $tax->save();

        return redirect()->route('payments.list', $saleId)
                         ->with('success', 'Sales tax and discount updated.');
    }
}

