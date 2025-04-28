<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\User;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('store')->latest()->paginate(20);
        $title = "Expenses List";
        return view('admin.expense.list', compact('expenses', 'title'));
    }

    public function create()
    {
        $stores = Store::all();
        $title = "Add Expense";
        return view('admin.expense.add', compact('stores', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'expense_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense = new Expense();
        $expense->store_id = $request->store_id;
        $expense->expense_type = $request->expense_type;
        $expense->amount = $request->amount;
        $expense->save();

        return redirect()->route('expense.list')->with('success', 'Expense added successfully.');
    }

    public function edit($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return redirect()->route('expense.list')->with('error', 'Expense not found.');
        }

        $stores = Store::all();
        $title = "Edit Expense";
        return view('admin.expense.add', compact('expense', 'stores', 'title'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return redirect()->route('expense.list')->with('error', 'Expense not found.');
        }

        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'expense_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense->store_id = $request->store_id;
        $expense->expense_type = $request->expense_type;
        $expense->amount = $request->amount;
        $expense->save();

        return redirect()->route('expense.list')->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return redirect()->route('expense.list')->with('error', 'Expense not found.');
        }

        $expense->delete();

        return redirect()->route('expense.list')->with('success', 'Expense deleted successfully.');
    }
}
