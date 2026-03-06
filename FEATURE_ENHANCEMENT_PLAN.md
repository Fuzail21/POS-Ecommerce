# FEATURE ENHANCEMENT PLAN
> **POS-Ecommerce** — Improvement Roadmap & Gap Analysis
> Last Updated: February 2026

---

## 1. Module Health Assessment

| Module | Code Quality (1-10) | UI/UX Score (1-10) | Feature Completeness (1-10) | Priority |
|--------|--------------------|--------------------|------------------------------|----------|
| Authentication (Admin) | 8 | 7 | 9 | Low |
| Authentication (Customer) | 6 | 6 | 5 | High |
| Dashboard & Analytics | 7 | 6 | 6 | High |
| POS Terminal | 7 | 7 | 7 | Medium |
| E-Commerce Store | 6 | 5 | 5 | High |
| Product Management | 7 | 6 | 7 | Medium |
| Inventory Management | 7 | 5 | 6 | Medium |
| Purchase Management | 7 | 6 | 7 | Low |
| Sales Management | 7 | 6 | 7 | Medium |
| Sales Returns | 6 | 5 | 6 | Medium |
| Customer Management | 6 | 6 | 6 | Medium |
| Supplier Management | 7 | 6 | 6 | Low |
| Finance / Payments | 6 | 5 | 5 | High |
| Expense Management | 7 | 6 | 7 | Low |
| Quotations | 6 | 5 | 5 | Medium |
| Discount Rules | 7 | 6 | 7 | Medium |
| User & Role Management | 5 | 6 | 4 | Critical |
| Warehouse & Branch | 7 | 6 | 7 | Low |
| Settings | 7 | 6 | 7 | Low |
| Units | 8 | 7 | 8 | Low |

---

## 2. UI/UX Improvement Suggestions

---

### Dashboard & Analytics

**Current State:**
Shows basic KPI cards and a weekly chart. Data displayed but layout is functional-only with no visual hierarchy or color-coded status indicators.

**Pain Points:**
- No date range filter — always shows hardcoded "last 7 days"
- Low stock alerts are listed but have no urgent visual indicator (no red badge, no sound)
- No comparison to previous period (e.g., "↑ 12% vs last week")
- No revenue goal / target tracking
- Charts are basic — no drill-down capability

**Suggested Improvements:**
- Add date range picker (Today / This Week / This Month / Custom Range)
- Add period-over-period comparison badges (green ↑ / red ↓)
- Color-code low stock items: yellow (warning) → red (critical)
- Add clickable chart bars that navigate to filtered sales list
- Add a "Quick Actions" panel: Open Register, New Sale, New Purchase, New Quotation
- Add revenue target progress bar (configurable in Settings)
- Add live clock showing current date/time for cashiers

---

### POS Terminal

**Current State:**
Functional POS with product search and cart. Works but lacks modern POS UX patterns.

**Pain Points:**
- No numeric keypad for quantity input on touch screens
- Product images not visible in cart line items
- No keyboard shortcut support (e.g., F1=search, F2=checkout, ESC=clear)
- Discount/tax applied per-order only — no per-item quick discount
- No split payment UI (e.g., partial cash + partial card)
- No "hold sale" / "park sale" functionality
- Receipt print button not clearly visible after sale
- No customer search/autocomplete in POS (must type full name)

**Suggested Improvements:**
- Add on-screen numpad widget for quantity adjustment
- Show product thumbnails in cart rows
- Implement keyboard shortcuts with a visible cheat sheet
- Add per-item inline discount field
- Build split payment modal with amount fields per method
- Add "Hold Sale" button that saves current cart to session with a name/number
- Add customer autocomplete search with phone lookup
- Auto-focus search bar on page load and after each item addition
- Show receipt preview before printing (modal)

---

### E-Commerce Store

**Current State:**
Basic product listing, cart, and checkout. Minimal styling with no modern e-commerce UX.

**Pain Points:**
- No product image gallery (single image only)
- No stock count shown on product page ("Only 3 left!")
- No "Related Products" or "You may also like" section
- Cart has no mini-cart dropdown in header
- No order history page for customers (`/store/orders`)
- No order tracking status page
- No guest checkout option (must log in)
- No pagination or infinite scroll on shop page
- No product search bar on the shop page
- No breadcrumbs on product pages
- Checkout form has no address autofill
- No COD (Cash on Delivery) option — only online payment shown

