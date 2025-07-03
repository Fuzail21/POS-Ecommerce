<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class StoreController extends Controller
{
    public function landing() {
        $categories = Category::all();
        $products = Product::latest()->take(8)->get();
        return view('store.landing', compact('categories', 'products'));
    }

    public function shop() {
        $products = Product::paginate(12);
        return view('store.shop', compact('products'));
    }

    public function product($id) {
        $product = Product::find($id)->firstOrFail();
        return view('store.product', compact('product'));
    }

}
