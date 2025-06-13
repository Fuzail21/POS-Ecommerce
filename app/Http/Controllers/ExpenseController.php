<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenseCategories = ExpenseCategory::paginate(20);
        $title = 'Expense Category List';
        return view('admin.expense.expense_categories_list', compact('expenseCategories', 'title'));
    }

    public function store(Request $request)
    {
       $request->validate([
            'name' => 'required|string',
        ]);

        $expenseCategory = new ExpenseCategory();
        $expenseCategory->name = $request->name;
        $expenseCategory->save();

        return redirect()->route('expense_categories.list')->with('success', 'Expense Category added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $expenseCategory = ExpenseCategory::findOrFail($id);
        $expenseCategory->name = $request->name;
        $expenseCategory->save();

        return redirect()->route('expense_categories.list')->with('success', 'Expense Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = ExpenseCategory::findOrFail($id);

        // Check if any expense is using this category
        $isUsed = Expense::where('category_id', $id)->exists();

        if ($isUsed) {
            return redirect()->route('expense_categories.list')->with('error', 'Cannot delete: Category is used in expenses.');
        }

        $category->delete();

        return redirect()->route('expense_categories.list')->with('success', 'Expense Category deleted successfully.');
    }

    //Expense
    public function list()
    {
        $expenses = Expense::with(['category', 'branch', 'creator'])->paginate(20);
        $title = 'Expense List';
        return view('admin.expense.list', compact('expenses', 'title'));
    }


    public function expenseCreate()
    {
        $title = 'Add Expense';
        $branches = Branch::all();
        $categories = ExpenseCategory::all();
        $users = User::all(); // If needed

        return view('admin.expense.create', compact('title', 'branches', 'categories', 'users'));
    }


    public function expenseStore(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'branch_id' => $request->branch_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('expense.list')->with('success', 'Expense added successfully.');
    }

    public function expenseEdit($id)
    {
        $expense = Expense::findOrFail($id);
        $branches = Branch::all();
        $categories = ExpenseCategory::all();
        $title = 'Edit Expense';

        return view('admin.expense.create', compact('expense', 'branches', 'categories', 'title'));
    }

    public function expenseUpdate(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update([
            'branch_id' => $request->branch_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date,
        ]);

        return redirect()->route('expense.list')->with('success', 'Expense updated successfully.');
    }

    public function expenseDestroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->route('expense.list')->with('success', 'Expense deleted successfully.');
    }


}
