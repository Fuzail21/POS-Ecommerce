<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardApiController extends Controller
{
    use ApiResponse;
    /**
     * GET /api/v1/dashboard/stats
     * Key KPIs for the mobile dashboard.
     */
    public function stats(Request $request)
    {
        $today     = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $setting   = Setting::first();

        // Today's sales
        $todaySales = Sale::where('sale_origin', 'POS')
            ->whereDate('sale_date', $today)
            ->sum('final_amount');

        // This month's sales
        $monthlySales = Sale::where('sale_origin', 'POS')
            ->whereBetween('sale_date', [$monthStart, Carbon::now()])
            ->sum('final_amount');

        // Pending e-commerce orders
        $pendingOrders = Sale::where('sale_origin', 'E-commerce')
            ->where('status', 'pending')
            ->count();

        // Monthly purchases
        $monthlyPurchases = Purchase::whereBetween('purchase_date', [$monthStart, Carbon::now()])
            ->sum('total_amount');

        // Monthly expenses
        $monthlyExpenses = Expense::whereBetween('expense_date', [$monthStart, Carbon::now()])
            ->sum('amount');

        // Net profit this month
        $netProfit = $monthlySales - $monthlyPurchases - $monthlyExpenses;

        // Total customers
        $totalCustomers = Customer::whereNull('deleted_at')->count();

        // Total products
        $totalProducts = Product::whereNull('deleted_at')->count();

        // Low stock count
        $lowStockCount = Product::with('inventoryStock')
            ->whereNull('deleted_at')
            ->get()
            ->filter(fn($p) => $p->is_low_stock)
            ->count();

        return $this->success([
            'currency'          => $setting?->currency_symbol ?? 'Rs',
            'today_sales'       => (float) $todaySales,
            'monthly_sales'     => (float) $monthlySales,
            'monthly_purchases' => (float) $monthlyPurchases,
            'monthly_expenses'  => (float) $monthlyExpenses,
            'net_profit'        => (float) $netProfit,
            'pending_orders'    => $pendingOrders,
            'total_customers'   => $totalCustomers,
            'total_products'    => $totalProducts,
            'low_stock_count'   => $lowStockCount,
        ]);
    }
}
