<?php

namespace App\Http\Controllers;

use App\Models\SalesPayment;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesPaymentController extends Controller
{
    public function index($saleId)
    {
        $sale = Sale::findOrFail($saleId);
        $payments = $sale->payments;     
        return view('admin.payments.list', compact('sale','payments'));
    }

    public function create($saleId)
    {
        $sale = Sale::findOrFail($saleId);
        return view('admin.payments.form', compact('sale'));
    }

    public function store(Request $request, $saleId)
    {
        $p = new SalesPayment();
        $p->sale_id = $saleId;
        $p->amount_paid = $request->amount_paid;
        $p->payment_method = $request->payment_method;
        $p->save();

        return redirect()->route('payments.list', $saleId)
                         ->with('success','Payment added.');
    }

    public function edit($paymentId)
    {
        $payment = SalesPayment::findOrFail($paymentId);
        return view('admin.payments.form', compact('payment'));
    }

    public function update(Request $request, $paymentId)
    {
        $p = SalesPayment::findOrFail($paymentId);
        $p->amount_paid = $request->amount_paid;
        $p->payment_method = $request->payment_method;
        $p->save();

        return redirect()->route('payments.list', $p->sale_id)
                         ->with('success','Payment updated.');
    }

    public function destroy($paymentId)
    {
        $p = SalesPayment::findOrFail($paymentId);
        $saleId = $p->sale_id;
        $p->delete();

        return redirect()->route('payments.list', $saleId)
                         ->with('success','Payment deleted.');
    }
}
