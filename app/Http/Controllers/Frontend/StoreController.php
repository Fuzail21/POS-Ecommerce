<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\DiscountRule;


class StoreController extends Controller
{
    public function landing(){
        $categories = Category::all();

        // Fetch active discount rules only once
        $now = now();
        $discountRules = DiscountRule::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        $products = Product::latest()
            ->take(8)
            ->with([
                'inventoryStocks',
                'baseUnit',
                'variants.inventoryStocks',
                'variants.product.baseUnit'
            ])
            ->get()
            ->map(function ($product) use ($discountRules) {
                // Calculate stock
                $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
                $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                $product->stock_quantity = $baseQuantity / $conversionFactor;
                $product->in_stock = $product->stock_quantity > 0;

                // Default price
                $product->final_price = $product->actual_price;
                $product->has_discount = false;

                // Find applicable discount rule
                $applicableRule = $discountRules->first(function ($rule) use ($product) {
                    $targetIds = json_decode($rule->target_ids ?? '[]');
                    if ($rule->type === 'product') {
                        return in_array($product->id, $targetIds);
                    } elseif ($rule->type === 'category') {
                        return in_array($product->category_id, $targetIds);
                    }
                    return false;
                });

                // Apply discount if found
                if ($applicableRule) {
                    $product->final_price = $product->actual_price - ($product->actual_price * ($applicableRule->discount / 100));
                    $product->has_discount = true;
                }

                // Variants
                foreach ($product->variants as $variant) {
                    $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
                    $variantQuantity = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                    $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
                    $variant->in_stock = $variant->stock_quantity > 0;

                    $variant->final_price = $variant->actual_price;
                    $variant->has_discount = false;

                    if ($applicableRule) {
                        $variant->final_price = $variant->actual_price - ($variant->actual_price * ($applicableRule->discount / 100));
                        $variant->has_discount = true;
                    }
                }

                return $product;
            });

        return view('store.landing', compact('categories', 'products'));
    }

    // public function shop(Request $request){
    //     $categoryId = $request->query('category');

    //     $productsQuery = Product::with([
    //         'inventoryStocks',
    //         'baseUnit',
    //         'variants.inventoryStocks',
    //         'variants.product.baseUnit'
    //     ])->latest();

    //     // 🔍 If category filter is applied
    //     if ($categoryId) {
    //         $productsQuery->where('category_id', $categoryId);
    //     }

    //     $products = $productsQuery->paginate(12)->withQueryString(); // Keeps ?category in pagination links

    //     // Load top-level categories with product count
    //     $categories = Category::withCount('products')
    //                           ->whereNotNull('parent_id')
    //                           ->get();

    //     // Optional: Compute stock quantities
    //     $products->getCollection()->transform(function ($product) {
    //         $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
    //         $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //         $product->stock_quantity = $baseQuantity / $conversionFactor;
    //         $product->in_stock = $product->stock_quantity > 0;

    //         foreach ($product->variants as $variant) {
    //             $variantConversion = $variant->product->baseUnit->conversion_factor ?? 1;
    //             $variantQty = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
    //             $variant->stock_quantity = $variantQty / $variantConversion;
    //             $variant->in_stock = $variant->stock_quantity > 0;
    //         }

    //         return $product;
    //     });

    //     return view('store.shop', compact('products', 'categories'));
    // }

    public function shop(Request $request){
        $categoryId = $request->query('category');
    
        // 🟡 Load active discount rules once
        $now = now();
        $discountRules = DiscountRule::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();
    
        $productsQuery = Product::with([
            'inventoryStocks',
            'baseUnit',
            'variants.inventoryStocks',
            'variants.product.baseUnit'
        ])->latest();
        
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }
    
        $products = $productsQuery->paginate(12)->withQueryString();
    
        $categories = Category::withCount('products')
            ->whereNotNull('parent_id')
            ->get();
    