**Suggested Improvements:**
- Add image gallery/slider with zoom on product page
- Show real-time stock indicator ("In Stock", "Low Stock", "Out of Stock")
- Add "Related Products" section (same category)
- Implement sticky mini-cart header icon with item count badge
- Build `/store/orders` page showing customer order history with status
- Add product search with live AJAX suggestions
- Add pagination or lazy loading to `/store/shop`
- Add breadcrumbs: Home > Category > Product Name
- Add "Add to Wishlist" button
- Add product reviews/ratings placeholder
- Add guest checkout flow or "Continue as Guest" option
- Add COD payment option

---

### Finance / Payments

**Current State:**
Payments can be recorded but there is no comprehensive financial report or balance sheet.

**Pain Points:**
- No profit & loss report
- No receivables vs payables summary
- Customer and supplier balance displayed nowhere at a glance
- No aging report (overdue invoices)
- Payment listing has no filter by customer/supplier/date

**Suggested Improvements:**
- Add Profit & Loss report page (revenue - COGS - expenses)
- Add Receivables report (customers with outstanding balances)
- Add Payables report (suppliers with outstanding balances)
- Add payment listing filters: date range, entity type, method
- Show balance prominently on customer/supplier profile pages
- Add "Mark as Paid" quick action on unpaid invoices

---

### Sales Returns

**Current State:**
Returns can be created but the UI for creating a return is basic. No bulk return option.

