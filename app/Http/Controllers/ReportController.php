<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Branch;
use App\Models\Setting;
use App\Models\InventoryStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    private function dateRange(Request $request): array
    {
        $start = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        return [$start, $end];
    }

    public function sales(Request $request)
    {
        [$start, $end] = $this->dateRange($request);

        $query = Sale::with(['customer', 'branch'])
            ->where('sale_origin', 'POS')
            ->whereBetween('sale_date', [$start, $end]);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $sales    = $query->latest('sale_date')->get();
        $total    = $sales->sum('final_amount');
        $branches = Branch::all();
        $setting  = Setting::first();
        $title    = 'Sales Report';

        return view('admin.reports.sales', compact('sales', 'total', 'branches', 'setting', 'title', 'start', 'end'));
    }

    public function purchases(Request $request)
    {
        [$start, $end] = $this->dateRange($request);

        $query = Purchase::with(['supplier', 'warehouse'])
            ->whereBetween('purchase_date', [$start, $end]);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $purchases  = $query->latest('purchase_date')->get();
        $total      = $purchases->sum('total_amount');
        $warehouses = Warehouse::orderBy('name')->get();
        $setting    = Setting::first();
        $title      = 'Purchases Report';

        return view('admin.reports.purchases', compact('purchases', 'total', 'warehouses', 'setting', 'title', 'start', 'end'));
    }

    public function expenses(Request $request)
    {
        [$start, $end] = $this->dateRange($request);

        $query = Expense::with(['category', 'branch'])
            ->whereBetween('expense_date', [$start, $end]);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $expenses = $query->latest('expense_date')->get();
        $total    = $expenses->sum('amount');
        $branches = Branch::all();
        $setting  = Setting::first();
        $title    = 'Expenses Report';

        return view('admin.reports.expenses', compact('expenses', 'total', 'branches', 'setting', 'title', 'start', 'end'));
    }

    public function profitLoss(Request $request)
    {
        [$start, $end] = $this->dateRange($request);

        $salesQuery = Sale::where('sale_origin', 'POS')
            ->whereBetween('sale_date', [$start, $end]);

        $purchasesQuery = Purchase::whereBetween('purchase_date', [$start, $end]);

        $expensesQuery = Expense::whereBetween('expense_date', [$start, $end]);

        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id;
            $salesQuery->where('branch_id', $branchId);
            // purchases has no branch_id (uses warehouse_id), so branch filter is not applied to it
            $expensesQuery->where('branch_id', $branchId);
        }

        $totalRevenue   = $salesQuery->sum('final_amount');
        $totalPurchases = $purchasesQuery->sum('total_amount');
        $totalExpenses  = $expensesQuery->sum('amount');
        $grossProfit    = $totalRevenue - $totalPurchases;
        $netProfit      = $grossProfit - $totalExpenses;

        $branches = Branch::all();
        $setting  = Setting::first();
        $title    = 'Profit & Loss Report';

        return view('admin.reports.profit-loss', compact(
            'totalRevenue', 'totalPurchases', 'totalExpenses',
            'grossProfit', 'netProfit', 'branches', 'setting', 'title', 'start', 'end'
        ));
    }

    public function inventoryValuation(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');

        // Eager-load product (with category) and warehouse; variant is optional
        $stockQuery = InventoryStock::with([
            'product:id,name,sku,actual_price,category_id',
            'product.category:id,name',
            'variant:id,variant_name,actual_price',
            'warehouse:id,name',
        ])->whereHas('product', fn($q) => $q->whereNull('deleted_at'))
          ->where('quantity_in_base_unit', '>', 0);

        if ($warehouseId) {
            $stockQuery->where('warehouse_id', $warehouseId);
        }

        $stocks = $stockQuery->get();

        // Calculate per-row valuation: quantity × unit cost
        $stocks = $stocks->map(function ($stock) {
            $unitCost = $stock->variant
                ? ($stock->variant->actual_price ?? $stock->product->actual_price)
                : $stock->product->actual_price;

            $stock->unit_cost    = (float) $unitCost;
            $stock->total_value  = round($stock->quantity_in_base_unit * $unitCost, 2);
            return $stock;
        });

        $grandTotal = $stocks->sum('total_value');
        $warehouses = Warehouse::orderBy('name')->get();
        $setting    = Setting::first();
        $title      = 'Inventory Valuation Report';

        return view('admin.reports.inventory-valuation', compact(
            'stocks', 'grandTotal', 'warehouses', 'setting', 'title', 'warehouseId'
        ));
    }
}
