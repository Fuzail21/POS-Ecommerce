<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Setting;
use App\Models\SalesReturn;
use App\Models\ReturnPurchase;
use App\Models\PurchaseReturn;
use App\Models\SalesReturnItem;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Auth;


class POSController extends Controller
{
    // Method 1: Open Register
    public function openRegister(Request $request){
        $request->validate(['opening_cash' => 'required|numeric|min:0']);

        // Prevent multiple open registers
        $existingRegister = CashRegister::where('user_id', auth()->id())
                                        ->whereNull('closed_at')
                                        ->first();

        if ($existingRegister) {
            return response()->json([
                'success' => false,
                'message' => 'A register is already open.'
            ], 409);
        }

        $register = CashRegister::create([
            'user_id' => auth()->id(),
            'opening_cash' => $request->opening_cash,
            'opened_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('pos.index')
        ]);
    }

    // Method 2: Close Register
    public function closeRegister() {
        $userId = auth()->id();
    
        $register = CashRegister::where('user_id', $userId)
                ->whereNull('closed_at')
                ->latest()
                ->first();
    
        if (!$register) {
            return response()->json([
                'success' => false,
                'message' => 'Register is already closed.'
            ], 400);
        }
    
        $start = $register->opened_at;
        $end = now();
    
        $salesTotal = Sale::where('created_by', $userId)
                          ->whereBetween('created_at', [$start, $end])
                          ->sum('final_amount');
    
        $expenseTotal = Expense::where('created_by', $userId)
                               ->whereBetween('created_at', [$start, $end])
                               ->sum('amount');
    
        $closingCash = $register->opening_cash + $salesTotal - $expenseTotal;
    
        $register->update([
            'total_sales'     => $salesTotal,
            'total_expense'   => $expenseTotal,
            'closing_cash'    => $closingCash,
            'cash_difference' => $closingCash - $register->opening_cash,
            'closed_at'       => $end,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Register closed successfully.',
            'redirect' => route('dashboard'),
        ]);
    }

    // Method 3: Check if Register is Open
    public function checkRegister(){
        $isOpen = CashRegister::where('user_id', auth()->id())
                              ->whereNull('closed_at')
                              ->exists();

        return response()->json(['open' => $isOpen]);
    }