        // 🔁 Transform each product to calculate stock + discount
        $products->getCollection()->transform(function ($product) use ($discountRules) {
            // 🧮 Stock calculation
            $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
            $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
            $product->stock_quantity = $baseQuantity / $conversionFactor;
            $product->in_stock = $product->stock_quantity > 0;
        
            // 💸 Default pricing
            $product->final_price = $product->actual_price;
            $product->has_discount = false;
        
            // 🔍 Check if a discount rule applies
            $applicableRule = $discountRules->first(function ($rule) use ($product) {
                $targetIds = json_decode($rule->target_ids ?? '[]');
                if ($rule->type === 'product') {
                    return in_array($product->id, $targetIds);
                } elseif ($rule->type === 'category') {
                    return in_array($product->category_id, $targetIds);
                }
                return false;
            });
        
            // ✅ Apply discount
            if ($applicableRule) {
                $product->final_price = $product->actual_price - ($product->actual_price * ($applicableRule->discount / 100));
                $product->has_discount = true;
            }
        
            // ♻️ Variant stock and discount
            foreach ($product->variants as $variant) {
                $variantConversion = $variant->product->baseUnit->conversion_factor ?? 1;
                $variantQty = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
                $variant->stock_quantity = $variantQty / $variantConversion;
                $variant->in_stock = $variant->stock_quantity > 0;
            
                $variant->final_price = $variant->actual_price;
                $variant->has_discount = false;
            
                if ($applicableRule) {
                    $variant->final_price = $variant->actual_price - ($variant->actual_price * ($applicableRule->discount / 100));
                    $variant->has_discount = true;
                }
            }
        
            return $product;
        });
    
        return view('store.shop', compact('products', 'categories'));
    }

    public function product($id)
    {
        // Fetch active discount rules
        $now = now();
        $discountRules = DiscountRule::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        $product = Product::with([
                'inventoryStocks',
                'baseUnit',
                'displayUnit', // Ensure this is loaded if you use it for base product
                'category',    // Ensure this is loaded for category discounts
                'variants.inventoryStocks',
                'variants.product.baseUnit'
            ])->find($id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        // Apply calculations to the single product as done in the landing method
        $conversionFactor = $product->baseUnit->conversion_factor ?? 1;
        $baseQuantity = $product->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
        $product->stock_quantity = $baseQuantity / $conversionFactor;
        $product->in_stock = $product->stock_quantity > 0;

        // Default price
        $product->final_price = $product->actual_price;
        $product->has_discount = false;

        // Find applicable discount rule for the product
        $applicableRule = $discountRules->first(function ($rule) use ($product) {
            $targetIds = json_decode($rule->target_ids ?? '[]');
            if ($rule->type === 'product') {
                return in_array($product->id, $targetIds);
            } elseif ($rule->type === 'category') {
                return in_array($product->category_id, $targetIds);
            }
            return false;
        });

        // Apply discount if found
        if ($applicableRule) {
            $product->final_price = $product->actual_price - ($product->actual_price * ($applicableRule->discount / 100));
            $product->has_discount = true;
        }

        // Variants
        foreach ($product->variants as $variant) {
            $variantConversionFactor = $variant->product->baseUnit->conversion_factor ?? 1;
            $variantQuantity = $variant->inventoryStocks->sum('quantity_in_base_unit') ?? 0;
            $variant->stock_quantity = $variantQuantity / $variantConversionFactor;
            $variant->in_stock = $variant->stock_quantity > 0;

            // Variants price logic should be based on their own actual_price, not product's base price
            // Assuming ProductVariant model has an actual_price field
            $variant->final_price = $variant->actual_price;
            $variant->has_discount = false;

            if ($applicableRule) {
                // Apply same discount percentage to variant's actual_price
                $variant->final_price = $variant->actual_price - ($variant->actual_price * ($applicableRule->discount / 100));
                $variant->has_discount = true;
            }
        }

        return view('store.product', compact('product')); // Make sure this path is correct
    }

}
