# IMPLEMENTATION PLAN
> **POS-Ecommerce** — Step-by-Step Developer Execution Guide
> Based on: `FEATURE_ENHANCEMENT_PLAN.md`
> Last Updated: February 2026

---

## HOW TO USE THIS DOCUMENT

Each task is written as a **checklist** with:
- Exact **files to create or modify**
- Exact **code to write**
- **Commands to run**
- **Acceptance criteria** (how to know it's done)

Work through tasks in order. Do NOT skip phases — Phase 1 fixes must be in place before Phase 2 begins.

---

# ═══════════════════════════════════════
# PHASE 1 — QUICK WINS (Week 1–2)
# ═══════════════════════════════════════
> **Goal:** Fix all critical bugs, security holes, and obvious broken features.

---

## TASK 1.1 — Fix Inactive User Login Bug
**Priority:** P0 | **Effort:** 1 hour | **Impact:** Critical Security Fix

### Files to Modify:
- `app/Http/Requests/Auth/LoginRequest.php`

### What to Do:
After `$this->authenticate()` succeeds in `LoginRequest`, add a status check:

Open `app/Http/Requests/Auth/LoginRequest.php` and find the `authenticate()` method. Add this check right after `Auth::attempt()` succeeds:

```php
// Inside authenticate() method, after Auth::attempt():
if (Auth::attempt(...)) {
    $user = Auth::user();
    if ($user->status !== 'Active') {
        Auth::logout();
        throw ValidationException::withMessages([
            'email' => 'Your account has been deactivated. Contact admin.',
        ]);
    }
}
```

### Acceptance Criteria:
- [ ] Set a user's status to 'Inactive' in DB
- [ ] Try logging in — should be rejected with "deactivated" message
- [ ] Active users still log in normally

---

## TASK 1.2 — Fix GET-Based Delete Routes (Security)
**Priority:** P0 | **Effort:** 2 hours | **Impact:** Critical Security Fix

### Files to Modify:
- `routes/web.php`
- All relevant Blade view files (index pages with delete buttons)

### What to Do:

**Step 1:** In `routes/web.php`, change all destructive GET routes to DELETE:

```php
// BEFORE (insecure):
Route::get('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');

// AFTER (correct):
Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
```

Apply this change to ALL of these routes:
- `/user/delete/{id}` → DELETE
- `/role/delete/{id}` → DELETE
- `warehouse/delete/{id}` → DELETE
- `branch/delete/{id}` → DELETE
- `units/{id}` (destroy) → DELETE
- `categories/{id}` (destroy) → DELETE
- `products/{id}` (destroy) → DELETE
- `suppliers/{id}` (destroy) → DELETE
- `customers/{id}` (destroy) → DELETE
- `sale_return/{id}` → DELETE
- `payments/{id}` → DELETE
- `expense_categories/{id}` → DELETE (GET already)
- `expenses/delete/{id}` → DELETE
- `quotations/{quotation}` (destroy) → DELETE

**Step 2:** In every Blade view that has a delete link/button, wrap it in a form:

```html
<!-- BEFORE (insecure link): -->
<a href="{{ route('user.delete', $user->id) }}" onclick="return confirm('Delete?')">Delete</a>

<!-- AFTER (secure form): -->
<form action="{{ route('user.delete', $user->id) }}" method="POST"
      onsubmit="return confirm('Are you sure?')" style="display:inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
</form>
```

### Acceptance Criteria:
- [ ] All delete operations use DELETE HTTP method
- [ ] All delete forms include `@csrf` and `@method('DELETE')`
- [ ] Visiting delete URL directly via browser returns 405 Method Not Allowed

---

## TASK 1.3 — Disable Public Admin Registration
**Priority:** P0 | **Effort:** 30 minutes | **Impact:** High Security Fix

### Files to Modify:
- `routes/auth.php`

### What to Do:
Comment out or remove the register routes. Admins should only be created by existing admins via `UserController`:

```php
// In routes/auth.php — REMOVE or COMMENT OUT:
// Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
// Route::post('register', [RegisteredUserController::class, 'store']);
```

Also remove the registration link from the login view:

Find `resources/views/auth/login.blade.php` and remove the "Register" link if present.

### Acceptance Criteria:
- [ ] Visiting `http://localhost/register` returns 404
- [ ] Admin users can still be created via `/user/create`

---

## TASK 1.4 — Fix `getIsLowStockAttribute()` Hardcoded Value
**Priority:** P1 | **Effort:** 15 minutes | **Impact:** Medium Bug Fix

### Files to Modify:
- `app/Models/Product.php`

### What to Do:
```php
// BEFORE (line ~58 in Product.php):
public function getIsLowStockAttribute()
{
    return $this->total_stock <= 5;  // ❌ hardcoded 5
}

// AFTER:
public function getIsLowStockAttribute()
{
    $threshold = $this->low_stock ?? 5;
    return $this->total_stock <= $threshold;  // ✅ uses DB column
}
```

### Acceptance Criteria:
- [ ] Product with `low_stock = 20` shows as low stock when stock is 15
- [ ] Product with `low_stock = null` falls back to threshold of 5

---

## TASK 1.5 — Cache `posSetting()` Helper
**Priority:** P1 | **Effort:** 30 minutes | **Impact:** Medium Performance Fix

### Files to Modify:
- `app/Helpers/SettingHelper.php`

### What to Do:
```php
// BEFORE:
function posSetting($key, $default = null) {
    $setting = Setting::first();  // ❌ DB query every call
    return $setting->{$key} ?? $default;
}

// AFTER:
function posSetting($key, $default = null) {
    static $setting = null;  // ✅ cached per request
    if ($setting === null) {
        $setting = \App\Models\Setting::first();
    }
    return $setting ? ($setting->{$key} ?? $default) : $default;
}
```

### Acceptance Criteria:
- [ ] `posSetting()` still returns correct values
- [ ] Only 1 DB query for settings per request (check Laravel Debugbar or logs)

---

## TASK 1.6 — Remove Dead Imports from `routes/web.php`
**Priority:** P3 | **Effort:** 15 minutes | **Impact:** Code Cleanliness

### Files to Modify:
- `routes/web.php`

### What to Do:
Remove these unused imports at the top of `web.php`:
```php
// DELETE these lines (controllers that don't exist):
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\LoanController;
```

### Acceptance Criteria:
- [ ] `php artisan route:list` runs without errors
- [ ] No unused import warnings

---

## TASK 1.7 — Enable Customer Self-Registration
**Priority:** P1 | **Effort:** 3 hours | **Impact:** High — E-commerce growth

### Files to Create:
- `resources/views/store/auth/register.blade.php`

### Files to Modify:
- `routes/web.php`
- `app/Http/Controllers/Frontend/CustomerAuth/RegisteredUserController.php`

### What to Do:

**Step 1:** Uncomment the registration routes in `routes/web.php`:
```php
// In the store prefix group, uncomment:
Route::get('/register', [CustomerRegisteredUserController::class, 'create'])->name('customer.register');
Route::post('/register', [CustomerRegisteredUserController::class, 'store']);
```

**Step 2:** Update `RegisteredUserController.php` store() method with validation:
```php
public function store(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:customers,email',
        'phone'    => 'required|string|max:20',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $customer = Customer::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'phone'    => $request->phone,
        'password' => $request->password,  // auto-hashed via model cast
    ]);

    Auth::guard('customer')->login($customer);

    return redirect()->route('store.shop');
}
```

**Step 3:** Create `resources/views/store/auth/register.blade.php` — copy the structure of `store/auth/login.blade.php` and add name, phone, password, password_confirmation fields.

**Step 4:** Add "Register" link to the store login page.

### Acceptance Criteria:
- [ ] Visiting `/store/register` shows the registration form
- [ ] Submitting valid data creates a customer and logs them in
- [ ] Duplicate email shows validation error
- [ ] Weak password (< 8 chars) shows validation error

---

## TASK 1.8 — Add Customer Forgot Password
**Priority:** P1 | **Effort:** 3 hours | **Impact:** High — E-commerce UX

### Files to Create:
- `app/Http/Controllers/Frontend/CustomerAuth/CustomerPasswordResetController.php`
- `resources/views/store/auth/forgot-password.blade.php`
- `resources/views/store/auth/reset-password.blade.php`

### Files to Modify:
- `routes/web.php`

### What to Do:

**Step 1:** Add routes in `web.php` store prefix group:
```php
Route::get('/forgot-password', [CustomerPasswordResetController::class, 'create'])
     ->name('customer.password.request');
Route::post('/forgot-password', [CustomerPasswordResetController::class, 'store'])
     ->name('customer.password.email');
Route::get('/reset-password/{token}', [CustomerPasswordResetController::class, 'resetForm'])
     ->name('customer.password.reset');
Route::post('/reset-password', [CustomerPasswordResetController::class, 'resetPassword'])
     ->name('customer.password.update');
```

**Step 2:** Create `CustomerPasswordResetController.php`:
```php
namespace App\Http\Controllers\Frontend\CustomerAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class CustomerPasswordResetController extends Controller
{
    public function create()
    {
        return view('store.auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetForm(Request $request, string $token)
    {
        return view('store.auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('customer.login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
```

**Step 3:** Add `customers` password broker to `config/auth.php`:
```php
'passwords' => [
    'users' => ['provider' => 'users', 'table' => 'password_reset_tokens', 'expire' => 60, 'throttle' => 60],
    'customers' => ['provider' => 'customers', 'table' => 'password_reset_tokens', 'expire' => 60, 'throttle' => 60],
],
```

### Acceptance Criteria:
- [ ] `/store/forgot-password` form sends reset email to customer
- [ ] Reset link redirects to `/store/reset-password/{token}`
- [ ] New password is saved and customer can log in with it

---

## TASK 1.9 — Add Customer Order History Page
**Priority:** P1 | **Effort:** 3 hours | **Impact:** High — Basic e-commerce feature

### Files to Create:
- `resources/views/store/orders.blade.php`
- `resources/views/store/order-detail.blade.php`

### Files to Modify:
- `routes/web.php`
- `app/Http/Controllers/Frontend/CheckoutController.php`

### What to Do:

**Step 1:** Add routes inside `auth:customer` middleware group in `web.php`:
```php
Route::get('/orders', [CheckoutController::class, 'orders'])->name('store.orders');
Route::get('/orders/{id}', [CheckoutController::class, 'orderDetail'])->name('store.order.detail');
```

**Step 2:** Add methods to `CheckoutController.php`:
```php
public function orders()
{
    $orders = \App\Models\Sale::where('customer_id', auth('customer')->id())
        ->where('sale_origin', 'Ecommerce')
        ->with(['items.product', 'items.variant'])
        ->latest('sale_date')
        ->paginate(10);

    return view('store.orders', compact('orders'));
}

public function orderDetail($id)
{
    $order = \App\Models\Sale::where('customer_id', auth('customer')->id())
        ->where('sale_origin', 'Ecommerce')
        ->with(['items.product', 'items.variant'])
        ->findOrFail($id);

    return view('store.order-detail', compact('order'));
}
```

**Step 3:** Create `resources/views/store/orders.blade.php` — table with columns: Invoice#, Date, Items Count, Total, Status, Actions (View Details).

**Step 4:** Create `resources/views/store/order-detail.blade.php` — show order items, totals, status timeline (Pending → Processing → Shipped → Completed).

**Step 5:** Add "My Orders" link to store navigation (visible only when `auth('customer')->check()`).

### Acceptance Criteria:
- [ ] Logged-in customer sees only their own orders
- [ ] Order list paginates (10 per page)
- [ ] Clicking an order shows full detail with items and prices
- [ ] Status is displayed clearly on each order

---

## TASK 1.10 — Fix N+1 Query on Stock List
**Priority:** P2 | **Effort:** 1 hour | **Impact:** Medium Performance Fix

### Files to Modify:
- `app/Http/Controllers/StockAdjustmentController.php`

### What to Do:
Find the `stockIndex()` method and add eager loading:
```php
// BEFORE:
$stocks = InventoryStock::all();

// AFTER:
$stocks = InventoryStock::with(['product', 'variant', 'warehouse'])
    ->whereNull('deleted_at')
    ->get();
```

Same fix for `stockLedger()` method:
```php
$ledgers = StockLedger::with(['product', 'variant', 'warehouse', 'creator'])
    ->latest()
    ->paginate(50);
```

### Acceptance Criteria:
- [ ] Stock list page loads without N+1 queries
- [ ] Install `barryvdh/laravel-debugbar` temporarily to verify query count reduced

---

## ✅ PHASE 1 COMPLETE CHECKLIST

- [ ] 1.1 — Inactive user login blocked
- [ ] 1.2 — All deletes use DELETE method + CSRF forms
- [ ] 1.3 — `/register` route disabled
- [ ] 1.4 — `getIsLowStockAttribute()` uses `$this->low_stock`
- [ ] 1.5 — `posSetting()` uses static cache
- [ ] 1.6 — Dead imports removed from `web.php`
- [ ] 1.7 — Customer self-registration working
- [ ] 1.8 — Customer forgot password working
- [ ] 1.9 — Customer order history page working
- [ ] 1.10 — Stock list N+1 fixed

**Run after Phase 1:**
```bash
php artisan route:list        # Verify all routes are correct
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

# ═══════════════════════════════════════
# PHASE 2 — CORE ENHANCEMENTS (Week 3–6)
# ═══════════════════════════════════════
> **Goal:** Fill critical feature gaps that block professional use.

---

## TASK 2.1 — Role-Based Access Control (RBAC) Middleware
**Priority:** P0 | **Effort:** 1 week | **Impact:** Critical Security

### Files to Create:
- `app/Http/Middleware/RoleMiddleware.php`

### Files to Modify:
- `bootstrap/app.php` (register middleware alias)
- `routes/web.php` (apply middleware to route groups)

### Step 1: Create the Middleware

```php
// app/Http/Middleware/RoleMiddleware.php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = auth()->user();

        if (!$user || !$user->role) {
            abort(403, 'No role assigned. Contact admin.');
        }

        if (!in_array($user->role->name, $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
```

### Step 2: Register Middleware Alias in `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'          => \App\Http\Middleware\RoleMiddleware::class,
        'check.register'=> \App\Http\Middleware\CheckOpenRegister::class,
    ]);
})
```

### Step 3: Define Role Permissions Matrix

| Route Group | Allowed Roles |
|-------------|---------------|
| User & Role management | Admin |
| Warehouse, Branch, Unit, Category management | Admin, Manager |
| Product management (CRUD) | Admin, Manager, Inventory |
| Purchase management | Admin, Manager, Accountant |
| Sales management (list/invoice) | Admin, Manager, Accountant, Cashier |
| Sales create/process | Admin, Manager, Cashier |
| POS terminal | Admin, Manager, Cashier |
| Expense management | Admin, Manager, Accountant |
| Customer management | Admin, Manager |
| Supplier management | Admin, Manager |
| Finance/Payments | Admin, Manager, Accountant |
| Stock management | Admin, Manager, Inventory |
| Settings | Admin |
| Dashboard | All roles |

### Step 4: Apply to `routes/web.php`

```php
Route::middleware('auth', 'verified')->group(function () {

    // ── ALL AUTHENTICATED USERS ──
    Route::get('/dashboard', [POSController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', ...)->name('profile.edit');
    Route::patch('/profile', ...)->name('profile.update');

    // ── ADMIN ONLY ──
    Route::middleware('role:Admin')->group(function () {
        Route::prefix('user')->name('user.')->group(function () { ... });
        Route::prefix('role')->name('role.')->group(function () { ... });
        Route::get('/settings', ...)->name('settings.index');
        Route::post('/settings/save', ...)->name('settings.save');
        Route::post('/mail-settings/save', ...)->name('mail-settings.save');
    });

    // ── ADMIN + MANAGER ──
    Route::middleware('role:Admin,Manager')->group(function () {
        Route::prefix('warehouse')->group(function () { ... });
        Route::prefix('branch')->group(function () { ... });
        Route::prefix('customers')->group(function () { ... });
        Route::prefix('suppliers')->group(function () { ... });
        Route::prefix('quotations')->group(function () { ... });
        Route::get('discount-rules', ...);
    });

    // ── ADMIN + MANAGER + INVENTORY ──
    Route::middleware('role:Admin,Manager,Inventory')->group(function () {
        Route::prefix('products')->group(function () { ... });
        Route::prefix('units')->group(function () { ... });
        Route::prefix('categories')->group(function () { ... });
        Route::get('/stock/list', ...);
        Route::get('/stock-ledger', ...);
    });

    // ── ADMIN + MANAGER + ACCOUNTANT ──
    Route::middleware('role:Admin,Manager,Accountant')->group(function () {
        Route::prefix('purchases')->group(function () { ... });
        Route::prefix('payments')->group(function () { ... });
        Route::prefix('expense')->group(function () { ... });
    });

    // ── SALES ROLES ──
    Route::middleware('role:Admin,Manager,Cashier,Accountant')->group(function () {
        Route::prefix('sales')->group(function () { ... });
        Route::prefix('sale_return')->group(function () { ... });
    });

    // ── POS (Cashier + Admin + Manager) ──
    Route::middleware(['role:Admin,Manager,Cashier', 'check.register'])->group(function () {
        Route::get('/pos', ...)->name('pos.index');
        Route::post('/pos/checkout', ...)->name('checkout.pos');
    });
});
```

### Step 5: Create 403 Error Page

Create `resources/views/errors/403.blade.php`:
```html
@extends('layouts.app')
@section('content')
<div class="text-center py-20">
    <h1 class="text-6xl font-bold text-red-500">403</h1>
    <p class="text-xl mt-4">آپ کے پاس اس صفحے تک رسائی کی اجازت نہیں ہے۔</p>
    <p class="text-gray-500">You do not have permission to access this page.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-6">Go to Dashboard</a>
</div>
@endsection
```

### Acceptance Criteria:
- [ ] Cashier cannot access `/settings`
- [ ] Inventory staff cannot access `/purchases`
- [ ] Accountant cannot access POS terminal
- [ ] Admin can access everything
- [ ] 403 page shows clearly when access denied
- [ ] Dashboard accessible to all roles

---

## TASK 2.2 — Branch-Scoped Data Filtering
**Priority:** P1 | **Effort:** 3 days | **Impact:** High — Data privacy

### Files to Modify:
- `app/Http/Controllers/SaleController.php`
- `app/Http/Controllers/ExpenseController.php`
- `app/Http/Controllers/POSController.php`

### What to Do:
Add a branch filter in all list views for non-admin roles:

```php
// Create a reusable trait: app/Traits/BranchScoped.php
<?php
namespace App\Traits;

trait BranchScoped
{
    protected function branchScope($query, string $branchColumn = 'branch_id')
    {
        $user = auth()->user();

        // Admin and Manager can see all branches
        if (in_array($user->role?->name, ['Admin', 'Manager'])) {
            return $query;
        }

        // Cashier and others see only their branch
        return $query->where($branchColumn, $user->branch_id);
    }
}
```

```php
// In SaleController::index():
use App\Traits\BranchScoped;

class SaleController extends Controller
{
    use BranchScoped;

    public function index()
    {
        $sales = $this->branchScope(Sale::query())
            ->with(['customer', 'branch', 'creator'])
            ->latest('sale_date')
            ->paginate(20);

        return view('sales.index', compact('sales'));
    }
}
```

Apply the same `branchScope()` to: `ExpenseController::list()`, `POSController::dashboard()` stats.

### Acceptance Criteria:
- [ ] Lahore cashier only sees Lahore branch sales
- [ ] Admin sees all branches
- [ ] Dashboard stats reflect branch-filtered data for non-admins

---

## TASK 2.3 — PDF Invoice Generation
**Priority:** P1 | **Effort:** 3 days | **Impact:** High — Professional requirement

### Step 1: Install Package
```bash
composer require barryvdh/laravel-dompdf
```

### Step 2: Publish Config
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### Files to Create:
- `resources/views/pdf/sale-invoice.blade.php`
- `resources/views/pdf/purchase-invoice.blade.php`
- `resources/views/pdf/quotation.blade.php`

### Files to Modify:
- `app/Http/Controllers/SaleController.php`
- `app/Http/Controllers/PurchaseController.php`
- `app/Http/Controllers/QuotationController.php`

### Step 3: Add PDF Download Method to `SaleController.php`

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function downloadInvoice($id)
{
    $sale = Sale::with(['items.product', 'items.variant', 'customer', 'branch'])->findOrFail($id);
    $settings = Setting::first();

    $pdf = Pdf::loadView('pdf.sale-invoice', compact('sale', 'settings'))
              ->setPaper('a4', 'portrait');

    return $pdf->download('Invoice-' . $sale->invoice_number . '.pdf');
}
```

### Step 4: Add Route
```php
Route::get('/sales/invoice/{id}/pdf', [SaleController::class, 'downloadInvoice'])->name('sales.invoice.pdf');
Route::get('/purchases/invoice/{id}/pdf', [PurchaseController::class, 'downloadInvoice'])->name('purchases.invoice.pdf');
Route::get('/quotations/{id}/pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf');
```

### Step 5: Create PDF Blade Template `resources/views/pdf/sale-invoice.blade.php`

Structure:
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .totals { text-align: right; margin-top: 10px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $settings->business_name ?? 'POS System' }}</h2>
        <p>{{ $settings->address ?? '' }} | {{ $settings->company_phone ?? '' }}</p>
        <h3>SALES INVOICE</h3>
        <p>Invoice #: {{ $sale->invoice_number }} | Date: {{ $sale->sale_date }}</p>
    </div>
    <!-- Customer info, items table, totals, footer -->
</body>
</html>
```

### Step 6: Add "Download PDF" button on invoice views

```html
<a href="{{ route('sales.invoice.pdf', $sale->id) }}" class="btn btn-success">
    ⬇ Download PDF
</a>
```

### Acceptance Criteria:
- [ ] Clicking "Download PDF" downloads a valid PDF file
- [ ] PDF contains business logo, name, address
- [ ] PDF shows all invoice line items with correct amounts
- [ ] PDF is formatted cleanly (no broken layout)

---

## TASK 2.4 — Convert Quotation to Sale
**Priority:** P1 | **Effort:** 1 day | **Impact:** High — Workflow completion

### Files to Modify:
- `app/Http/Controllers/QuotationController.php`
- `routes/web.php`
- `resources/views/quotations/show.blade.php`

### Step 1: Add Route
```php
Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convertToSale'])
     ->name('quotations.convert');
```

### Step 2: Add `convertToSale()` Method to `QuotationController.php`

```php
public function convertToSale(Quotation $quotation)
{
    if ($quotation->status === 'Converted') {
        return back()->with('error', 'This quotation has already been converted.');
    }

    // Generate invoice number
    $lastSale = Sale::latest()->first();
    $invoiceNo = 'INV-' . date('Y') . '-' . str_pad(($lastSale?->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

    $sale = Sale::create([
        'customer_id'    => $quotation->customer_id,
        'branch_id'      => $quotation->branch_id,
        'invoice_number' => $invoiceNo,
        'sale_date'      => now()->toDateString(),
        'total_amount'   => $quotation->grand_total,
        'discount_amount'=> $quotation->order_tax_amount ?? 0,
        'tax_amount'     => 0,
        'final_amount'   => $quotation->grand_total,
        'paid_amount'    => 0,
        'due_amount'     => $quotation->grand_total,
        'payment_method' => 'cash',
        'sale_origin'    => 'POS',
        'status'         => 'pending',
        'created_by'     => auth()->id(),
    ]);

    foreach ($quotation->items as $item) {
        SaleItem::create([
            'sale_id'               => $sale->id,
            'product_id'            => $item->product_id,
            'variant_id'            => $item->product_variant_id,
            'quantity'              => $item->quantity,
            'quantity_in_base_unit' => $item->quantity,
            'unit_price'            => $item->unit_price,
            'discount'              => $item->discount_amount,
            'tax'                   => $item->tax_amount,
            'total_price'           => $item->subtotal,
        ]);

        // Deduct inventory
        InventoryStock::where('product_id', $item->product_id)
            ->where('variant_id', $item->product_variant_id)
            ->decrement('quantity_in_base_unit', $item->quantity);

        // Stock ledger
        StockLedger::create([
            'product_id'                => $item->product_id,
            'variant_id'                => $item->product_variant_id,
            'ref_type'                  => 'sale',
            'ref_id'                    => $sale->id,
            'quantity_change_in_base_unit' => $item->quantity,
            'direction'                 => 'out',
            'created_by'                => auth()->id(),
        ]);
    }

    $quotation->update(['status' => 'Converted']);

    return redirect()->route('sales.invoice', $sale->id)
                     ->with('success', 'Quotation converted to sale successfully!');
}
```

### Step 3: Add Button in `quotations/show.blade.php`
```html
@if($quotation->status !== 'Converted')
<form action="{{ route('quotations.convert', $quotation->id) }}" method="POST"
      onsubmit="return confirm('Convert this quotation to a sale?')">
    @csrf
    <button type="submit" class="btn btn-success">
        Convert to Sale
    </button>
</form>
@else
<span class="badge bg-secondary">Already Converted</span>
@endif
```

### Acceptance Criteria:
- [ ] "Convert to Sale" button appears on accepted quotations
- [ ] Clicking converts the quotation and redirects to sale invoice
- [ ] Stock is deducted for all items
- [ ] Quotation status changes to "Converted"
- [ ] Can't convert the same quotation twice

---

## TASK 2.5 — Sales Report Page
**Priority:** P1 | **Effort:** 3 days | **Impact:** Critical — Management visibility

### Files to Create:
- `app/Http/Controllers/ReportController.php`
- `resources/views/reports/sales.blade.php`
- `resources/views/reports/purchases.blade.php`
- `resources/views/reports/expenses.blade.php`

### Files to Modify:
- `routes/web.php`

### Step 1: Add Routes
```php
Route::middleware('role:Admin,Manager,Accountant')->prefix('reports')->name('reports.')->group(function () {
    Route::get('/sales',     [ReportController::class, 'sales'])->name('sales');
    Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
    Route::get('/expenses',  [ReportController::class, 'expenses'])->name('expenses');
    Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit_loss');
});
```

### Step 2: Create `ReportController.php`
```php
<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $from   = $request->get('from', now()->startOfMonth()->toDateString());
        $to     = $request->get('to', now()->toDateString());
        $branch = $request->get('branch_id');
        $method = $request->get('payment_method');

        $query = Sale::whereBetween('sale_date', [$from, $to])
            ->with(['customer', 'branch', 'items'])
            ->when($branch, fn($q) => $q->where('branch_id', $branch))
            ->when($method, fn($q) => $q->where('payment_method', $method));

        $sales        = $query->latest('sale_date')->get();
        $totalRevenue = $sales->sum('final_amount');
        $totalDiscount= $sales->sum('discount_amount');
        $totalTax     = $sales->sum('tax_amount');

        $branches = \App\Models\Branch::all();

        return view('reports.sales', compact('sales', 'totalRevenue', 'totalDiscount', 'totalTax', 'from', 'to', 'branches'));
    }

    public function purchases(Request $request)
    {
        $from     = $request->get('from', now()->startOfMonth()->toDateString());
        $to       = $request->get('to', now()->toDateString());
        $supplier = $request->get('supplier_id');

        $purchases = Purchase::whereBetween('purchase_date', [$from, $to])
            ->with('supplier')
            ->when($supplier, fn($q) => $q->where('supplier_id', $supplier))
            ->latest('purchase_date')
            ->get();

        $totalPurchases = $purchases->sum('total_amount');
        $totalPaid      = $purchases->sum('paid_amount');
        $totalDue       = $purchases->sum('due_amount');
        $suppliers      = \App\Models\Supplier::all();

        return view('reports.purchases', compact('purchases', 'totalPurchases', 'totalPaid', 'totalDue', 'from', 'to', 'suppliers'));
    }

    public function expenses(Request $request)
    {
        $from     = $request->get('from', now()->startOfMonth()->toDateString());
        $to       = $request->get('to', now()->toDateString());
        $branch   = $request->get('branch_id');
        $category = $request->get('category_id');

        $expenses = Expense::whereBetween('expense_date', [$from, $to])
            ->with(['branch', 'category'])
            ->when($branch, fn($q) => $q->where('branch_id', $branch))
            ->when($category, fn($q) => $q->where('category_id', $category))
            ->latest('expense_date')
            ->get();

        $total = $expenses->sum('amount');

        $branches   = \App\Models\Branch::all();
        $categories = \App\Models\ExpenseCategory::all();

        return view('reports.expenses', compact('expenses', 'total', 'from', 'to', 'branches', 'categories'));
    }

    public function profitLoss(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $revenue   = Sale::whereBetween('sale_date', [$from, $to])->sum('final_amount');
        $purchases = Purchase::whereBetween('purchase_date', [$from, $to])->sum('total_amount');
        $expenses  = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $profit    = $revenue - $purchases - $expenses;

        return view('reports.profit-loss', compact('revenue', 'purchases', 'expenses', 'profit', 'from', 'to'));
    }
}
```

### Step 3: Create Report Views (for each: `reports/sales.blade.php`)

Each report view should have:
- **Filter bar** at top: date range pickers, dropdown filters, "Generate Report" button
- **Summary cards**: total revenue, count, averages
- **Data table**: sortable, with totals row at bottom
- **Export buttons**: Print, Download CSV (use `league/csv` or simple response)

### Step 4: Add Reports to Sidebar Navigation

In `resources/views/layouts/sidebar.blade.php`, add:
```html
<li class="nav-item">
    <a class="nav-link" href="{{ route('reports.sales') }}">
        📊 Reports
    </a>
    <ul>
        <li><a href="{{ route('reports.sales') }}">Sales Report</a></li>
        <li><a href="{{ route('reports.purchases') }}">Purchase Report</a></li>
        <li><a href="{{ route('reports.expenses') }}">Expense Report</a></li>
        <li><a href="{{ route('reports.profit_loss') }}">Profit & Loss</a></li>
    </ul>
</li>
```

### Acceptance Criteria:
- [ ] Sales report filters by date range and shows correct totals
- [ ] Purchase report shows supplier-wise totals
- [ ] Expense report filters by branch and category
- [ ] P&L shows: Revenue - Purchases - Expenses = Net Profit
- [ ] Reports are visible only to Admin, Manager, Accountant

---

## TASK 2.6 — Low Stock Email Alert
**Priority:** P1 | **Effort:** 1 day | **Impact:** High — Prevents stock-outs

### Files to Create:
- `app/Mail/LowStockAlert.php`
- `resources/views/emails/low-stock-alert.blade.php`

### Files to Modify:
- `routes/console.php` (schedule the daily job)

### Step 1: Create the Mailable
```bash
php artisan make:mail LowStockAlert
```

Edit `app/Mail/LowStockAlert.php`:
```php
<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function build()
    {
        return $this->subject('⚠️ Low Stock Alert — ' . now()->format('d M Y'))
                    ->view('emails.low-stock-alert');
    }
}
```

### Step 2: Create Email View `resources/views/emails/low-stock-alert.blade.php`
```html
<!DOCTYPE html>
<html>
<body>
    <h2>Low Stock Alert</h2>
    <p>The following products are running low on stock:</p>
    <table border="1" cellpadding="8" style="border-collapse:collapse; width:100%">
        <tr>
            <th>Product</th><th>SKU</th><th>Current Stock</th><th>Min Threshold</th>
        </tr>
        @foreach($products as $product)
        <tr style="{{ $product->inventoryStock?->quantity_in_base_unit == 0 ? 'background:#ffcccc' : 'background:#fff3cd' }}">
            <td>{{ $product->name }}</td>
            <td>{{ $product->sku }}</td>
            <td>{{ $product->inventoryStock?->quantity_in_base_unit ?? 0 }}</td>
            <td>{{ $product->low_stock }}</td>
        </tr>
        @endforeach
    </table>
    <p>Please reorder stock as soon as possible.</p>
