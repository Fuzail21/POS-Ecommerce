<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Profit;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;


class FinanceController extends Controller
{
    // ---------- PAYMENTS ----------

    public function index(){
        $title =  "Payments List";
        $payments = Payment::with(['entity', 'creator', 'reference'])
                   ->orderBy('created_at', 'desc')
                   ->paginate(20);

                        //    dd($payments);
    
        return view('admin.payments.list', compact('payments', 'title'));
    }

    public function create(){
        $title = "Add Payment";
        $customers = Sale::where('due_amount', '>', 0)->with('customer')->get()->groupBy('customer_id');
        $suppliers = Purchase::where('due_amount', '>', 0)->with('supplier')->get()->groupBy('supplier_id');
    
        return view('admin.payments.create', compact('title', 'customers', 'suppliers'));
    }

    public function store(Request $request){
        $request->validate([
            'entity_type'     => 'required|in:customer,supplier',
            'entity_id'       => 'required|integer',
            'ref_id'          => 'required|integer',
            'amount'          => 'required|numeric|min:0.01',
            'payment_method'  => 'required|in:cash,card,bank',
            'note'            => 'nullable|string',
        ]);

        $user = auth()->user();

        // Step 1: Get the reference record (Sale or Purchase)
        if ($request->entity_type === 'customer') {
            $reference = Sale::findOrFail($request->ref_id);
        } else {
            $reference = Purchase::findOrFail($request->ref_id);
        }

        // Prevent overpayment
        if ($request->amount > $reference->due_amount) {
            return back()->withErrors(['amount' => 'Payment exceeds due amount.'])->withInput();
        }

        // Step 2: Create payment record
        $payment = new Payment();
        $payment->entity_type     = $request->entity_type;
        $payment->entity_id       = $request->entity_id;
        $payment->ref_type        = $request->ref_type;
        $payment->ref_id          = $reference->id;
        $payment->amount          = $request->amount;
        $payment->payment_method  = $request->payment_method;
        $payment->note            = $request->note;
        $payment->created_by      = $user->id;
        $payment->save();

        // Step 3: Update Sale or Purchase record
        $reference->paid_amount += $request->amount;
        $reference->due_amount  -= $request->amount;
        $reference->save();

        // Step 4: Update Customer or Supplier balance
        if ($request->entity_type === 'customer') {
            $customer = Customer::findOrFail($reference->customer_id);
            $customer->balance -= $request->amount;
            $customer->save();
        } else {
            $supplier = Supplier::findOrFail($reference->supplier_id);
            $supplier->balance -= $request->amount;
            $supplier->save();
        }

        return redirect()->route('payments.list')->with('success', 'Payment recorded and balances updated successfully.');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $payment = Payment::findOrFail($id);
            $amount = $payment->amount;

            // Update Sale or Purchase record
            if ($payment->ref_type === 'sale') {
                $sale = Sale::findOrFail($payment->ref_id);
                $sale->paid_amount -= $amount;
                $sale->due_amount += $amount;
                $sale->save();
            } elseif ($payment->ref_type === 'purchase') {
                $purchase = Purchase::findOrFail($payment->ref_id);
                $purchase->paid_amount -= $amount;
                $purchase->due_amount += $amount;
                $purchase->save();
            }
            
            // Update Customer or Supplier balance
            if ($payment->entity_type === 'customer') {
                $customer = Customer::findOrFail($payment->entity_id);
                $customer->balance += $amount;
                $customer->save();
            } elseif ($payment->entity_type === 'supplier') {
                $supplier = Supplier::findOrFail($payment->entity_id);
                $supplier->balance += $amount;
                $supplier->save();
            }

            // Finally, delete the payment
            $payment->delete();

            DB::commit();
            return redirect()->route('payments.list')->with('success', 'Payment deleted and balances updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }
}