**Pain Points:**
- No way to partially return some items from an invoice
- Return reason is a free-text field — no dropdown options
- No return policy date validation (e.g., can't return after 30 days)
- Return list has no filtering

**Suggested Improvements:**
- Show invoice items as checkboxes with quantity inputs for partial returns
- Add predefined return reason dropdown (Defective, Wrong Item, Customer Changed Mind, etc.)
- Add configurable return window in Settings
- Add return policy enforcement in controller

---

### Inventory Management

**Current State:**
Stock list and ledger exist but are read-only. No visual low stock dashboard.

**Pain Points:**
- Stock ledger has no filters (can't filter by product, date, or type)
- No low-stock reorder alert email
- No stock transfer between warehouses UI
- Supplier product report exists but isn't linked from stock pages
- Stock adjustment (manual increase/decrease) not visible in the navigation

**Suggested Improvements:**
- Add filters to stock ledger: product, warehouse, date range, direction
- Add "Stock Transfer" form between warehouses with ledger entry
- Add email alert when stock drops below `low_stock` threshold
- Add "Reorder" quick link from low stock items to create a purchase
- Make stock adjustment accessible from the stock list page

---

## 3. Feature Gap Analysis

### User & Role Management

**Missing Features:**
- **Role-based authorization** — roles exist in DB but zero enforcement via middleware/gates/policies. Any logged-in user can access any admin route. 🐛
- **Permissions system** — no granular permissions per module

**Incomplete Features:**
- User status (`Active`/`Inactive`) exists but inactive users are not blocked from logging in 🐛
- Branch assignment exists but not used to filter data (e.g., a Lahore cashier can see Karachi sales)

**Suggested Implementation:**
```php
// In middleware or controller:
if (auth()->user()->status !== 'Active') {
    Auth::logout();
    return redirect('/')->with('error', 'Account is inactive.');
}
```

---

### Customer Authentication

**Missing Features:**
- Self-registration is commented out — customers must be created by admin 🐛
- No email verification for customers
- No "Forgot Password" for customers
- No social login (Google/Facebook)

---

### E-Commerce Orders

**Missing Features:**
- No customer-facing order history at `/store/orders` (route not defined)
- No order tracking page with status timeline
- No email notification when admin updates order status
- No invoice download as PDF for customers

---

### Quotations

**Missing Features:**
- No "Convert Quotation to Sale" button
- No PDF download of quotation
- No quotation expiry enforcement (status doesn't auto-expire)
- Quotation items don't validate against current stock

---

### Reporting

**Missing Entirely:**
- No sales report by date range, product, category, or branch
- No purchase report
- No expense summary report
- No profit & loss statement
- No tax report
- No inventory valuation report
- No customer statement (full ledger per customer)
- No supplier statement
- No cash register history report

---

### API

**Missing Entirely:**
- No REST API (`api.php` not configured)
- No mobile app support
- No webhook support for payment gateways

---

## 4. New Feature Suggestions

---

### Feature 1: Role-Based Access Control (RBAC) Enforcement

**Module:** User & Role Management
**Problem:** Roles exist in DB but are purely cosmetic — all authenticated users have full access.
**User Story:** "As an admin, I want cashiers to only access POS and sales, so that sensitive data stays protected."
**Complexity:** Medium | **Impact:** Critical

**Technical Approach:**
```php
// Create RoleMiddleware
class RoleMiddleware {
    public function handle($request, Closure $next, ...$roles) {
        if (!auth()->user() || !in_array(auth()->user()->role->name, $roles)) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}

// Apply to routes:
Route::middleware(['auth', 'role:Admin,Manager'])->group(function () {
    // Sensitive routes
});
```

**Dependencies:** `Role` model, `User` model, new `RoleMiddleware`

---

### Feature 2: Customer Self-Registration & Password Reset

**Module:** E-Commerce Authentication
**Problem:** Customers can't register themselves; admin must create accounts.
**User Story:** "As a customer, I want to register and recover my password, so that I can shop without admin help."
**Complexity:** Low | **Impact:** High

**Technical Approach:**
- Uncomment registration routes in `web.php`
- Add `CustomerPasswordResetController` similar to admin reset flow
- Add `ForgotPassword` view under `store/auth/`
- Send reset email using existing mail configuration

---

### Feature 3: PDF Invoice & Report Generation

**Module:** Sales, Purchases, Quotations
**Problem:** No downloadable PDF invoices — only print-view HTML pages.
**User Story:** "As a manager, I want to download PDF invoices to email to customers and suppliers."
**Complexity:** Medium | **Impact:** High

**Technical Approach:**
- Install `barryvdh/laravel-dompdf` package
- Add `->download('invoice.pdf')` method to invoice controllers
- Create clean print-optimized Blade templates for PDF rendering

**Dependencies:** `barryvdh/laravel-dompdf`, existing invoice views

---

### Feature 4: Customer Order History Page

**Module:** E-Commerce Store
**Problem:** After placing an order, customers have no way to view past orders or track status.
**User Story:** "As a customer, I want to view my order history and track delivery status."
**Complexity:** Low | **Impact:** High

**Technical Approach:**
```php
// Add to web.php (inside auth:customer group):
Route::get('/orders', [CheckoutController::class, 'orders'])->name('store.orders');
Route::get('/orders/{sale}', [CheckoutController::class, 'orderDetail'])->name('store.order.detail');

// Controller:
public function orders() {
    $orders = Sale::where('customer_id', auth('customer')->id())
                  ->where('sale_origin', 'Ecommerce')
                  ->latest()->paginate(10);
    return view('store.orders', compact('orders'));
}
```

---

### Feature 5: Low Stock Email Alerts

**Module:** Inventory Management
**Problem:** Low stock is shown on dashboard but no proactive notification sent.
**User Story:** "As a manager, I want email alerts when products fall below their low stock threshold."
**Complexity:** Medium | **Impact:** High

**Technical Approach:**
- Create `LowStockAlert` Mailable
- Schedule a daily job via `app/Console/Kernel.php`:
  ```php
  $schedule->daily()->call(function() {
      $lowStockProducts = Product::whereHas('inventoryStock', function($q) {
          $q->whereRaw('quantity_in_base_unit <= products.low_stock');
      })->get();
      if ($lowStockProducts->count()) {
          Mail::to(posSetting('default_email'))->send(new LowStockAlert($lowStockProducts));
      }
  });
  ```
- Create Blade email template

---

### Feature 6: Convert Quotation to Sale

**Module:** Quotations
**Problem:** Quotations are dead-end documents — can't be turned into actual sales.
**User Story:** "As a salesperson, I want to convert an accepted quotation directly into a sale."
**Complexity:** Medium | **Impact:** High

**Technical Approach:**
```php
// In QuotationController:
public function convertToSale(Quotation $quotation) {
    $sale = Sale::create([
        'customer_id'    => $quotation->customer_id,
        'total_amount'   => $quotation->grand_total,
        'sale_origin'    => 'POS',
        // ...map other fields
    ]);
    foreach ($quotation->items as $item) {
        SaleItem::create([...]);
        // Deduct stock, create ledger entry
    }
    $quotation->update(['status' => 'Converted']);
    return redirect()->route('sales.invoice', $sale->id);
}
```

---

### Feature 7: Stock Transfer Between Warehouses

**Module:** Inventory Management
**Problem:** No mechanism to transfer stock between warehouses (e.g., Lahore → Karachi).
**User Story:** "As an inventory manager, I want to move stock between branches/warehouses."
**Complexity:** Medium | **Impact:** Medium

**Technical Approach:**
- New `stock_transfers` table: (from_warehouse_id, to_warehouse_id, product_id, variant_id, quantity, notes, created_by)
- `StockTransferController` with store() method:
  - Deduct from source `inventory_stocks`
  - Add to destination `inventory_stocks`
  - Create two `StockLedger` entries: `direction=out` (source) and `direction=in` (destination) with `ref_type='transfer'`

---

### Feature 8: Sales Reports & Analytics

**Module:** New — Reports Module
**Problem:** Zero reporting capability beyond the basic dashboard.
**User Story:** "As a manager, I want to generate monthly sales reports filtered by branch, product, and date."
**Complexity:** Medium | **Impact:** Critical

**Technical Approach:**
- New `ReportController` with methods:
  - `salesReport()` — filter by date, branch, customer, payment method
  - `purchaseReport()` — filter by date, supplier
  - `expenseReport()` — filter by date, branch, category
  - `profitLoss()` — revenue - COGS - expenses
  - `inventoryValuation()` — stock × purchase cost
- Use Laravel Query Builder with conditional `->when()` filters
- Add export to CSV/PDF

---

### Feature 9: Barcode Scanner Hardware Integration

**Module:** POS Terminal
**Problem:** Barcode scanning exists as an API but relies on manual input only.
**User Story:** "As a cashier, I want to scan physical barcodes with a USB scanner and have products auto-added to cart."
**Complexity:** Low | **Impact:** High

**Technical Approach:**
- Add JavaScript keydown listener for barcode scanner events (scanners type barcode + Enter very fast):
```javascript
let barcodeBuffer = '';
let barcodeTimeout;
document.addEventListener('keydown', (e) => {
    clearTimeout(barcodeTimeout);
    if (e.key !== 'Enter') barcodeBuffer += e.key;
    else {
        if (barcodeBuffer.length >= 6) searchByBarcode(barcodeBuffer);
        barcodeBuffer = '';
    }
    barcodeTimeout = setTimeout(() => barcodeBuffer = '', 100);
});
```
- `GET /api/search-products?barcode={code}` already exists; connect scanner to it

---

### Feature 10: Multi-Currency Support

**Module:** Settings, Sales, E-Commerce
**Problem:** Currency is hardcoded as PKR; no conversion for international orders.
**User Story:** "As a store owner, I want to accept payments in multiple currencies."
**Complexity:** High | **Impact:** Low (Pakistan-focused app)

---

## 5. Technical Debt & Refactoring Opportunities

### 🐛 Bugs & Logic Errors

| # | Issue | Location | Fix |
|---|-------|----------|-----|
| 1 | Inactive users can still log in | `UserController`, `User` model | Add status check in `LoginRequest` or middleware |
| 2 | Customer self-registration commented out | `routes/web.php` | Uncomment + add CSRF, validation |
| 3 | Role enforcement missing | All admin controllers | Add `RoleMiddleware` |
| 4 | Unit routes use GET for destroy | `routes/web.php` | Change to DELETE method |
| 5 | Category routes use GET for destroy | `routes/web.php` | Change to DELETE method |
| 6 | Units destroy and edit share same GET `/{id}` pattern | `routes/web.php` | Route conflict risk — differentiate paths |
| 7 | `Product::getIsLowStockAttribute()` hardcodes 5 | `app/Models/Product.php:58` | Use `$this->low_stock` column instead |
| 8 | `posSetting()` calls `Setting::first()` on every invocation | `app/Helpers/SettingHelper.php` | Add static caching: `static $setting = null` |

### ⚠️ Security Issues

| # | Issue | Severity | Fix |
|---|-------|----------|-----|
| 1 | No RBAC enforcement — all authenticated users have full admin access | **Critical** | Implement `RoleMiddleware` |
| 2 | DELETE operations use GET requests (e.g., `/user/delete/{id}`) | **High** | Change to DELETE HTTP method + use `@method('DELETE')` in forms |
| 3 | No CSRF on some AJAX cart operations | **Medium** | Ensure `X-CSRF-TOKEN` header sent with all AJAX requests |
| 4 | Customer passwords not enforced for minimum strength | **Medium** | Add password validation rules |
| 5 | Mail password stored as plaintext in `mail_settings` table | **Medium** | Encrypt using `encrypt()`/`decrypt()` |
| 6 | Product images uploaded without type/size validation | **Medium** | Add MIME type check + max size limit |
| 7 | Admin registration open (`/register` route public) | **High** | Disable registration route or add invite-only flow after initial setup |

### Code Smells & Refactoring

| Issue | Location | Recommendation |
|-------|----------|---------------|
| Business logic in controllers | All controllers | Extract to Service classes (e.g., `SaleService`, `StockService`) |
| `DB::table()` raw inserts in seeders | All seeders | Use model factories for better maintainability |
| No Form Request classes for most forms | All controllers | Create dedicated `StoreProductRequest`, `StoreSaleRequest`, etc. |
| Dead imports in `web.php` | `routes/web.php:5-8` | `CompanyController`, `VendorController`, `LoanController` imported but unused |
| `SettingHelper::posSetting()` queries DB every call | `app/Helpers/SettingHelper.php` | Cache with `Cache::remember('settings', 3600, ...)` |
| No tests written | `tests/` directory empty | Add Feature tests for critical flows (POS sale, checkout) |
| `SaleController` handles both POS and manual sales in same methods | `SaleController.php` | Split into `POSSaleController` and `ManualSaleController` |
| Stock ledger entries created inline in controllers | Multiple controllers | Create `StockService::recordMovement()` method |
| No API versioning | `routes/api.php` (missing) | Create `routes/api.php` with v1 prefix |

### Missing Tests

- Zero test coverage across the entire application
- Critical flows that must be tested:
  - POS checkout reduces inventory correctly
  - Sales return restores inventory
  - Coupon code validation (expired, wrong type, valid)
  - Cash register open/close
  - Customer checkout creates sale with correct totals
  - Discount rule application (category, product, coupon)

### Performance Bottlenecks

| Issue | Location | Fix |
|-------|----------|-----|
| `N+1` query on stock list | `StockAdjustmentController::stockIndex()` | Eager load `->with(['product', 'variant', 'warehouse'])` |
| `Product::getDiscountedPriceAttribute()` queries all discount rules on each product | `app/Models/Product.php` | Cache discount rules in memory for the request lifecycle |
| `posSetting()` hits DB every time | `app/Helpers/SettingHelper.php` | Use `Cache::remember()` |
| No database indexes on frequently queried columns | Migrations | Add index on `sale_origin`, `status`, `sale_date`, `customer_id` in `sales` |

---

## 6. Implementation Roadmap

---

### Phase 1: Quick Wins (1–2 Weeks)

**Goal:** Fix critical bugs, security issues, and obvious UX gaps with minimal effort.

| Task | Effort | Impact |
|------|--------|--------|
| Fix inactive user login (check `status` in login flow) | 1 hr | Critical |
| Change all GET-based deletes to DELETE HTTP method | 2 hrs | High |
| Disable or protect `/register` route | 30 min | High |
| Fix `getIsLowStockAttribute()` to use `$this->low_stock` | 15 min | Medium |
| Add static caching to `posSetting()` helper | 30 min | Medium |
| Remove dead imports from `routes/web.php` | 15 min | Low |
| Enable customer self-registration + forgot password | 3 hrs | High |
| Add customer order history page `/store/orders` | 3 hrs | High |
| Fix route naming conflicts for units/categories destroy | 1 hr | Medium |
| Add eager loading to stock list (N+1 fix) | 1 hr | Medium |
| Encrypt mail password in `mail_settings` table | 2 hrs | Medium |

---

### Phase 2: Core Enhancements (2–4 Weeks)

**Goal:** Fill critical feature gaps and improve data quality.

| Task | Effort | Impact |
|------|--------|--------|
| Implement Role-Based Access Control middleware | 1 week | Critical |
| Branch-scoped data filtering (cashier sees only own branch) | 3 days | High |
| PDF invoice generation (dompdf) for sales + purchases | 3 days | High |
| Convert Quotation to Sale button | 1 day | High |
| Sales report page (date range, branch, product filters) | 3 days | High |
| Expense & purchase summary reports | 2 days | High |
| Add Form Request validation classes for all major forms | 3 days | Medium |
| Low stock email alert (scheduled job) | 1 day | High |
| Product search bar on e-commerce shop page | 1 day | High |
| Pagination on `/store/shop` | 1 day | Medium |
| Order status email notification to customer | 1 day | High |
| Split payment support in POS | 2 days | Medium |

---

### Phase 3: Major Features (1–2 Months)

**Goal:** Add new modules and significant functionality.

| Task | Effort | Impact |
|------|--------|--------|
| Full Reports Module (P&L, Receivables, Payables, Tax, Inventory Valuation) | 2 weeks | Critical |
| Stock Transfer between warehouses | 1 week | High |
| REST API (`routes/api.php`) for mobile app integration | 2 weeks | High |
| Hardware barcode scanner integration (JS keydown listener) | 2 days | High |
| Customer loyalty / points system (card_id integration) | 1 week | Medium |
| Product image gallery (multiple images per product) | 3 days | Medium |
| E-Commerce product reviews and ratings | 1 week | Medium |
| "Hold Sale" / Park Sale in POS | 2 days | Medium |
| Service layer refactoring (`SaleService`, `StockService`, `PaymentService`) | 2 weeks | Medium |
| Feature tests for critical flows | 1 week | Medium |

---

### Phase 4: Future Vision (3–6 Months)

**Goal:** Advanced features, scalability, and intelligence.

| Task | Effort | Impact |
|------|--------|--------|
| WhatsApp invoice sharing (Twilio / WhatsApp Business API) | 2 weeks | High |
| Online payment gateway integration (JazzCash, EasyPaisa, Stripe) | 3 weeks | Critical |
| Mobile POS app (React Native or Flutter using API) | 2 months | High |
| AI-powered demand forecasting (low stock prediction) | 1 month | Medium |
| Multi-tenancy support (SaaS mode — multiple businesses) | 3 months | High |
| Advanced analytics dashboard (Chart.js → Apache ECharts) | 2 weeks | Medium |
| Customer behavior analytics (most viewed products, cart abandonment) | 2 weeks | Medium |
| Automatic purchase order generation when stock is low | 1 week | Medium |
| SMS notifications via Jazz, Telenor, or Ufone API | 1 week | High |
| Accounting module integration (double-entry bookkeeping) | 2 months | High |

---

## 7. Priority Matrix

| Feature / Improvement | Effort | Impact | Priority | Phase |
|-----------------------|--------|--------|----------|-------|
| RBAC Enforcement | Medium | Critical | P0 | 2 |
| Fix inactive user login | Low | Critical | P0 | 1 |
| Fix GET-based deletes | Low | High | P0 | 1 |
| Customer self-registration | Low | High | P1 | 1 |
| Customer order history page | Low | High | P1 | 1 |
| PDF Invoice generation | Medium | High | P1 | 2 |
| Sales Reports Module | Medium | Critical | P1 | 2 |
| Low stock email alert | Low | High | P1 | 2 |
| Branch-scoped data | Medium | High | P1 | 2 |
| Convert quotation to sale | Low | High | P1 | 2 |
| Product search on store | Low | High | P1 | 2 |
| Order status email notification | Low | High | P1 | 2 |
| Stock Transfer feature | Medium | High | P2 | 3 |
| Full Reports Module | High | Critical | P2 | 3 |
| REST API | High | High | P2 | 3 |
| Barcode scanner JS integration | Low | High | P2 | 3 |
| Service layer refactoring | High | Medium | P2 | 3 |
| Feature tests | Medium | Medium | P2 | 3 |
| Split payment in POS | Medium | Medium | P3 | 2 |
| Product image gallery | Medium | Medium | P3 | 3 |
| Payment gateway (JazzCash) | High | Critical | P3 | 4 |
| WhatsApp invoicing | Medium | High | P3 | 4 |
| Mobile POS app | High | High | P3 | 4 |
| AI demand forecasting | High | Medium | P4 | 4 |
| Multi-tenancy / SaaS | Very High | High | P4 | 4 |

---

## Summary — Top 10 Most Impactful Actions Right Now

1. **🔴 Implement RBAC** — Any authenticated user currently has full admin access
2. **🔴 Fix inactive user login** — Inactive users can still log in
3. **🔴 Change DELETE routes from GET to DELETE** — Security & REST compliance
4. **🟠 Enable customer self-registration + forgot password** — E-commerce can't grow without this
5. **🟠 Add customer order history page** — Basic e-commerce expectation
6. **🟠 Build Sales Reports page** — Managers are flying blind without reports
7. **🟠 PDF invoice generation** — Professional requirement for any business
8. **🟡 Low stock email alerts** — Prevent lost sales from stock-outs
9. **🟡 Convert Quotation to Sale** — Quotations are currently dead-end documents
10. **🟡 Cache `posSetting()` and discount rules** — Performance improvement, zero risk
