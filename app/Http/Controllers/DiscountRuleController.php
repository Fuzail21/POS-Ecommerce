<?php

namespace App\Http\Controllers;

use App\Models\DiscountRule;
use App\Models\Category; // Make sure Category is imported
use App\Models\Product;  // Make sure Product is imported
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountRuleController extends Controller
{
    public function index(){
        $title = "Discount Offers";
        $discountRules = DiscountRule::latest()->paginate(10);
        return view('admin.discount_rules.list', compact('discountRules', 'title'));
    }

    public function create(){
        $title = "Add Discount Offers";
        $products = Product::whereNull('deleted_at')->get();
        $categories = Category::whereNotNull('parent_id')->get(); // Assuming parent_id means subcategories
        $data = compact('title');
        return view('admin.discount_rules.form', [
            'rule' => new DiscountRule(),
            'products' => $products,
            'categories' => $categories,
            'edit' => false
        ])->with($data);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Update 'type' validation to include 'coupon'
            'type' => ['required', Rule::in(['product', 'category', 'coupon'])],
            // 'coupon_code' is required ONLY if 'type' is 'coupon'
            'coupon_code' => [
                'nullable', // Can be null if type is not 'coupon'
                'string',
                'max:255',
                // Add unique rule for coupon codes, but only if type is coupon and code is provided
                Rule::when($request->input('type') === 'coupon' && $request->filled('coupon_code'), [
                    Rule::unique('discount_rules', 'coupon_code')
                        ->where('type', 'coupon')
                        ->whereNotNull('coupon_code'),
                ]),
                'required_if:type,coupon',
            ],
            // 'target_ids' is nullable as it won't be used for 'coupon' type
            'target_ids' => 'nullable|array',
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        // Handle target_ids based on type
        if ($validated['type'] === 'coupon') {
            $validated['target_ids'] = null;
        } else {
            $validated['target_ids'] = json_encode($validated['target_ids'] ?? []);
        }

        // Ensure coupon_code is null if the type is not 'coupon'
        if ($validated['type'] !== 'coupon') {
            $validated['coupon_code'] = null;
        }

        DiscountRule::create($validated);

        return redirect()->route('discount_rules.index')->with('success', 'Discount rule created.');
    }

    public function edit($id){
        $title = "Edit Discount Offers";
        $rule = DiscountRule::findOrFail($id);
        $products = Product::whereNull('deleted_at')->get();
        $categories = Category::all(); // Fetch all categories for edit, adjust if needed
        $data = compact('title');
        return view('admin.discount_rules.form', [
            'rule' => $rule,
            'products' => $products,
            'categories' => $categories,
            'edit' => true
        ])->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DiscountRule  $discountRule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $discountRule = DiscountRule::find($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Update 'type' validation to include 'coupon'
            'type' => ['required', Rule::in(['product', 'category', 'coupon'])],
            // 'coupon_code' is required ONLY if 'type' is 'coupon'
            'coupon_code' => [
                'nullable',
                'string',
                'max:255',
                // Apply unique rule only if type is 'coupon' AND coupon_code is provided.
                // The ignore($discountRule->id) will handle the current record.
                Rule::when(
                    $request->input('type') === 'coupon' && $request->filled('coupon_code'),
                    [
                        Rule::unique('discount_rules', 'coupon_code')
                            ->where('type', 'coupon')
                            ->whereNotNull('coupon_code')
                            ->ignore($id),
                    ]
                ),
                'required_if:type,coupon',
            ],
            // 'target_ids' is nullable as it won't be used for 'coupon' type
            'target_ids' => 'nullable|array',
            'discount' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        // Handle target_ids based on type
        if ($validated['type'] === 'coupon') {
            $validated['target_ids'] = null;
        } else {
            $validated['target_ids'] = json_encode($validated['target_ids'] ?? []);
        }

        // Ensure coupon_code is null if the type is not 'coupon'
        if ($validated['type'] !== 'coupon') {
            $validated['coupon_code'] = null;
        }

        $discountRule->update($validated);

        return redirect()->route('discount_rules.index')->with('success', 'Discount rule updated.');
    }

    public function destroy($id){
        DiscountRule::destroy($id);
        return redirect()->route('discount_rules.index')->with('success', 'Discount rule deleted.');
    }
}