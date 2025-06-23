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
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\POSController;
use App\Http\Middleware\CheckOpenRegister;
use App\Http\Controllers\SettingController;




// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    $title = "POS - Dashboard"; 
    return view('dashboard', compact('title'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {


    // // Profits
    // Route::get('/profit/list', [FinanceController::class, 'profits'])->name('profit.list');
    // Route::get('/profit/create', [FinanceController::class, 'createProfit'])->name('profit.create');
    // Route::post('/profit/store', [FinanceController::class, 'storeProfit'])->name('profit.store');
    // Route::get('/profit/edit/{id}', [FinanceController::class, 'editProfit'])->name('profit.edit');
    // Route::post('/profit/update/{id}', [FinanceController::class, 'updateProfit'])->name('profit.update');
    // Route::get('/profit/delete/{id}', [FinanceController::class, 'deleteProfit'])->name('profit.delete');


    
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



    //Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('list');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/store', [CustomerController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::post('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::get('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
    });



    //Purchases
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('list');
        Route::get('/create', [PurchaseController::class, 'create'])->name('create');
        Route::post('/store', [PurchaseController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PurchaseController::class, 'edit'])->name('edit');
        Route::post('/{id}', [PurchaseController::class, 'update'])->name('update');
        Route::delete('/{id}', [PurchaseController::class, 'destroy'])->name('destroy');
        // Purchase Items View
        // Route::get('/purchase/items/{id}', [PurchaseController::class, 'showItems'])->name('items');
        Route::get('/invoice/{id}', [PurchaseController::class, 'invoice'])->name('invoice');

    });



    //Sales
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('list');
        Route::get('/create', [SaleController::class, 'create'])->name('create');
        Route::post('/checkout', [SaleController::class, 'process'])->name('checkout.process');
        Route::delete('/{id}', [SaleController::class, 'destroy'])->name('destroy');
        // Purchase Items View
        // Route::get('/purchase/items/{id}', [SaleController::class, 'showItems'])->name('items');
        Route::get('/invoice/{id}', [SaleController::class, 'invoice'])->name('invoice');

    });



    //Sales Return
    Route::prefix('sale_return')->name('sale_return.')->group(function () {
        Route::get('/', [SalesReturnController::class, 'list'])->name('list');
        Route::get('/create/{sale}', [SalesReturnController::class, 'create'])->name('create');
        Route::post('/{sale}/store', [SalesReturnController::class, 'store'])->name('store');
        Route::delete('/{id}', [SalesReturnController::class, 'destroy'])->name('destroy');

        Route::get('/details/{id}', [SalesReturnController::class, 'details'])->name('details');
        Route::get('/{sales_return}', [SalesReturnController::class, 'show'])->name('show');
    });



    //Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('list');
        Route::get('/create', [FinanceController::class, 'create'])->name('create');
        Route::post('/store', [FinanceController::class, 'store'])->name('store');
        Route::delete('/{id}', [FinanceController::class, 'destroy'])->name('destroy');


        Route::get('/invoice/{id}', [SaleController::class, 'invoice'])->name('invoice');
    });


    // Stocks
    Route::get('/stock/list', [StockAdjustmentController::class, 'stockIndex'])->name('stock.list');
    Route::get('/stock-ledger', [StockAdjustmentController::class, 'stockLedger'])->name('stock.ledger');

    // POS
    Route::middleware(['auth', CheckOpenRegister::class])->group(function () {
        Route::get('/pos', [SaleController::class, 'pos'])->name('pos.index');
        Route::post('/pos/checkout', [SaleController::class, 'posProcess'])->name('checkout.pos');
    });



    // Expense Category
    Route::get('/expense_categories/list', [ExpenseController::class, 'index'])->name('expense_categories.list');
    Route::post('/expense_categories/store', [ExpenseController::class, 'store'])->name('expense_categories.store');
    Route::put('/expense_categories/{id}', [ExpenseController::class, 'update'])->name('expense_categories.update');
    Route::get('/expense_categories/{id}', [ExpenseController::class, 'destroy'])->name('expense_categories.destroy');



    // Expense
    Route::get('/expense/list', [ExpenseController::class, 'list'])->name('expense.list');
    Route::get('/expense/create', [ExpenseController::class, 'expenseCreate'])->name('expense.create');
    Route::post('/expense/store', [ExpenseController::class, 'expenseStore'])->name('expense.store');
    Route::get('/expenses/edit/{id}', [ExpenseController::class, 'expenseEdit'])->name('expense.edit');
    Route::put('/expenses/update/{id}', [ExpenseController::class, 'expenseUpdate'])->name('expense.update');
    Route::get('/expenses/delete/{id}', [ExpenseController::class, 'expenseDestroy'])->name('expense.destroy');
    


    //Cash Register
    Route::post('/pos/open-register', [POSController::class, 'openRegister'])->name('pos.openRegister');
    Route::post('/pos/close-register', [POSController::class, 'closeRegister'])->name('pos.closeRegister');
    Route::get('/pos/check-register', [POSController::class, 'checkRegister'])->name('pos.checkRegister');
    Route::get('/pos/register-details', [POSController::class, 'getRegisterDetails'])->name('pos.getRegisterDetails');


    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/save', [SettingController::class, 'saveSettings'])->name('settings.save');


    // Prodfile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