</body>
</html>
```

### Step 3: Schedule in `routes/console.php`
```php
use Illuminate\Support\Facades\Schedule;
use App\Models\Product;
use App\Mail\LowStockAlert;
use Illuminate\Support\Facades\Mail;

Schedule::call(function () {
    $lowStock = Product::with('inventoryStock')
        ->whereNotNull('low_stock')
        ->get()
        ->filter(fn($p) => ($p->inventoryStock?->quantity_in_base_unit ?? 0) <= $p->low_stock);

    if ($lowStock->count() > 0) {
        $email = posSetting('default_email');
        if ($email) {
            Mail::to($email)->send(new LowStockAlert($lowStock));
        }
    }
})->dailyAt('08:00')->name('low-stock-alert');
```

### Step 4: Enable Task Scheduler

Add to Windows Task Scheduler or XAMPP startup:
```bash
# Run every minute (Windows cron equivalent):
php artisan schedule:run
```

Or for development, test with:
```bash
php artisan schedule:run --verbose
```

### Acceptance Criteria:
- [ ] `php artisan schedule:run` sends alert email when low stock products exist
- [ ] Email lists each low stock product with current and minimum quantities
- [ ] No email sent when all products are adequately stocked

---

## TASK 2.7 — Product Search on E-Commerce Shop
**Priority:** P1 | **Effort:** 1 day | **Impact:** High — UX improvement

### Files to Modify:
- `app/Http/Controllers/Frontend/StoreController.php`
- `resources/views/store/shop.blade.php`

### What to Do:

**Step 1:** Update `shop()` method in `StoreController.php`:
```php
public function shop(Request $request)
{
    $search   = $request->get('q');
    $category = $request->get('category_id');

    $products = Product::with(['inventoryStock', 'category', 'variants'])
        ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                                    ->orWhere('sku', 'like', "%{$search}%")
                                    ->orWhere('brand', 'like', "%{$search}%"))
        ->when($category, fn($q) => $q->where('category_id', $category))
        ->whereHas('inventoryStock', fn($q) => $q->where('quantity_in_base_unit', '>', 0))
        ->paginate(12);

    $categories = \App\Models\Category::whereNull('parent_id')->get();

    return view('store.shop', compact('products', 'categories', 'search', 'category'));
}
```

**Step 2:** Add search bar to `resources/views/store/shop.blade.php`:
```html
<form method="GET" action="{{ route('store.shop') }}" class="flex gap-2 mb-6">
    <input type="text" name="q" value="{{ $search }}"
           placeholder="Search products..."
           class="border rounded px-4 py-2 w-full">
    <select name="category_id" class="border rounded px-4 py-2">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary">Search</button>
    @if($search || $category)
        <a href="{{ route('store.shop') }}" class="btn btn-secondary">Clear</a>
    @endif
