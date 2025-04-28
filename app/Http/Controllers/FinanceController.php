<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Profit;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Store;

class FinanceController extends Controller
{
    // ---------- PAYMENTS ----------

    public function payments() {
        $title =  "Payments List";
        $payments = Payment::with(['customer','vendor'])
                           ->paginate(20);
    
        return view('admin.finance.payment.list', compact('payments', 'title'));
    }
    

    public function createPayment()
    {
        $title =  "Add Payment";
        $loans = Loan::with(['customer', 'vendor'])->get();
        return view('admin.finance.payment.add', compact('loans', 'title'));
    }


    public function storePayment(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|integer',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);
    
        $loan = Loan::findOrFail($request->loan_id);
        // dd($loan);

        // Check if amount_paid is greater than remaining loan amount
        if ($request->amount_paid > $loan->amount) {
            return back()->withErrors(['amount_paid' => 'The payment amount exceeds the remaining loan amount.'])->withInput();
        }
    
        // Create the payment
        $payment = new Payment();
        $payment->loan_id = $loan->id;
        $payment->amount_paid = $request->amount_paid;
        $payment->payment_date = $request->payment_date;
        $payment->save();
    
        // Subtract payment from loan
        $loan->amount -= $request->amount_paid;
    
        // If loan is fully paid, mark it as paid
        if ($loan->amount <= 0) {
            $loan->amount = 0;
            $loan->status = 'paid'; // assuming 'status' is a column
        }
    
        $loan->save();
    
        return redirect()->route('payment.list')->with('success', 'Payment added and loan updated successfully.');
    }
    


    public function editPayment($id)
    {
        $title =  "Edit Payment";
        $payment = Payment::findOrFail($id);
        $payment->load(['customer', 'vendor']);

        // If you're loading all loans
        $loans = Loan::with(['customer', 'vendor'])->get();

        return view('admin.finance.payment.add', compact('payment', 'loans', 'title'));
    }




    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'loan_id' => 'required|integer',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);

        $payment = Payment::findOrFail($id);
        $loan = Loan::findOrFail($request->loan_id);

        // First, reverse the previous payment amount
        $loan->amount += $payment->amount_paid;

        // Check if new amount_paid exceeds the loan amount
        if ($request->amount_paid > $loan->amount) {
            return back()->withErrors(['amount_paid' => 'The payment amount exceeds the remaining loan amount.'])->withInput();
        }

        // Update the payment
        $payment->loan_id = $loan->id;
        $payment->amount_paid = $request->amount_paid;
        $payment->payment_date = $request->payment_date;
        $payment->save();

        // Subtract the new payment amount from the loan
        $loan->amount -= $request->amount_paid;

        // If loan is fully paid, mark it as paid
        if ($loan->amount <= 0) {
            $loan->amount = 0;
            $loan->status = 'paid';
        } else {
            $loan->status = 'pending'; // still some amount left
        }

        $loan->save();

        return redirect()->route('payment.list')->with('success', 'Payment updated and loan updated successfully.');
    }


    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        $loan = Loan::findOrFail($payment->loan_id);

        // Add the payment amount back to the loan
        $loan->amount += $payment->amount_paid;

        // Update loan status
        if ($loan->amount <= 0) {
            $loan->amount = 0;
            $loan->status = 'paid';
        } else {
            $loan->status = 'pending';
        }

        $loan->save();

        // Now delete the payment
        $payment->delete();

        return redirect()->route('payment.list')->with('success', 'Payment deleted and loan updated successfully.');
    }


    // ---------- PROFITS ----------

    public function profits()
    {
        $title = "Profits List";
        $profits = Profit::with('store')->paginate(20);
        return view('admin.finance.profit.list', compact('profits', 'title'));
    }

    public function createProfit()
    {
        $title = "Add Profits";
        $stores = Store::all();
        return view('admin.finance.profit.add', compact('stores', 'title'));
    }

    public function storeProfit(Request $request)
    {
        $request->validate([
            'store_id' => 'required|integer',
            'total_income' => 'required|numeric',
            'total_expense' => 'required|numeric',
        ]);

        $profit = new Profit();
        $profit->store_id = $request->store_id;
        $profit->total_income = $request->total_income;
        $profit->total_expense = $request->total_expense;
        $profit->net_profit = $request->net_profit;

        // net_profit will be auto-calculated if it's a generated column in database
        $profit->save();

        return redirect()->route('profit.list')->with('success', 'Profit added successfully.');
    }

    public function editProfit($id)
    {
        $profit = Profit::with('store')->findOrFail($id);
        $stores = Store::all();
        return view('admin.finance.profit.add', compact('profit', 'stores'));
    }

    public function updateProfit(Request $request, $id)
    {
        $request->validate([
            'store_id' => 'required|integer',
            'total_income' => 'required|numeric',
            'total_expense' => 'required|numeric',
        ]);

        $profit = Profit::findOrFail($id);
        $profit->store_id = $request->store_id;
        $profit->total_income = $request->total_income;
        $profit->total_expense = $request->total_expense;
        $profit->net_profit = $request->net_profit;
        // net_profit will auto-update in DB if generated
        $profit->save();

        return redirect()->route('profit.list')->with('success', 'Profit updated successfully.');
    }

    public function deleteProfit($id)
    {
        $profit = Profit::findOrFail($id);
        $profit->delete();

        return redirect()->route('profit.list')->with('success', 'Profit deleted successfully.');
    }
}
