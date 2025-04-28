<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Company;
use App\Models\Store;
use App\Models\Customer;
use App\Models\Category;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category:id,name', 'company:id,name', 'store:id,name'])
            ->select('id', 'name', 'category_id', 'company_id', 'store_id', 'stock_quantity', 'purchase_price', 'selling_price', 'expiry_date')
            ->paginate(20);
        $title = 'Products List';
        return view('admin.product.list', compact('products', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stores = Store::all();
        $companies = Company::all();
        $categories = Category::all();
        $title = 'Add New Product';
        return view('admin.product.add', compact('title', 'companies', 'categories', 'stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try 
        {
            // Validate form input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'company_id' => 'required|exists:companies,id',
                'store_id' => 'required|exists:stores,id',
                'purchase_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|numeric|min:0',
                'expiry_date' => 'required|date',
            ]);
        
            // Create product instance
            $product = new Product();
            $product->name = $validated['name'];
            $product->category_id = $validated['category_id'];
            $product->company_id = $validated['company_id'];
            $product->store_id = $validated['store_id'];
            $product->purchase_price = $validated['purchase_price'];
            $product->selling_price = $validated['selling_price'];
            $product->stock_quantity = $validated['stock_quantity'];
            $product->expiry_date = $validated['expiry_date'];
        
            // Save product
            $product->save();

            return redirect()->route('product.list')->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            return redirect()->route('product.list')->with('error', 'Something went wrong! Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Find product
        $product = Product::findOrFail($id);

        // Fetch dropdown data
        $categories = Category::all();
        $companies = Company::all();
        $stores = Store::all();
        $title = 'Edit Product';

        // Return view with data
        return view('admin.product.edit', compact('title', 'product', 'categories', 'companies', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try 
        {
            // Validate form input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'company_id' => 'required|exists:companies,id',
                'store_id' => 'required|exists:stores,id',
                'purchase_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|numeric|min:0',
                'expiry_date' => 'required|date',
            ]);
        
            // Create product instance
            $product = Product::findOrFail($id);
            $product->name = $validated['name'];
            $product->category_id = $validated['category_id'];
            $product->company_id = $validated['company_id'];
            $product->store_id = $validated['store_id'];
            $product->purchase_price = $validated['purchase_price'];
            $product->selling_price = $validated['selling_price'];
            $product->stock_quantity = $validated['stock_quantity'];
            $product->expiry_date = $validated['expiry_date'];
        
            // Save product
            $product->save();

            return redirect()->route('product.list')->with('success', 'Product update successfully!');
        } catch (\Exception $e) {
            return redirect()->route('product.list')->with('error', 'Something went wrong! Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if ($product) {
            // Delete the store
            $product->delete();
    
            // Redirect back with a success message
            return redirect()->route('product.list')->with('success', 'Product deleted successfully!');
        } else {
            // Redirect back with an error message if store not found
            return redirect()->route('product.list')->with('error', 'Product not found!');
        }
    }
}