</form>

<!-- Pagination -->
{{ $products->appends(request()->query())->links() }}
```

### Acceptance Criteria:
- [ ] Search box filters products by name, SKU, or brand
- [ ] Category dropdown filters products
- [ ] Results paginate (12 per page)
- [ ] "Clear" button resets all filters
- [ ] URL is bookmarkable (e.g., `/store/shop?q=samsung&category_id=5`)

---

## TASK 2.8 — Order Status Email Notification to Customer
**Priority:** P1 | **Effort:** 1 day | **Impact:** High — Customer experience

### Files to Create:
- `app/Mail/OrderStatusUpdated.php`
- `resources/views/emails/order-status-updated.blade.php`

### Files to Modify:
- `app/Http/Controllers/SaleController.php` (`updateStatus()` method)

### What to Do:

**Step 1:** Create Mailable
```bash
php artisan make:mail OrderStatusUpdated
```

```php
// app/Mail/OrderStatusUpdated.php
class OrderStatusUpdated extends Mailable
{
    public function __construct(public Sale $sale) {}

    public function build()
    {
        return $this->subject('Order #' . $this->sale->invoice_number . ' — Status Updated')
                    ->view('emails.order-status-updated');
    }
}
```

**Step 2:** Update `SaleController::updateStatus()`
```php
public function updateStatus(Request $request, Sale $order)
{
    $request->validate(['status' => 'required|in:pending,processing,shipped,completed,cancelled']);

    $order->update(['status' => $request->status]);

    // Send email to customer if they have an email
    if ($order->customer && $order->customer->email) {
        Mail::to($order->customer->email)->send(new OrderStatusUpdated($order));
    }

    return back()->with('success', 'Order status updated to ' . ucfirst($request->status));
}
```

### Acceptance Criteria:
- [ ] Updating order status in admin panel sends email to customer
- [ ] Email shows order number, new status, and items
- [ ] No crash if customer has no email

---

## ✅ PHASE 2 COMPLETE CHECKLIST

- [ ] 2.1 — RBAC middleware implemented and applied
- [ ] 2.2 — Branch-scoped data filtering working
- [ ] 2.3 — PDF invoices downloadable for sales/purchases/quotations
- [ ] 2.4 — Quotation to Sale conversion working
- [ ] 2.5 — Reports: Sales, Purchases, Expenses, P&L
- [ ] 2.6 — Low stock email alert scheduled
- [ ] 2.7 — Product search + pagination on store
- [ ] 2.8 — Order status email to customer

**Run after Phase 2:**
```bash
php artisan route:list | grep reports   # Verify report routes
php artisan schedule:list               # Verify scheduled jobs
php artisan optimize
```

---

# ═══════════════════════════════════════
# PHASE 3 — MAJOR FEATURES (Month 2–3)
# ═══════════════════════════════════════
> **Goal:** New modules and significant system expansions.

---

## TASK 3.1 — Stock Transfer Between Warehouses

### Files to Create:
- Migration: `create_stock_transfers_table`
- `app/Models/StockTransfer.php`
- `app/Http/Controllers/StockTransferController.php`
- `resources/views/stock/transfer.blade.php`

### Step 1: Create Migration
```bash
php artisan make:migration create_stock_transfers_table
```
```php
Schema::create('stock_transfers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('from_warehouse_id')->constrained('warehouses');
    $table->foreignId('to_warehouse_id')->constrained('warehouses');
    $table->foreignId('product_id')->constrained('products');
    $table->foreignId('variant_id')->nullable()->constrained('product_variants');
    $table->decimal('quantity', 12, 4);
    $table->string('transfer_reference')->unique();
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
```

### Step 2: Create `StockTransferController.php`
```php
public function store(Request $request)
{
    $request->validate([
        'from_warehouse_id' => 'required|different:to_warehouse_id',
        'to_warehouse_id'   => 'required',
        'product_id'        => 'required',
        'quantity'          => 'required|numeric|min:0.01',
    ]);

    // Check source has enough stock
    $sourceStock = InventoryStock::where('product_id', $request->product_id)
        ->where('variant_id', $request->variant_id)
        ->where('warehouse_id', $request->from_warehouse_id)
        ->firstOrFail();

    if ($sourceStock->quantity_in_base_unit < $request->quantity) {
        return back()->withErrors(['quantity' => 'Insufficient stock in source warehouse.']);
    }

    DB::transaction(function () use ($request, $sourceStock) {
        // Deduct from source
        $sourceStock->decrement('quantity_in_base_unit', $request->quantity);

        // Add to destination
        InventoryStock::updateOrCreate(
            ['product_id' => $request->product_id, 'variant_id' => $request->variant_id, 'warehouse_id' => $request->to_warehouse_id],
            ['quantity_in_base_unit' => DB::raw("quantity_in_base_unit + {$request->quantity}")]
        );

        // Ledger: OUT from source
        StockLedger::create(['product_id' => $request->product_id, 'variant_id' => $request->variant_id, 'warehouse_id' => $request->from_warehouse_id, 'ref_type' => 'transfer', 'ref_id' => 0, 'quantity_change_in_base_unit' => $request->quantity, 'direction' => 'out', 'created_by' => auth()->id()]);

        // Ledger: IN to destination
        StockLedger::create(['product_id' => $request->product_id, 'variant_id' => $request->variant_id, 'warehouse_id' => $request->to_warehouse_id, 'ref_type' => 'transfer', 'ref_id' => 0, 'quantity_change_in_base_unit' => $request->quantity, 'direction' => 'in', 'created_by' => auth()->id()]);

        // Save transfer record
        StockTransfer::create([...$request->only(['from_warehouse_id','to_warehouse_id','product_id','variant_id','quantity','notes']), 'transfer_reference' => 'TRF-'.date('Y').'-'.uniqid(), 'created_by' => auth()->id()]);
    });

    return redirect()->route('stock.list')->with('success', 'Stock transferred successfully!');
}
```

### Acceptance Criteria:
- [ ] Transfer reduces stock in source warehouse
- [ ] Transfer adds stock to destination warehouse
- [ ] Two stock ledger entries created (in + out)
- [ ] Can't transfer more than available stock

---

## TASK 3.2 — Hardware Barcode Scanner Integration (POS)

### Files to Modify:
- `resources/views/pos/index.blade.php` (or wherever POS JS lives)

### What to Do:
Add this JavaScript at the bottom of the POS page:

```javascript
// Barcode scanner listener — USB scanners type fast + press Enter
let barcodeBuffer = '';
let barcodeTimeout = null;

document.addEventListener('keydown', function(e) {
    // Only capture when search input is NOT focused
    const searchInput = document.getElementById('product-search');
    if (document.activeElement === searchInput) return;

    clearTimeout(barcodeTimeout);

    if (e.key === 'Enter') {
        if (barcodeBuffer.length >= 6) {
            searchByBarcode(barcodeBuffer.trim());
        }
        barcodeBuffer = '';
    } else if (e.key.length === 1) {
        barcodeBuffer += e.key;
    }

    // Reset buffer if no keypress for 100ms (scanner finishes in < 50ms)
    barcodeTimeout = setTimeout(() => { barcodeBuffer = ''; }, 100);
});

function searchByBarcode(barcode) {
    fetch(`/api/search-products?barcode=${encodeURIComponent(barcode)}`)
        .then(res => res.json())
        .then(data => {
            if (data.product) {
                addToCart(data.product);
                // Flash confirmation
                showToast('Product added: ' + data.product.name);
            } else {
                showToast('Product not found for barcode: ' + barcode, 'error');
            }
        });
}
```

### Acceptance Criteria:
- [ ] Scanning a barcode with USB scanner adds product to POS cart
- [ ] If barcode not found, shows an error toast
- [ ] Does not interfere with keyboard typing in search input

---

## TASK 3.3 — Full Inventory Valuation Report

Add to `ReportController.php`:
```php
public function inventoryValuation()
{
    $stocks = InventoryStock::with(['product.baseUnit', 'variant', 'warehouse'])
        ->where('quantity_in_base_unit', '>', 0)
        ->get();

    $valuation = $stocks->map(function ($stock) {
        $cost  = PurchaseItem::where('product_id', $stock->product_id)
                    ->where('variant_id', $stock->variant_id)
                    ->avg('unit_cost') ?? 0;
        return [
            'product'   => $stock->product->name,
            'variant'   => $stock->variant?->variant_name,
            'warehouse' => $stock->warehouse?->name,
            'quantity'  => $stock->quantity_in_base_unit,
            'avg_cost'  => $cost,
            'total_value' => $stock->quantity_in_base_unit * $cost,
        ];
    });

    $totalValue = $valuation->sum('total_value');

    return view('reports.inventory-valuation', compact('valuation', 'totalValue'));
}
```

---

## TASK 3.4 — Encrypt Mail Password in DB
**Priority:** P2 | **Effort:** 2 hours | **Impact:** Medium Security

### Files to Modify:
- `app/Models/MailSetting.php`
- `app/Http/Controllers/SettingController.php`
- `app/Providers/MailConfigServiceProvider.php`
- Run a migration to re-encrypt existing data

### What to Do:

**Step 1:** Add encryption cast to `MailSetting` model:
```php
protected $casts = [
    'mail_password' => 'encrypted',  // Laravel auto-encrypts/decrypts
];
```

**Step 2:** In `MailConfigServiceProvider::boot()`, password is now auto-decrypted by Eloquent — no changes needed there.

**Step 3:** In `SettingController::saveMailSettings()`, the password saves encrypted automatically.

**Step 4:** Update the existing seeder/existing DB record:
```bash
php artisan tinker
>>> App\Models\MailSetting::first()->update(['mail_password' => 'your-password']);
```

### Acceptance Criteria:
- [ ] `mail_password` column in DB shows encrypted string (not plaintext)
- [ ] Mail still sends correctly (encryption is transparent)

---

## ✅ PHASE 3 COMPLETE CHECKLIST

- [ ] 3.1 — Stock transfer between warehouses working
- [ ] 3.2 — Barcode scanner JS integration in POS
- [ ] 3.3 — Inventory valuation report
- [ ] 3.4 — Mail password encrypted in DB

---

# ═══════════════════════════════════════
# PHASE 4 — FUTURE VISION (Month 3–6)
# ═══════════════════════════════════════
> **Goal:** Advanced integrations and scalability.

---

## TASK 4.1 — Online Payment Gateway (JazzCash / EasyPaisa)

### Approach:
1. Install a Pakistan payment gateway SDK or use their REST API directly
2. On checkout, redirect customer to payment gateway
3. On callback/webhook: mark Sale as paid, send confirmation email
4. Store `transaction_id` in a new `payment_transactions` table

### Key Files:
- New `PaymentGatewayController.php`
- New migration: `add_transaction_id_to_payments_table`
- New route: `POST /store/checkout/payment-callback`

---

## TASK 4.2 — WhatsApp Invoice Sharing

### Approach:
1. Use WhatsApp Business API (Twilio or official Meta API)
2. Add "Share via WhatsApp" button on sale invoice page
3. Generate PDF invoice → send as attachment via API

```php
// Pseudo code:
public function shareViaWhatsApp(Sale $sale)
{
    $pdf = Pdf::loadView('pdf.sale-invoice', compact('sale'))->output();
    $client = new TwilioClient(config('twilio.sid'), config('twilio.token'));
    $client->messages->create(
        'whatsapp:+92' . $sale->customer->phone,
        ['from' => 'whatsapp:' . config('twilio.from'), 'body' => 'Your invoice is attached.', 'mediaUrl' => ...]
    );
}
```

---

## TASK 4.3 — SMS Notifications (Jazz / Telenor / Ufone)

### Approach:
1. Register with a Pakistani SMS gateway (e.g., EcoSMS, Zong, or DigitalSMS.pk)
2. Create `SmsService.php` with `send($phone, $message)` method
3. Trigger SMS on: Order placed, Order status updated, Payment received

```php
// app/Services/SmsService.php
class SmsService
{
    public function send(string $phone, string $message): bool
    {
        $response = Http::get('https://api.smspk.net/sms/send', [
            'api_key' => config('services.sms.key'),
            'to'      => '0092' . ltrim($phone, '0'),
            'message' => $message,
        ]);
        return $response->successful();
    }
}
```

---

## TASK 4.4 — REST API for Mobile App

### What to Create:
- `routes/api.php` with v1 prefix
- `app/Http/Controllers/Api/V1/` namespace
- Laravel Sanctum for API token authentication

### Key Endpoints:
```
POST   /api/v1/auth/login          → Return API token
GET    /api/v1/products            → Product list with stock
GET    /api/v1/products/{id}       → Product detail
POST   /api/v1/sales               → Create POS sale
GET    /api/v1/dashboard/stats     → Dashboard KPIs
GET    /api/v1/inventory/stock     → Current stock levels
POST   /api/v1/purchases           → Create purchase order
```

---

# GLOBAL COMMANDS REFERENCE

```bash
# After any code change:
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# After adding migrations:
php artisan migrate

# After adding new seeder:
php artisan db:seed --class=NewSeederName

# After adding new routes:
php artisan route:list

# Check scheduled tasks:
php artisan schedule:list

# Run scheduler (development):
php artisan schedule:run --verbose

# Install PDF package (Phase 2):
composer require barryvdh/laravel-dompdf

# Full fresh setup:
php artisan migrate:fresh --seed
```

---

# OVERALL PROGRESS TRACKER

## Phase 1 — Quick Wins
| # | Task | Status |
|---|------|--------|
| 1.1 | Fix inactive user login | ✅ Done |
| 1.2 | Fix GET-based deletes | ✅ Done |
| 1.3 | Disable public registration | ✅ Done |
| 1.4 | Fix `getIsLowStockAttribute()` | ✅ Done |
| 1.5 | Cache `posSetting()` | ✅ Done |
| 1.6 | Remove dead imports | ✅ Done |
| 1.7 | Customer self-registration | ✅ Done |
| 1.8 | Customer forgot password | ✅ Done |
| 1.9 | Customer order history | ✅ Done |
| 1.10 | Fix N+1 stock list | ✅ Done |

## Phase 2 — Core Enhancements
| # | Task | Status |
|---|------|--------|
| 2.1 | RBAC middleware | ⬜ Pending |
| 2.2 | Branch-scoped data | ⬜ Pending |
| 2.3 | PDF invoices (dompdf) | ⬜ Pending |
| 2.4 | Convert quotation to sale | ⬜ Pending |
| 2.5 | Sales/Purchase/Expense reports | ⬜ Pending |
| 2.6 | Low stock email alert | ⬜ Pending |
| 2.7 | Store search + pagination | ⬜ Pending |
| 2.8 | Order status email | ⬜ Pending |

## Phase 3 — Major Features
| # | Task | Status |
|---|------|--------|
| 3.1 | Stock transfer | ⬜ Pending |
| 3.2 | Barcode scanner JS | ⬜ Pending |
| 3.3 | Inventory valuation report | ⬜ Pending |
| 3.4 | Encrypt mail password | ⬜ Pending |

## Phase 4 — Future Vision
| # | Task | Status |
|---|------|--------|
| 4.1 | JazzCash payment gateway | ⬜ Future |
| 4.2 | WhatsApp invoice sharing | ⬜ Future |
| 4.3 | SMS notifications | ⬜ Future |
| 4.4 | REST API for mobile | ⬜ Future |

---

> Update status: ⬜ Pending → 🔄 In Progress → ✅ Done → ❌ Blocked
