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
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplierController;




// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    $title = "POS - Dashboard"; 
    return view('dashboard', compact('title'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {

    // // Stores
    // Route::get('/stores/list', [StoreController::class, 'index'])->name('store.list');
    // Route::get('/stores/create', [StoreController::class, 'create'])->name('store.create');
    // Route::post('/stores/store', [StoreController::class, 'store'])->name('store.store');
    // Route::get('/stores/edit/{id}', [StoreController::class, 'edit'])->name('store.edit');
    // Route::post('/stores/edit/{id}', [StoreController::class, 'update'])->name('store.update');
    // Route::get('/stores/delete/{id}', [StoreController::class, 'destroy'])->name('store.delete');



    // // Company
    // Route::get('/company/list', [CompanyController::class, 'index'])->name('company.list');
    // Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    // Route::post('/company/store', [CompanyController::class, 'store'])->name('company.store');
    // Route::get('/company/edit/{id}', [CompanyController::class, 'edit'])->name('company.edit');
    // Route::post('/company/edit/{id}', [CompanyController::class, 'update'])->name('company.update');
    // Route::get('/company/delete/{id}', [CompanyController::class, 'destroy'])->name('company.delete');



    // // Loan
    // Route::get('/loan/list', [LoanController::class, 'index'])->name('loan.list');
    // Route::get('/loan/create', [LoanController::class, 'create'])->name('loan.create');
    // Route::post('/loan/store', [LoanController::class, 'store'])->name('loan.store');
    // Route::get('/loan/edit/{id}', [LoanController::class, 'edit'])->name('loan.edit');
    // Route::post('/loan/edit/{id}', [LoanController::class, 'update'])->name('loan.update');
    // Route::get('/loan/delete/{id}', [LoanController::class, 'destroy'])->name('loan.delete');
    // Route::get('/get-users-by-type', [LoanController::class, 'getUsersByType'])->name('get.users.by.type'); // for loans



    // // Expense
    // Route::get('/expense/list', [ExpenseController::class, 'index'])->name('expense.list');
    // Route::get('/expense/create', [ExpenseController::class, 'create'])->name('expense.create');
    // Route::post('/expense/store', [ExpenseController::class, 'store'])->name('expense.store');
    // Route::get('/expense/edit/{id}', [ExpenseController::class, 'edit'])->name('expense.edit');
    // Route::post('/expense/edit/{id}', [ExpenseController::class, 'update'])->name('expense.update');
    // Route::get('/expense/delete/{id}', [ExpenseController::class, 'destroy'])->name('expense.delete');



    // // Sales
    // Route::get('/sale/list', [SaleController::class, 'index'])->name('sale.list');
    // Route::get('/sale/create', [SaleController::class, 'create'])->name('sale.create');
    // Route::post('/sale/store', [SaleController::class, 'store'])->name('sale.store');
    // Route::get('/sale/edit/{id}', [SaleController::class, 'edit'])->name('sale.edit');
    // Route::post('/sale/edit/{id}', [SaleController::class, 'update'])->name('sale.update');
    // Route::get('/sale/delete/{id}', [SaleController  ::class, 'destroy'])->name('sale.delete');
    // // Sale Items View
    // Route::get('/sale/items/{id}', [SaleController::class, 'showItems'])->name('sale.items');



    // // Payments
    // Route::get('/payment/list', [FinanceController::class, 'payments'])->name('payment.list');
    // Route::get('/payment/create', [FinanceController::class, 'createPayment'])->name('payment.create');
    // Route::post('/payment/store', [FinanceController::class, 'storePayment'])->name('payment.store');
    // Route::get('/payment/edit/{id}', [FinanceController::class, 'editPayment'])->name('payment.edit');
    // Route::post('/payment/update/{id}', [FinanceController::class, 'updatePayment'])->name('payment.update');
    // Route::get('/payment/delete/{id}', [FinanceController::class, 'deletePayment'])->name('payment.delete');



    // // Profits
    // Route::get('/profit/list', [FinanceController::class, 'profits'])->name('profit.list');
    // Route::get('/profit/create', [FinanceController::class, 'createProfit'])->name('profit.create');
    // Route::post('/profit/store', [FinanceController::class, 'storeProfit'])->name('profit.store');
    // Route::get('/profit/edit/{id}', [FinanceController::class, 'editProfit'])->name('profit.edit');
    // Route::post('/profit/update/{id}', [FinanceController::class, 'updateProfit'])->name('profit.update');
    // Route::get('/profit/delete/{id}', [FinanceController::class, 'deleteProfit'])->name('profit.delete');



    // // Stock Adjustements
    // Route::get('stock-adjustments/list', [StockAdjustmentController::class, 'index'])->name('stock_adjustments.list');
    // Route::get('stock-adjustments/create', [StockAdjustmentController::class, 'create'])->name('stock_adjustments.create');
    // Route::post('stock-adjustments/store', [StockAdjustmentController::class, 'store'])->name('stock_adjustments.store');
    // Route::get('stock-adjustments/edit/{id}', [StockAdjustmentController::class, 'edit'])->name('stock_adjustments.edit');
    // Route::put('stock-adjustments/update/{id}', [StockAdjustmentController::class, 'update'])->name('stock_adjustments.update');
    // Route::get('stock-adjustments/delete/{id}', [StockAdjustmentController::class, 'destroy'])->name('stock_adjustments.destroy');



    // Route::prefix('sales')->group(function () {
    //     // Payments
    //     Route::get('payments/list/{saleId}', [SalesPaymentController::class, 'index'])->name('payments.list');
    //     Route::get('payments/create/{saleId}', [SalesPaymentController::class, 'create'])->name('payments.create');
    //     Route::post('payments/store/{saleId}', [SalesPaymentController::class, 'store'])->name('payments.store');
    //     Route::get('payments/edit/{id}', [SalesPaymentController::class, 'edit'])->name('payments.edit');
    //     Route::put('payments/update/{id}', [SalesPaymentController::class, 'update'])->name('payments.update');
    //     Route::get('payments/delete/{id}', [SalesPaymentController::class, 'destroy'])->name('payments.destroy');
    
    //     // Discounts & Taxes
    //     Route::get('{saleId}/discounts-taxes', [SalesDiscountTaxController::class, 'edit'])->name('discount_taxes.edit');
    //     Route::post('{saleId}/discounts-taxes', [SalesDiscountTaxController::class, 'update'])->name('discount_taxes.update');
    // });


    
    // Users
    Route::get('/user/list', [UserController::class, 'index'])->name('user.list');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/edit/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');



    // Role
    Route::get('/role/list', [RoleController::class, 'index'])->name('role.list');
    Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
    Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
    Route::get('/role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('/role/edit/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::get('/role/delete/{id}', [RoleController::class, 'destroy'])->name('role.delete');



    // Warehouse
    Route::prefix('warehouse')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('warehouse.list');
        Route::get('/create', [WarehouseController::class, 'create'])->name('warehouse.create');
        Route::post('/store', [WarehouseController::class, 'store'])->name('warehouse.store');
        Route::get('/edit/{id}', [WarehouseController::class, 'edit'])->name('warehouse.edit');
        Route::put('/update/{id}', [WarehouseController::class, 'update'])->name('warehouse.update');
        Route::get('/delete/{id}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');
    });



    //Branch
    Route::prefix('branch')->name('branch.')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('list');
        Route::get('/create', [BranchController::class, 'create'])->name('create');
        Route::post('/store', [BranchController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BranchController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [BranchController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [BranchController::class, 'destroy'])->name('delete');
    });



    //Units
    Route::prefix('units')->name('units.')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('list');
        Route::get('/create', [UnitController::class, 'create'])->name('create');
        Route::post('/store', [UnitController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UnitController::class, 'edit'])->name('edit');
        Route::post('/{id}', [UnitController::class, 'update'])->name('update');
        Route::get('/{id}', [UnitController::class, 'destroy'])->name('destroy');
    });



    //Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('list');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::get('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });



    //Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('list');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::post('/{id}', [ProductController::class, 'update'])->name('update');
        Route::get('/{id}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/variants', [ProductController::class, 'viewVariants'])->name('variants');
    });



    //Suppliers
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('list');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/store', [SupplierController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::post('/{id}', [SupplierController::class, 'update'])->name('update');
        Route::get('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
    });



    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('list');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/store', [CustomerController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::post('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::get('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
    });




        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [PurchaseController::class, 'index'])->name('list');
            Route::get('/create', [PurchaseController::class, 'create'])->name('create');
            Route::post('/store', [PurchaseController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PurchaseController::class, 'edit'])->name('edit');
            Route::post('/{id}', [PurchaseController::class, 'update'])->name('update');
            Route::get('/{id}', [PurchaseController::class, 'destroy'])->name('destroy');
            // Purchase Items View
            Route::get('/purchase/items/{id}', [PurchaseController::class, 'showItems'])->name('purchase.items');
    });




    // Prodfile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
