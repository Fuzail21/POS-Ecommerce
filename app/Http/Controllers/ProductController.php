<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(){
        $title = 'Products List';
        $products = Product::paginate(20);
        return view('admin.product.list', compact('products', 'title'));
    }

    public function create(){
        $title = 'Add Product';
        $categories = Category::where('parent_id', '!=', NULL)->get();
        $units = Unit::all();
        return view('admin.product.form', compact('title', 'categories', 'units'));
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'base_unit_id' => 'required|exists:units,id',
            'has_variants' => 'required|boolean',
            'sku' => 'required|unique:products,sku',
            'barcode' => 'nullable|unique:products,barcode',
            'brand' => 'nullable|string|max:255',
            'track_expiry' => 'required|boolean',
            'tax_rate' => 'required|numeric|between:0,100.00',
            'product_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = new Product();
        $product->fill($validatedData);
        $product->default_display_unit_id = $validatedData['base_unit_id']; // ✅ set here
        $product->save();

        // Handle image upload
        if ($request->hasFile('product_img')) {
            $imagePath = $request->file('product_img')->store('products', 'public');
            $product->product_img = $imagePath;
        }

        $product->save();

        // Save variants
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                $product->variants()->create([
                    'variant_name' => $variantData['variant_name'],
                    'sku' => $variantData['sku'],
                    'barcode' => $variantData['barcode'] ?? null,
                ]);
            }
        }

        return redirect()->route('products.list')->with('success', 'Product created successfully.');
    }

    public function edit($id) {
        $title = 'Edit Product';
        $categories = Category::where('parent_id', '!=', NULL)->get();
        $units = Unit::all();
        $product = Product::findOrFail($id);
        return view('admin.product.form', compact('product', 'title', 'categories', 'units'));
    }

    public function update(Request $request, $id){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'base_unit_id' => 'required|exists:units,id',
            'has_variants' => 'required|boolean',
            'sku' => [
                'required',
                Rule::unique('products')->ignore($id)->whereNull('deleted_at'),
            ],
            'barcode' => [
                'nullable',
                Rule::unique('products')->ignore($id)->whereNull('deleted_at'),
            ],
            'brand' => 'nullable|string|max:255',
            'track_expiry' => 'required|boolean',
            'tax_rate' => 'required|numeric|between:0,100.00',
            'product_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $product->fill($validatedData);
        $product->default_display_unit_id = $validatedData['base_unit_id'];

        // Handle image update
        if ($request->hasFile('product_img')) {
            if (!empty($product->product_img) && Storage::disk('public')->exists($product->product_img)) {
                Storage::disk('public')->delete($product->product_img);
            }

            $imagePath = $request->file('product_img')->store('products', 'public');
            $product->product_img = $imagePath;
        }

        $product->save();

        // Handle variants based on has_variants flag
        if ($validatedData['has_variants'] == 1) {
            // Hard delete old variants (including soft deleted)
            $product->variants()->withTrashed()->forceDelete();

            // Add new variants
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $product->variants()->create([
                        'variant_name' => $variantData['variant_name'],
                        'sku' => $variantData['sku'],
                        'barcode' => $variantData['barcode'] ?? null,
                    ]);
                }
            }
        } else {
            // Hard delete all variants if no longer a variant product
            $product->variants()->withTrashed()->forceDelete();
        }

        return redirect()->route('products.list')->with('success', 'Product updated successfully.');
    }

    public function destroy($id){
        $product = Product::findOrFail($id);
        if (!empty($product->product_img) && Storage::disk('public')->exists($product->product_img)) {
            Storage::disk('public')->delete($product->product_img);
        }
        $product->variants()->withTrashed()->forceDelete();
        $product->delete();
        return redirect()->route('products.list')->with('success', 'Product deleted successfully.');
    }

    public function viewVariants($id){
        $title = 'Product Varients';
        $product = Product::with('variants')->findOrFail($id);
        return view('admin.product.variants', compact('product', 'title'));
    }
}

