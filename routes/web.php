<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController ;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SalesPaymentController;
use App\Http\Controllers\SalesDiscountTaxController;



// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    $title = "POS - Dashboard"; 
    return view('dashboard', compact('title'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {

    // Stores
    Route::get('/stores/list', [StoreController::class, 'index'])->name('store.list');
    Route::get('/stores/create', [StoreController::class, 'create'])->name('store.create');
    Route::post('/stores/store', [StoreController::class, 'store'])->name('store.store');
    Route::get('/stores/edit/{id}', [StoreController::class, 'edit'])->name('store.edit');
    Route::post('/stores/edit/{id}', [StoreController::class, 'update'])->name('store.update');
    Route::get('/stores/delete/{id}', [StoreController::class, 'destroy'])->name('store.delete');



    // Company
    Route::get('/company/list', [CompanyController::class, 'index'])->name('company.list');
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/company/store', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/company/edit/{id}', [CompanyController::class, 'edit'])->name('company.edit');
    Route::post('/company/edit/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::get('/company/delete/{id}', [CompanyController::class, 'destroy'])->name('company.delete');



    // Category
    Route::get('/category/list', [CategoryController::class, 'index'])->name('category.list');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/category/edit/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::get('/category/delete/{id}', [CategoryController::class, 'destroy'])->name('category.delete');

    

    // Vendor
    Route::get('/vendor/list', [VendorController::class, 'index'])->name('vendor.list');
    Route::get('/vendor/create', [VendorController::class, 'create'])->name('vendor.create');
    Route::post('/vendor/store', [VendorController::class, 'store'])->name('vendor.store');
    Route::get('/vendor/edit/{id}', [VendorController::class, 'edit'])->name('vendor.edit');
    Route::post('/vendor/edit/{id}', [VendorController::class, 'update'])->name('vendor.update');
    Route::get('/vendor/delete/{id}', [VendorController::class, 'destroy'])->name('vendor.delete');



    // Customer
    Route::get('/customer/list', [CustomerController::class, 'index'])->name('customer.list');
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/edit/{id}', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::post('/customer/edit/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::get('/customer/delete/{id}', [CustomerController::class, 'destroy'])->name('customer.delete');



    // Product
    Route::get('/product/list', [ProductController::class, 'index'])->name('product.list');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/edit/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/product/delete/{id}', [ProductController::class, 'destroy'])->name('product.delete');



    // Loan
    Route::get('/loan/list', [LoanController::class, 'index'])->name('loan.list');
    Route::get('/loan/create', [LoanController::class, 'create'])->name('loan.create');
    Route::post('/loan/store', [LoanController::class, 'store'])->name('loan.store');
    Route::get('/loan/edit/{id}', [LoanController::class, 'edit'])->name('loan.edit');
    Route::post('/loan/edit/{id}', [LoanController::class, 'update'])->name('loan.update');
    Route::get('/loan/delete/{id}', [LoanController::class, 'destroy'])->name('loan.delete');
    Route::get('/get-users-by-type', [LoanController::class, 'getUsersByType'])->name('get.users.by.type'); // for loans



    // Expense
    Route::get('/expense/list', [ExpenseController::class, 'index'])->name('expense.list');
    Route::get('/expense/create', [ExpenseController::class, 'create'])->name('expense.create');
    Route::post('/expense/store', [ExpenseController::class, 'store'])->name('expense.store');
    Route::get('/expense/edit/{id}', [ExpenseController::class, 'edit'])->name('expense.edit');
    Route::post('/expense/edit/{id}', [ExpenseController::class, 'update'])->name('expense.update');
    Route::get('/expense/delete/{id}', [ExpenseController::class, 'destroy'])->name('expense.delete');



    // Sales
    Route::get('/sale/list', [SaleController::class, 'index'])->name('sale.list');
    Route::get('/sale/create', [SaleController::class, 'create'])->name('sale.create');
    Route::post('/sale/store', [SaleController::class, 'store'])->name('sale.store');
    Route::get('/sale/edit/{id}', [SaleController::class, 'edit'])->name('sale.edit');
    Route::post('/sale/edit/{id}', [SaleController::class, 'update'])->name('sale.update');
    Route::get('/sale/delete/{id}', [SaleController  ::class, 'destroy'])->name('sale.delete');
    // Sale Items View
    Route::get('/sale/items/{id}', [SaleController::class, 'showItems'])->name('sale.items');



    // Purchases
    Route::get('/purchase/list', [PurchaseController::class, 'index'])->name('purchase.list');
    Route::get('/purchase/create', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/edit/{id}', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::put('/purchase/update/{id}', [PurchaseController::class, 'update'])->name('purchase.update');
    Route::delete('/purchase/delete/{id}', [PurchaseController::class, 'destroy'])->name('purchase.delete');
    // Purchase Items View
    Route::get('/purchase/items/{id}', [PurchaseController::class, 'showItems'])->name('purchase.items');



    // Payments
    Route::get('/payment/list', [FinanceController::class, 'payments'])->name('payment.list');
    Route::get('/payment/create', [FinanceController::class, 'createPayment'])->name('payment.create');
    Route::post('/payment/store', [FinanceController::class, 'storePayment'])->name('payment.store');
    Route::get('/payment/edit/{id}', [FinanceController::class, 'editPayment'])->name('payment.edit');
    Route::post('/payment/update/{id}', [FinanceController::class, 'updatePayment'])->name('payment.update');
    Route::get('/payment/delete/{id}', [FinanceController::class, 'deletePayment'])->name('payment.delete');



    // Profits
    Route::get('/profit/list', [FinanceController::class, 'profits'])->name('profit.list');
    Route::get('/profit/create', [FinanceController::class, 'createProfit'])->name('profit.create');
    Route::post('/profit/store', [FinanceController::class, 'storeProfit'])->name('profit.store');
    Route::get('/profit/edit/{id}', [FinanceController::class, 'editProfit'])->name('profit.edit');
    Route::post('/profit/update/{id}', [FinanceController::class, 'updateProfit'])->name('profit.update');
    Route::get('/profit/delete/{id}', [FinanceController::class, 'deleteProfit'])->name('profit.delete');



    // Stock Adjustements
    Route::get('stock-adjustments/list', [StockAdjustmentController::class, 'index'])->name('stock_adjustments.list');
    Route::get('stock-adjustments/create', [StockAdjustmentController::class, 'create'])->name('stock_adjustments.create');
    Route::post('stock-adjustments/store', [StockAdjustmentController::class, 'store'])->name('stock_adjustments.store');
    Route::get('stock-adjustments/edit/{id}', [StockAdjustmentController::class, 'edit'])->name('stock_adjustments.edit');
    Route::put('stock-adjustments/update/{id}', [StockAdjustmentController::class, 'update'])->name('stock_adjustments.update');
    Route::get('stock-adjustments/delete/{id}', [StockAdjustmentController::class, 'destroy'])->name('stock_adjustments.destroy');



    Route::prefix('sales')->group(function () {
        // Payments
        Route::get('payments/list/{saleId}', [SalesPaymentController::class, 'index'])->name('payments.list');
        Route::get('payments/create/{saleId}', [SalesPaymentController::class, 'create'])->name('payments.create');
        Route::post('payments/store/{saleId}', [SalesPaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/edit/{id}', [SalesPaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/update/{id}', [SalesPaymentController::class, 'update'])->name('payments.update');
        Route::get('payments/delete/{id}', [SalesPaymentController::class, 'destroy'])->name('payments.destroy');
    
        // Discounts & Taxes
        Route::get('{saleId}/discounts-taxes', [SalesDiscountTaxController::class, 'edit'])->name('discount_taxes.edit');
        Route::post('{saleId}/discounts-taxes', [SalesDiscountTaxController::class, 'update'])->name('discount_taxes.update');
    });
    



    // Prodfile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