    // Method 4: Get Register Details
    public function getRegisterDetails(Request $request){
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized: Please log in to view register details.',
                'details' => null
            ], 401);
        }

        $userId = Auth::id();

        $register = CashRegister::where('user_id', $userId)
                                ->whereNull('closed_at')
                                ->first();

        if (!$register) {
            $register = CashRegister::where('user_id', $userId)
                                    ->whereNotNull('closed_at')
                                    ->orderBy('closed_at', 'desc')
                                    ->first();
        }

        if (!$register) {
            return response()->json([
                'message' => 'No register found.',
                'details' => [
                    'payment_type' => [
                        'Cash In Hand'   => '$ 0.00',
                        'Cash'           => '$ 0.00',
                        'Card'           => '$ 0.00',
                        'Bank'           => '$ 0.00',
                    ],
                    'total_sales'   => '$ 0.00',
                    'total_refund'  => '$ 0.00',
                    'total_payment' => '$ 0.00',
                    'date' => now()->format('F jS Y'),
                ]
            ], 404);
        }

        $registerStatus = $register->closed_at ? 'closed' : 'open';
        $start = Carbon::parse($register->opened_at);
        $end = $register->closed_at ? Carbon::parse($register->closed_at) : now();

        $paymentTypeTotals = [
            'cash' => 0,
            'card' => 0,
            'bank' => 0,
        ];
        $totalSales = 0;
        $totalRefund = 0;

        $payments = Payment::where('created_by', $userId)
                            ->whereBetween('created_at', [$start, $end])
                            ->get();

        foreach ($payments as $payment) {
            $amount = (float) $payment->amount;
            $method = strtolower($payment->payment_method);

            switch ($payment->ref_type) {
                case 'sale':
                    $totalSales += $amount;
                    if (isset($paymentTypeTotals[$method])) {
                        $paymentTypeTotals[$method] += $amount;
                    } else {
                        $paymentTypeTotals['other'] = ($paymentTypeTotals['other'] ?? 0) + $amount;
                    }
                    break;

                case 'sales_return':
                    $totalRefund += $amount;
                    break;

                case 'payment':
                    if (isset($paymentTypeTotals[$method])) {
                        $paymentTypeTotals[$method] += $amount;
                    } else {
                        $paymentTypeTotals['other'] = ($paymentTypeTotals['other'] ?? 0) + $amount;
                    }
                    break;
            }
        }

        $totalPayment = $register->opening_cash + $totalSales - $totalRefund;
        $format = fn($amt) => '$ ' . number_format($amt, 2);

        return response()->json([
            'message' => 'Register details fetched successfully.',
            'details' => [
                'payment_type' => [
                    'Cash In Hand' => $format($register->opening_cash),
                    'Cash'         => $format($paymentTypeTotals['cash']),
                    'Card'         => $format($paymentTypeTotals['card']),
                    'Bank'         => $format($paymentTypeTotals['bank']),
                ],
                'total_sales'   => $format($totalSales),
                'total_refund'  => $format($totalRefund),
                'total_payment' => $format($totalPayment),
                'date' => $registerStatus === 'open'
                            ? now()->format('F jS Y')
                            : Carbon::parse($register->closed_at)->format('F jS Y'),
            ]
        ]);
    }

    // Method 5: Dashboard
    public function dashboard(Request $request)
    {
        $title = "Dashboard";

        // Get date range from request or set defaults
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::today()->startOfDay();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();

        $hasDateFilter = $request->filled('start_date') && $request->filled('end_date');

        // Total Sales — query sales table directly (final_amount = after discount/tax)
        $sales = Sale::when($hasDateFilter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                     ->sum('final_amount');

        // Total Purchases — query purchases table directly
        $purchases = Purchase::when($hasDateFilter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                              ->sum('total_amount');

        // Sales Returns — query sales_returns table directly
        $salesReturns = SalesReturn::when($hasDateFilter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                                    ->sum('total_return_amount');

        // Purchase Returns — query purchase_returns table
        $purchaseReturns = PurchaseReturn::when($hasDateFilter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                                          ->sum('total_return_amount');

        // Today's Sales — from sales table
        $todaySales = Sale::whereDate('created_at', Carbon::today())->sum('final_amount');

        // Today's Received — actual paid amount from today's sales
        $todayReceived = Sale::whereDate('created_at', Carbon::today())->sum('paid_amount');

        // Today's Purchases — from purchases table
        $todayPurchases = Purchase::whereDate('created_at', Carbon::today())->sum('total_amount');

        // Today's Expense - always for today
        $todayExpense = Expense::whereDate('created_at', Carbon::today())->sum('amount');

        // Today's Purchase Payments (separate for dashboard card)
        $todayPurchasePayments = $todayPurchases;


        // --- Data for Charts and Recent Sales ---

        // 1. This Week Sales & Purchases (always for the current week, not affected by the date filter)
        $startOfWeek = Carbon::now()->startOfWeek(); // Monday
        $endOfWeek = Carbon::now()->endOfWeek();     // Sunday
        $weeklySalesPurchases = [];
        $currentDate = clone $startOfWeek;
        while ($currentDate->lessThanOrEqualTo($endOfWeek)) {
            $date = $currentDate->toDateString();
            $dailySales     = Sale::whereDate('created_at', $date)->sum('final_amount');
            $dailyPurchases = Purchase::whereDate('created_at', $date)->sum('total_amount');

            $weeklySalesPurchases[] = [
                'date' => $currentDate->format('Y-m-d'),
                'sales' => (float) $dailySales,
                'purchases' => (float) $dailyPurchases,
            ];
            $currentDate->addDay();
        }

        $weekDates = array_column($weeklySalesPurchases, 'date');
        $weekSalesData = array_column($weeklySalesPurchases, 'sales');
        $weekPurchasesData = array_column($weeklySalesPurchases, 'purchases');


        // 2. Top Selling Products (Current Month) - Now includes variant name if available
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $topSellingProductsWithTotal = SaleItem::select('sale_items.product_id', 'sale_items.variant_id')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity')
            ->selectRaw('SUM(sale_items.quantity * products.actual_price) as grand_total_amount') // Using product sale price, adjust if variant price_adjustment affects this
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            // Conditionally join product_variants if product_variant_id is not null
            ->leftJoin('product_variants', 'sale_items.variant_id', '=', 'product_variants.id')
            ->whereHas('sale', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            })
            ->with(['product', 'variant']) // Eager load product and variant relationships
            ->groupBy('sale_items.product_id', 'sale_items.variant_id') // Group by both product and variant
            ->orderByDesc('total_quantity')
            ->limit(2)
            ->get();

        // Fetch setting for currency symbol
        $setting = Setting::first();

        // Refined data for Top Selling Products - Concatenate product and variant name
        $topProductNames = $topSellingProductsWithTotal->map(function($item) {
            $productName = $item->product->name ?? 'Unknown Product';
            $variantName = $item->variant->variant_name ?? null; // Get variant name
            return $variantName ? "{$productName} ({$variantName})" : $productName;
        })->toArray();

        $topProductQuantities = $topSellingProductsWithTotal->pluck('total_quantity')->map(function($quantity) {
            return (float) $quantity;
        })->toArray();

        $topProductTableData = $topSellingProductsWithTotal->map(function ($item) use ($setting) {
            $productName = $item->product->name ?? 'Unknown Product';
            $variantName = $item->variant->name ?? null; // This gets the variant name if available
            $fullProductName = $variantName ? "{$productName} ({$variantName})" : $productName; // Combines them

            return [
                'product_name' => $fullProductName, // This key now holds "Product Name (Variant Name)"
                'quantity' => (float) $item->total_quantity,
                'grand_total' => $setting->currency_symbol . ' ' . number_format((float) $item->grand_total_amount, 2),
            ];
        });


        // 3. Top Customers (Current Month) - Unchanged, still limited to 2
        $topCustomers = Sale::selectRaw('customer_id, SUM(total_amount) as total_spent')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(2)
            ->get();

        // Refined data for Top Customers
        $topCustomerNames = $topCustomers->map(function($customer) {
            return $customer->customer->name ?? 'Walk-in Customer';
        })->toArray();

        $topCustomerAmounts = $topCustomers->pluck('total_spent')->map(function($amount) {
            return (float) $amount;
        })->toArray();


        // 4. Recent Sales - affected by the date range filter, now includes variant name
        $recentSales = Sale::with(['customer', 'items.product', 'items.variant']) // Eager load 'items.variant'
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // 5. Low Stock Products Alert
        // Adjusted the query to specifically fetch products or variants that are low in stock
        $stockAlertProducts = Product::with([
            'category',
            'baseUnit',
            'variants.inventoryStock.warehouse',
            'variants.displayUnit',
            'inventoryStock.warehouse',
        ])->where(function ($query) {
            // Condition for products without variants
            $query->where(function ($subQuery) {
                $subQuery->whereDoesntHave('variants')
                         ->where(function ($noVariantSubQuery) {
                             $noVariantSubQuery->whereHas('inventoryStock', function ($invStockQuery) {
                         $invStockQuery->whereColumn('quantity_in_base_unit', '<=', 'products.low_stock');
                             })
                             ->orWhereDoesntHave('inventoryStock'); // Products with no inventory stock at all are considered low
                         });
            });
        
            // Condition for products with variants
            $query->orWhere(function ($subQuery) {
                $subQuery->whereHas('variants', function ($variantQuery) {
                    $variantQuery->where(function ($variantHasQuery) {
                        $variantHasQuery->whereHas('inventoryStock', function ($invStockQuery) {
                            $invStockQuery->whereColumn('quantity_in_base_unit', '<=', 'product_variants.low_stock');
                        })
                        ->orWhereDoesntHave('inventoryStock'); // Variants with no inventory stock are considered low
                    });
                });
            });
        })->paginate(5);
        
        return view('dashboard', compact(
            'title',
            'sales',
            'purchases',
            'salesReturns',
            'purchaseReturns',
            'todaySales',
            'todayReceived',
            'todayPurchases',
            'todayExpense',
            'todayPurchasePayments',
            'weekDates',
            'weekSalesData',
            'weekPurchasesData',
            'topProductNames',
            'topProductQuantities',
            'topProductTableData',
            'topCustomerNames',
            'topCustomerAmounts',
            'recentSales',
            'stockAlertProducts',
            'setting'
        ));
    }

}
