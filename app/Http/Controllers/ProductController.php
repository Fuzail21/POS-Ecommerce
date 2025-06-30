<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;

class ProductController extends Controller
{
    public function index(){
        $title = 'Products List';
        $products = Product::paginate(10);
        return view('admin.product.list', compact('products', 'title'));
    }

    public function create(){
        $title = 'Add Product';
        $categories = Category::whereNotNull('parent_id')
            ->orWhereDoesntHave('children')
            ->get();
        $units = Unit::all();
        $suppliers = Supplier::all();
        return view('admin.product.form', compact('title', 'categories', 'units', 'suppliers'));
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
            'low_stock' => 'nullable|numeric',
            // 'track_expiry' => 'required|boolean',
            // 'tax_rate' => 'required|numeric|between:0,100.00',
            'sale_price' => 'required|numeric|min:0',
            'product_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'supplier_ids' => 'nullable|array',
            'supplier_ids.*' => 'exists:suppliers,id',
        ]);

        $product = new Product();
        $product->fill($validatedData);
        $product->default_display_unit_id = $validatedData['base_unit_id'];

        // Handle main product image
        if ($request->hasFile('product_img')) {
            $imagePath = $request->file('product_img')->store('products', 'public');
            $product->product_img = $imagePath;
        }

        $product->save();

        if ($request->filled('supplier_ids')) {
            $product->suppliers()->sync($request->supplier_ids);
        }


        // Save product variants
        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variantData) {
                $variantImagePath = null;
                        
                if ($request->hasFile("variants.$index.product_img")) {
                    $variantImagePath = $request->file("variants.$index.product_img")
                                                ->store('products/variants', 'public');
                }
            
                $product->variants()->create([
                    'variant_name' => $variantData['variant_name'],
                    'sku' => $variantData['sku'],
                    'barcode' => $variantData['barcode'] ?? null,
                    'sale_price' => $variantData['sale_price'] ?? 0,
                    'low_stock' => $variantData['low_stock'] ?? 0,
                    'product_img' => $variantImagePath,
                ]);
            }

        }

        return redirect()->route('products.list')->with('success', 'Product created successfully.');
    }

    public function edit($id) {
        $title = 'Edit Product';
        $categories = Category::whereNotNull('parent_id')
            ->orWhereDoesntHave('children')
            ->get();
        $units = Unit::all();
        $product = Product::with('suppliers')->findOrFail($id); // Load attached suppliers
        $suppliers = Supplier::all(); // Load all suppliers

        return view('admin.product.form', compact('product', 'title', 'categories', 'units', 'suppliers'));
    }

    public function update(Request $request, $id){
        $product = Product::findOrFail($id);

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
            'low_stock' => 'nullable|numeric',
            // 'track_expiry' => 'required|boolean',
            // 'tax_rate' => 'required|numeric|between:0,100.00',
            'sale_price' => 'required|numeric|min:0',
            'product_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'supplier_ids' => 'nullable|array',
            'supplier_ids.*' => 'exists:suppliers,id',

        ]);

         $product->fill($validatedData);

        // Handle main product image upload with error handling
        if ($request->hasFile('product_img')) {
            try {
                $imagePath = $request->file('product_img')->store('products', 'public');
                if (!$imagePath) {
                    // Storage failed, add error message
                    return back()->withInput()->withErrors(['product_img' => 'Failed to upload product image. Please try again.']);
                }
                $product->product_img = $imagePath;
            } catch (\Exception $e) {
                // Exception during upload, add error message
                return back()->withInput()->withErrors(['product_img' => 'Error uploading image: ' . $e->getMessage()]);
            }
        }

        $product->save();

        if ($request->filled('supplier_ids')) {
            $product->suppliers()->sync($request->supplier_ids);
        }

        // Handle variants
        if ($request->has_variants && $request->has('variants')) {
            $incomingVariants = collect($request->input('variants'));
            $existingVariants = $product->variants()->get()->keyBy('sku'); // Assuming SKU is unique

            $existingSkus = $existingVariants->keys();
            $incomingSkus = $incomingVariants->pluck('sku');

            foreach ($incomingVariants as $index => $variantData) {
                $sku = $variantData['sku'];

                // Skip if SKU is missing
                if (!$sku) continue;

                $variant = $existingVariants->get($sku);

                if ($variant) {
                    // Update only if something changed
                    $needsUpdate = (
                        $variant->variant_name !== $variantData['variant_name'] ||
                        $variant->barcode !== ($variantData['barcode'] ?? null) ||
                        $variant->sale_price != ($variantData['sale_price'] ?? 0) ||
                        $variant->low_stock != ($variantData['low_stock'] ?? 0)

                    );

                    if ($needsUpdate) {
                        $variant->update([
                            'variant_name' => $variantData['variant_name'],
                            'barcode' => $variantData['barcode'] ?? null,
                            'sale_price' => $variantData['sale_price'] ?? 0,
                            'low_stock' => $variantData['low_stock'] ?? 0,

                        ]);
                    }

                    // Handle image update
                    if ($request->hasFile("variants.{$index}.product_img")) {
                        $image = $request->file("variants.{$index}.product_img");
                        $path = $image->store('products/variants', 'public');
                        $variant->update(['product_img' => $path]);
                    }

                } else {
                    // New variant – check for unique SKU
                    if (!ProductVariant::where('sku', $sku)->exists()) {
                        $newVariant = new ProductVariant([
                            'variant_name' => $variantData['variant_name'],
                            'sku' => $sku,
                            'barcode' => $variantData['barcode'] ?? null,
                            'sale_price' => $variantData['sale_price'] ?? 0,
                            'low_stock' => $variantData['low_stock'] ?? 0,
                        ]);

                        // Handle image
                        if ($request->hasFile("variants.{$index}.product_img")) {
                            $image = $request->file("variants.{$index}.product_img");
                            $path = $image->store('products/variants', 'public');
                            $newVariant->product_img = $path;
                        }

                        $product->variants()->save($newVariant);
                    }
                }
            }

            // Optional: Delete variants no longer in the request
            $skusToKeep = $incomingSkus->toArray();
            $product->variants()->whereNotIn('sku', $skusToKeep)->delete();
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

    public function search(Request $request)
    {
        $query = $request->input('q');
    
        $products = Product::with(['variants:id,product_id,variant_name', 'baseUnit:id,name'])
            ->where('name', 'like', '%' . $query . '%')
            ->limit(20)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'unit' => $product->baseUnit->name ?? '',
                    'unit_id' => $product->base_unit_id,
                    'variants' => $product->variants->map(fn($v) => ['id' => $v->id, 'name' => $v->variant_name])
                ];
            });
        
        return response()->json($products);
    }

}

