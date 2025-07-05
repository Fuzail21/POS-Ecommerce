<?php

// Controller
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscountRule;
use App\Models\Category;
use App\Models\Product;

class DiscountRuleController extends Controller
{
    public function index()
    {
        $discountRules = DiscountRule::latest()->paginate(10);
        return view('admin.discount_rules.list', compact('discountRules'));
    }

    public function create()
    {
        $products = Product::whereNull('deleted_at')->get();
        $categories = Category::whereNotNull('parent_id')->get();
        return view('admin.discount_rules.form', [
            'rule' => new DiscountRule(),
            'products' => $products,
            'categories' => $categories,
            'edit' => false
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'type' => 'required|in:product,category',
            'target_ids' => 'required|array',
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $validated['target_ids'] = json_encode($validated['target_ids']);

        DiscountRule::create($validated);

        return redirect()->route('discount_rules.index')->with('success', 'Discount rule created.');
    }

    public function edit($id)
    {
        $rule = DiscountRule::findOrFail($id);
        $products = Product::whereNull('deleted_at')->get();
        $categories = Category::all();
        return view('admin.discount_rules.form', [
            'rule' => $rule,
            'products' => $products,
            'categories' => $categories,
            'edit' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        $rule = DiscountRule::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'type' => 'required|in:product,category',
            'target_ids' => 'required|array',
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $validated['target_ids'] = json_encode($validated['target_ids']);

        $rule->update($validated);

        return redirect()->route('discount_rules.index')->with('success', 'Discount rule updated.');
    }

    public function destroy($id)
    {
        DiscountRule::destroy($id);
        return redirect()->route('discount_rules.index')->with('success', 'Discount rule deleted.');
    }
}

