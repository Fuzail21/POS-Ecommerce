<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;


class POSController extends Controller
{
    public function openRegister(Request $request)
    {
        $request->validate(['opening_cash' => 'required|numeric|min:0']);

        $register = CashRegister::create([
            'user_id' => auth()->id(),
            'opening_cash' => $request->opening_cash,
            'opened_at' => now(),
        ]);

        session(['register_id' => $register->id]);

        return response()->json([
            'success' => true,
            'redirect' => route('pos.index')
        ]);

    }

    public function closeRegister()
    {
        $register = CashRegister::findOrFail(session('register_id'));

        $salesTotal = Sale::whereDate('created_at', today())
                          ->where('user_id', auth()->id())
                          ->sum('final_amount');

        $expenseTotal = Expense::whereDate('created_at', today())
                                ->where('user_id', auth()->id())
                                ->sum('amount');

        $closingCash = $register->opening_cash + $salesTotal - $expenseTotal;

        $register->update([
            'total_sales' => $salesTotal,
            'total_expense' => $expenseTotal,
            'closing_cash' => $closingCash,
            'cash_difference' => $closingCash - $register->opening_cash,
            'closed_at' => now(),
        ]);

        session()->forget('register_id');
        return redirect()->route('dashboard')->with('success', 'Register closed successfully.');
    }

    public function checkRegister()
    {
        return response()->json(['open' => session()->has('register_id')]);
    }
}
