# PROJECT DOCUMENTATION
> **POS-Ecommerce** — Laravel 12 Point-of-Sale + E-Commerce Platform
> Last Updated: February 2026

---

## 1. Project Overview

### Project Name
**الفلاح ٹریڈرز POS** (Al-Falah Traders POS)

### Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend Framework | Laravel 12 (PHP 8.2+) |
| Authentication | Laravel Breeze (multi-guard: `web` for admin, `customer` for e-commerce) |
| Frontend Styling | Tailwind CSS + Alpine.js |
| Asset Bundler | Vite |
| Database | MySQL (via XAMPP) |
| Barcode Generation | `milon/barcode` v12 |
| Schema Operations | `doctrine/dbal` v4 |
| Testing Framework | PestPHP v3 |
| Code Style | Laravel Pint |
| Pagination | Bootstrap 5 (set in AppServiceProvider) |

### Architecture Pattern
**MVC (Model-View-Controller)** — standard Laravel structure with no dedicated Service or Repository layers. Business logic lives directly in Controllers.

### What the Project Does
A dual-purpose business management system:
1. **POS (Point-of-Sale)**: Staff-facing backend for managing inventory, sales, purchases, expenses, cash registers, quotations, and financial tracking.
2. **E-Commerce Store**: Customer-facing storefront with product browsing, shopping cart, coupon codes, checkout, and order history.

### Authentication
- **Admin/Staff** — Laravel `web` guard → `users` table → Role-based (Admin, Manager, Cashier, Accountant, Inventory)
- **Customer** — Laravel `customer` guard → `customers` table → Separate Authenticatable model

---

## 2. Folder Structure

```
POS-Ecommerce/
├── app/
│   ├── Exceptions/                  # Global exception handler
│   ├── Helpers/
│   │   ├── SettingHelper.php        # Global posSetting() helper function
│   │   └── UnitHelper.php           # Unit conversion utilities
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                # Laravel Breeze admin auth controllers
│   │   │   ├── Frontend/            # E-commerce facing controllers
│   │   │   │   ├── CustomerAuth/    # Customer login/register controllers
│   │   │   │   ├── CartController.php
│   │   │   │   ├── CheckoutController.php
│   │   │   │   ├── CustomerProfileController.php
│   │   │   │   └── StoreController.php
│   │   │   ├── BranchController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── DiscountRuleController.php
│   │   │   ├── ExpenseController.php
│   │   │   ├── FinanceController.php
│   │   │   ├── POSController.php
│   │   │   ├── ProductController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── PurchaseController.php
│   │   │   ├── QuotationController.php
│   │   │   ├── RoleController.php
│   │   │   ├── SaleController.php
│   │   │   ├── SalesDiscountTaxController.php
│   │   │   ├── SalesPaymentController.php
│   │   │   ├── SalesReturnController.php
│   │   │   ├── SettingController.php
│   │   │   ├── StockAdjustmentController.php
│   │   │   ├── SupplierController.php
│   │   │   ├── UnitController.php
│   │   │   ├── UserController.php
│   │   │   └── WarehouseController.php
│   │   ├── Middleware/
│   │   │   ├── CheckOpenRegister.php  # Enforces open cash register for POS
│   │   │   └── RedirectIfAuthenticated.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       │   ├── LoginRequest.php          # Admin login + rate limiting
│   │       │   └── CustomerLoginRequest.php  # Customer login validation
│   │       └── ProfileUpdateRequest.php
│   ├── Models/                         # 29 Eloquent models
│   └── Providers/
│       ├── AppServiceProvider.php      # Bootstrap 5 pagination + morph map
│       └── MailConfigServiceProvider.php  # Dynamic mail config from DB
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/                     # 53 migration files
│   └── seeders/                        # 17 seeder files
├── resources/
│   ├── css/                            # Tailwind CSS source
│   ├── js/                             # Alpine.js + Vite entry
│   └── views/                          # ~94 Blade templates
│       ├── auth/                       # Admin auth views
│       ├── emails/                     # Email templates
│       ├── layouts/
│       │   ├── app.blade.php           # Admin layout
│       │   ├── frontend/app.blade.php  # E-commerce layout
│       │   ├── guest.blade.php
│       │   ├── navigation.blade.php
│       │   └── sidebar.blade.php
│       └── store/                      # All e-commerce frontend views
├── routes/
│   ├── auth.php                        # Breeze auth routes
│   └── web.php                         # All application routes
├── .env                                # Environment config (not committed)
├── .env.example                        # Environment template
├── composer.json                       # PHP dependencies
├── package.json                        # JS dependencies
├── tailwind.config.js
└── vite.config.js
```

---

## 3. Module-by-Module Breakdown

---

### Module: Authentication (Admin)

**Files Involved:**
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Controllers/Auth/PasswordResetLinkController.php`
- `app/Http/Controllers/Auth/NewPasswordController.php`
- `app/Http/Controllers/Auth/ConfirmablePasswordController.php`
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php`
- `app/Http/Controllers/Auth/VerifyEmailController.php`
- `app/Http/Requests/Auth/LoginRequest.php`
- `resources/views/auth/` (login, register, forgot-password, reset-password, verify-email, confirm-password)
- `routes/auth.php`

**Purpose:**
Handles admin/staff login, registration, email verification, and password reset using Laravel Breeze scaffolding. The `web` guard authenticates against the `users` table.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/` | `login` | Show login form |
| POST | `/login` | — | Process login |
| POST | `/logout` | `logout` | Logout user |
| GET | `/register` | `register` | Show registration form |
| POST | `/register` | — | Create admin account |
| GET | `/forgot-password` | `password.request` | Show forgot password form |
| POST | `/forgot-password` | `password.email` | Send reset link |
| GET | `/reset-password/{token}` | `password.reset` | Show reset form |
| POST | `/reset-password` | `password.store` | Process password reset |
| GET | `/verify-email` | `verification.notice` | Email verification prompt |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` | Verify email |
| POST | `/email/verification-notification` | `verification.send` | Resend verification |
| GET | `/confirm-password` | `password.confirm` | Confirm password |
| POST | `/confirm-password` | — | Process confirmation |

**Business Logic Flow:**
```
User → POST /login → LoginRequest (validates email/password, rate limits 5/min)
  → Auth::attempt() → Session regenerate → redirect('/dashboard')
```

**Validation Rules (LoginRequest):**
- `email`: required, string, email
- `password`: required, string
- Rate limited: 5 attempts per minute per IP

**Middleware Applied:** `guest` on all auth routes; `auth`, `verified` on protected routes

---

### Module: Authentication (Customer / E-Commerce)

**Files Involved:**
- `app/Http/Controllers/Frontend/CustomerAuth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Frontend/CustomerAuth/RegisteredUserController.php`
- `app/Http/Requests/Auth/CustomerLoginRequest.php`
- `app/Models/Customer.php`
- `resources/views/store/auth/login.blade.php`
- `config/auth.php` (customer guard)

**Purpose:**
Provides separate authentication for e-commerce customers using the `customer` guard. Customers authenticate against the `customers` table. Registration route is currently commented out.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/store/login` | `customer.login` | Customer login form |
| POST | `/store/login` | — | Process customer login |
| POST | `/store/logout` | `customer.logout` | Customer logout |

> ⚠️ **Security Note:** Customer self-registration route (`/store/register`) is commented out — customers can only be created by admin via `CustomerController`.

**Guard Configuration:** `auth:customer` middleware applied to `/store/checkout`, `/store/profile/*`

---

### Module: Dashboard & Analytics (POS)

**Files Involved:**
- `app/Http/Controllers/POSController.php`
- `app/Models/Sale.php`, `Purchase.php`, `Expense.php`, `Product.php`, `Customer.php`, `Payment.php`, `CashRegister.php`
- `resources/views/dashboard.blade.php`

**Purpose:**
Main admin dashboard showing real-time business KPIs including weekly sales/purchases charts, top products, top customers, low stock alerts, and payment method breakdowns.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/dashboard` | `dashboard` | Main dashboard with analytics |
| POST | `/pos/open-register` | `pos.openRegister` | Open cash register |
| POST | `/pos/close-register` | `pos.closeRegister` | Close cash register |
| GET | `/pos/check-register` | `pos.checkRegister` | Check if register is open |
| GET | `/pos/register-details` | `pos.getRegisterDetails` | Get register summary |

**Dashboard Data Includes:**
- Today's total sales, purchases, expenses
- Weekly revenue chart (last 7 days)
- Top 5 selling products with revenue
- Top 5 customers by purchase amount
- Low stock product alerts
- Payment method breakdown (cash/card/online/mixed)
- Active cash register summary

**Business Logic — Cash Register:**
```
Staff clicks "Open Register" → POST /pos/open-register
  → Validates opening_cash (numeric, min:0)
  → Checks no existing open register for user
  → Creates CashRegister record (user_id, opening_cash, opened_at)
  → Returns JSON redirect to /pos

Staff clicks "Close Register" → POST /pos/close-register
  → Finds open register for auth user
  → Calculates total_sales, total_expense during session
  → Computes cash_difference (closing_cash - expected_cash)
  → Updates CashRegister (closed_at, closing_cash, totals)
```

**Middleware:** `auth`, `verified`

---

### Module: POS (Point of Sale)

**Files Involved:**
- `app/Http/Controllers/SaleController.php` (pos() and posProcess() methods)
- `app/Http/Controllers/BarcodeScannerController.php`
- `app/Http/Middleware/CheckOpenRegister.php`
- `app/Models/Sale.php`, `SaleItem.php`, `Product.php`, `ProductVariant.php`, `InventoryStock.php`, `StockLedger.php`, `Payment.php`
- `resources/views/pos/` (POS terminal view)

**Purpose:**
Interactive POS terminal for cashiers to process in-store sales. Supports barcode scanning, multiple items, discounts, tax, and multiple payment methods. Requires an open cash register.

**Routes:**

| Method | URI | Name | Middleware | Description |
|--------|-----|------|-----------|-------------|
| GET | `/pos` | `pos.index` | `auth`, `CheckOpenRegister` | POS terminal UI |
| POST | `/pos/checkout` | `checkout.pos` | `auth`, `CheckOpenRegister` | Process POS sale |

**Business Logic Flow — POS Sale:**
```
Cashier scans barcode / searches product
  → GET /api/search-products?q={term} → returns product+variant data

Cashier adds items, sets quantities, discount, payment method
  → POST /pos/checkout {items[], discount, payment_method, customer_id?}
    → Generate invoice_number (INV-YYYY-XXXX)
    → Create Sale record (sale_origin='POS', status='completed')
    → For each item:
        → Create SaleItem
        → Deduct InventoryStock (quantity_in_base_unit)
        → Create StockLedger entry (direction='out', ref_type='sale')
    → Create Payment record
    → Update Customer balance if credit sale
    → Return success + invoice
```

**Middleware:** `CheckOpenRegister` → Redirects to dashboard if no open register found for current user.

---

### Module: E-Commerce Store

**Files Involved:**
- `app/Http/Controllers/Frontend/StoreController.php`
- `app/Http/Controllers/Frontend/CartController.php`
- `app/Http/Controllers/Frontend/CheckoutController.php`
- `app/Http/Controllers/Frontend/CustomerProfileController.php`
- `app/Models/Product.php`, `ProductVariant.php`, `DiscountRule.php`, `Sale.php`, `SaleItem.php`, `Customer.php`
- `resources/views/store/` (landing, shop, product, cart, checkout, thankyou, profile, auth/login)

**Purpose:**
Customer-facing online store with product catalog, variant selection, session-based shopping cart, coupon codes, and checkout flow that creates a Sale record with `sale_origin='Ecommerce'`.

**Routes:**

| Method | URI | Name | Middleware | Description |
|--------|-----|------|-----------|-------------|
| GET | `/store` | `store.landing` | — | Store homepage |
| GET | `/store/shop` | `store.shop` | — | Product catalog |
| GET | `/store/product/{id}` | `store.product` | — | Product detail + variants |
| POST | `/store/cart/add` | `cart.add` | — | Add item to cart |
| GET | `/store/cart` | `cart.view` | — | View cart |
| POST | `/store/cart/update` | `cart.update` | — | Update quantities |
| DELETE | `/store/cart/remove` | `cart.remove` | — | Remove item |
| POST | `/store/cart/apply-coupon` | `cart.apply_coupon` | — | Apply coupon code |
| POST | `/store/cart/remove-coupon` | `cart.remove_coupon` | — | Remove coupon |
| POST | `/store/product-variant` | `product.getVariant` | — | Fetch variant data via AJAX |
| GET | `/store/checkout` | `store.checkout` | `auth:customer` | Checkout form |
| POST | `/store/checkout/process` | `store.checkout.process` | `auth:customer` | Place order |
| GET | `/store/thank-you` | `store.thankyou` | `auth:customer` | Order confirmation |
| GET | `/store/profile` | `customer.profile.edit` | `auth:customer` | Customer profile |
| PUT | `/store/profile` | `customer.profile.update` | `auth:customer` | Update profile |

**Business Logic Flow — Checkout:**
```
Customer browses /store/shop
  → Filters by category, search query
  → Products shown with getDiscountedPriceAttribute() (auto-applies active discount rules)

Customer views /store/product/{id}
  → Variants shown with color/size
  → AJAX POST /store/product-variant → returns variant price, stock

Customer adds to cart (session-based)
  → Cart stored in session: ['items' => [{product_id, variant_id, quantity, price}]]
  → Coupon applied: validates against discount_rules (type='coupon', active dates)
  → Coupon discount stored in session

Customer proceeds to /store/checkout (auth:customer required)
  → POST /store/checkout/process
    → Generate invoice_number
    → Create Sale (sale_origin='Ecommerce', payment_method='online')
    → Create SaleItems
    → Deduct InventoryStock
    → Create StockLedger entries
    → Send order confirmation email to customer
    → Clear cart session
    → Redirect to /store/thank-you
```

**Third-Party Dependencies:** Laravel Mail (for order confirmation email)

---

### Module: Product Management

**Files Involved:**
- `app/Http/Controllers/ProductController.php`
- `app/Models/Product.php`, `ProductVariant.php`, `Category.php`, `Unit.php`, `InventoryStock.php`
- `resources/views/products/` (index, create, edit, variants)
- Routes in `web.php` under `/products` prefix

**Purpose:**
Full CRUD for products with optional variant support (color + size). Products auto-generate SKU and barcode using `milon/barcode`. Supports product images, tax rates, low stock thresholds, and expiry tracking.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/products` | `products.list` | Product list |
| GET | `/products/create` | `products.create` | Create form |
| POST | `/products/store` | `products.store` | Store product |
| GET | `/products/{id}/edit` | `products.edit` | Edit form |
| POST | `/products/{id}` | `products.update` | Update product |
| GET | `/products/{id}` | `products.destroy` | Soft delete product |
| GET | `/products/{id}/variants` | `products.variants` | View/manage variants |
| GET | `/api/search-products` | — | AJAX product search for POS |

**Key Product Model Attributes:**
- `getDiscountedPriceAttribute()` — Auto-applies active discount rules (category or product-level)
- `getTotalStockAttribute()` — Sums `quantity_in_base_unit` across all inventory stocks
- `getIsLowStockAttribute()` — Returns true if total stock ≤ 5

**Barcode Generation:** `milon/barcode` package generates EAN-13 or Code128 barcodes for products and variants.

**Database Tables:** `products`, `product_variants`, `inventory_stocks`, `categories`, `units`

---

### Module: Inventory Management

**Files Involved:**
- `app/Http/Controllers/StockAdjustmentController.php`
- `app/Models/InventoryStock.php`, `StockLedger.php`, `Product.php`, `ProductVariant.php`, `Warehouse.php`
- `resources/views/stock/` (list, ledger)

**Purpose:**
Tracks stock levels per product/variant/warehouse. Maintains a full audit trail of all stock movements in `stock_ledgers`. Supports stock adjustments and supplier-product reports.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/stock/list` | `stock.list` | Current stock levels |
| GET | `/stock-ledger` | `stock.ledger` | Full stock movement history |
| GET | `/reports/supplier-products` | `reports.supplier_products` | Products per supplier |

**Stock Ledger ref_type Values:**
`purchase` | `sale` | `return` | `adjustment` | `transfer` | `cancelled_order_return`

**Direction Values:** `in` (stock added) | `out` (stock removed)

**Stock Update Flow:**
```
Purchase → PurchaseController::store()
  → InventoryStock::updateOrCreate({product_id, variant_id, warehouse_id})
  → increment quantity_in_base_unit
  → StockLedger::create(direction='in', ref_type='purchase')

Sale → SaleController::process()
  → InventoryStock: decrement quantity_in_base_unit
  → StockLedger::create(direction='out', ref_type='sale')

Sales Return → SalesReturnController::store()
  → InventoryStock: increment quantity_in_base_unit
  → StockLedger::create(direction='in', ref_type='return')
```

---

### Module: Purchase Management

**Files Involved:**
- `app/Http/Controllers/PurchaseController.php`
- `app/Models/Purchase.php`, `PurchaseItem.php`, `Supplier.php`, `InventoryStock.php`, `StockLedger.php`, `Payment.php`
- `resources/views/purchases/` (index, create, edit, invoice)

**Purpose:**
Records purchase orders from suppliers. Each purchase auto-updates inventory stock and the stock ledger. Tracks payment against supplier balance.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/purchases` | `purchases.list` | Purchase list |
| GET | `/purchases/create` | `purchases.create` | Create purchase |
| POST | `/purchases/store` | `purchases.store` | Store purchase |
| GET | `/purchases/{id}/edit` | `purchases.edit` | Edit purchase |
| POST | `/purchases/{id}` | `purchases.update` | Update purchase |
| DELETE | `/purchases/{id}` | `purchases.destroy` | Delete purchase |
| GET | `/purchases/invoice/{id}` | `purchases.invoice` | Print invoice |

**Business Logic:**
- `invoice_number` auto-generated (format: `PUR-YYYY-XXXX`)
- On save: `due_amount = total_amount - paid_amount`
- Each `PurchaseItem` increases `InventoryStock.quantity_in_base_unit`
- `Supplier.balance` updated (incremented on purchase, decremented on payment)

---

### Module: Sales Management

**Files Involved:**
- `app/Http/Controllers/SaleController.php`
- `app/Models/Sale.php`, `SaleItem.php`, `Customer.php`, `Payment.php`, `StockLedger.php`
- `resources/views/sales/` (index, create, invoice)

**Purpose:**
Manages both POS and E-commerce sales in one unified `sales` table. `sale_origin` column distinguishes POS vs Ecommerce. Supports invoice printing, sale deletion (soft), and e-commerce order status management.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/sales` | `sales.list` | All sales list |
| GET | `/sales/create` | `sales.create` | Manual sale form |
| POST | `/sales/checkout` | `sales.checkout.process` | Process manual sale |
| DELETE | `/sales/{id}` | `sales.destroy` | Delete sale |
| GET | `/sales/invoice/{id}` | `sales.invoice` | Print invoice |
| GET | `/e-commerce/orders` | `orders.index` | E-commerce orders list |
| GET | `/orders/{order}` | `orders.show` | Order detail |
| PUT | `/orders/{order}/status` | `orders.updateStatus` | Update order status |

**Sale Status Values:** `pending`, `processing`, `shipped`, `completed`, `cancelled`

**Sale Origin Values:** `POS`, `Ecommerce`

---

### Module: Sales Returns

**Files Involved:**
- `app/Http/Controllers/SalesReturnController.php`
- `app/Models/SalesReturn.php`, `SalesReturnItem.php`, `Sale.php`, `InventoryStock.php`, `StockLedger.php`, `Payment.php`
- `resources/views/sale_return/`

**Purpose:**
Handles product returns from customers. Restores inventory stock and creates a refund payment record. Full stock ledger entry created with `ref_type='return'`.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/sale_return` | `sale_return.list` | Returns list |
| GET | `/sale_return/create/{sale}` | `sale_return.create` | Create return for sale |
| POST | `/sale_return/{sale}/store` | `sale_return.store` | Process return |
| DELETE | `/sale_return/{id}` | `sale_return.destroy` | Delete return |
| GET | `/sale_return/details/{id}` | `sale_return.details` | Return details |
| GET | `/sale_return/{sales_return}` | `sale_return.show` | Return view |

---

### Module: Customer Management (Admin)

**Files Involved:**
- `app/Http/Controllers/CustomerController.php`
- `app/Models/Customer.php`
- `resources/views/customers/`

**Purpose:**
Admin-side CRUD for customers. Customers can have card IDs for loyalty systems, passwords for e-commerce login, and address fields. Balance tracking for credit customers.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/customers` | `customers.list` | Customer list |
| GET | `/customers/create` | `customers.create` | Create customer |
| POST | `/customers/store` | `customers.store` | Store customer |
| GET | `/customers/{id}/edit` | `customers.edit` | Edit customer |
| POST | `/customers/{id}` | `customers.update` | Update customer |
| GET | `/customers/{id}` | `customers.destroy` | Delete customer |
| GET | `/customers/card/{id}` | `customers.card` | Print customer card |

**Customer Model — Notable Fields:**
- `password` — Hashed, for e-commerce login
- `card_id` — Unique loyalty card identifier
- `balance` — Outstanding credit/debit balance
- `last_name`, `country`, `city`, `postcode` — E-commerce address fields

---

### Module: Supplier Management

**Files Involved:**
- `app/Http/Controllers/SupplierController.php`
- `app/Models/Supplier.php`
- `resources/views/suppliers/`

**Purpose:**
Manages supplier records with contact details and balance tracking. Balance increases with each purchase and decreases with payments.

**Routes:** Standard CRUD under `/suppliers` prefix.

**Database Tables:** `suppliers`, `product_supplier` (pivot), `purchases`

---

### Module: Finance / Payments

**Files Involved:**
- `app/Http/Controllers/FinanceController.php`
- `app/Models/Payment.php`
- `resources/views/payments/`

**Purpose:**
Records payments against sales (customer payments) and purchases (supplier payments). Uses a polymorphic `payments` table that references both customers/suppliers and sales/purchases.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/payments` | `payments.list` | All payments |
| GET | `/payments/create` | `payments.create` | Create payment |
| POST | `/payments/store` | `payments.store` | Record payment |
| GET | `/payments/{id}` | `payments.destroy` | Delete payment |

**Polymorphic Map (AppServiceProvider):**
```php
Relation::enforceMorphMap([
    'customer'     => Customer::class,
    'supplier'     => Supplier::class,
    'sale'         => Sale::class,
    'purchase'     => Purchase::class,
    'sales_return' => SalesReturn::class,
]);
```

**Payment Methods:** `cash`, `card`, `bank`
**Transaction Types:** `in` (money received), `out` (money paid)
**ref_type Values:** `sale`, `purchase`, `manual`, `sales_return`

---

### Module: Expense Management

**Files Involved:**
- `app/Http/Controllers/ExpenseController.php`
- `app/Models/Expense.php`, `ExpenseCategory.php`
- `resources/views/expense/`

**Purpose:**
Tracks business expenses per branch with categorization. Categories are managed inline (no separate page). Expenses affect the cash register closing balance calculation.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/expense_categories/list` | `expense_categories.list` | Inline categories view |
| POST | `/expense_categories/store` | `expense_categories.store` | Create category |
| PUT | `/expense_categories/{id}` | `expense_categories.update` | Update category |
| GET | `/expense_categories/{id}` | `expense_categories.destroy` | Delete category |
| GET | `/expense/list` | `expense.list` | Expense list |
| GET | `/expense/create` | `expense.create` | Create expense form |
| POST | `/expense/store` | `expense.store` | Store expense |
| GET | `/expenses/edit/{id}` | `expense.edit` | Edit expense |
| PUT | `/expenses/update/{id}` | `expense.update` | Update expense |
| GET | `/expenses/delete/{id}` | `expense.destroy` | Delete expense |

---

### Module: Quotations

**Files Involved:**
- `app/Http/Controllers/QuotationController.php`
- `app/Models/Quotation.php`, `QuotationItem.php`
- `resources/views/quotations/`
- `resources/views/emails/quotation-sent.blade.php`

**Purpose:**
Creates sales quotations for customers with line items, tax, discount, and shipping. Quotations can be emailed to customers and converted to orders.

**Routes:** Standard CRUD under `/quotations` prefix with email sending.

**Quotation Status Values:** `Pending`, `Accepted`, `Rejected`, `Expired`

---

### Module: Discount Rules

**Files Involved:**
- `app/Http/Controllers/DiscountRuleController.php`
- `app/Models/DiscountRule.php`
- `resources/views/discount_rules/`

**Purpose:**
Configures flexible discount rules that can target specific products, product categories, or operate as coupon codes. Rules have date ranges and percentage discounts.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/discount-rules` | `discount_rules.index` | List discount rules |
| GET | `/discount-rules/create` | `discount_rules.create` | Create form |
| POST | `/discount-rules` | `discount_rules.store` | Store rule |
| GET | `/discount-rules/{id}/edit` | `discount_rules.edit` | Edit rule |
| PUT | `/discount-rules/{id}` | `discount_rules.update` | Update rule |
| DELETE | `/discount-rules/{id}` | `discount_rules.destroy` | Delete rule |

**Discount Types:** `category`, `product`, `coupon`
**Application:** Rules auto-apply in `Product::getDiscountedPriceAttribute()` and cart coupon validation.

---

### Module: User & Role Management

**Files Involved:**
- `app/Http/Controllers/UserController.php`, `RoleController.php`
- `app/Models/User.php`, `Role.php`
- `resources/views/user/`, `resources/views/role/`

**Purpose:**
Admin management of staff accounts and role definitions. Users are assigned a role and a branch. Roles exist in DB but no gate/policy enforcement is implemented.

> ⚠️ **Security Issue:** `role_id` is stored in the `users` table, but no middleware, gates, or policies enforce role-based access control. Any authenticated user can access any admin route.

---

### Module: Warehouse & Branch Management

**Files Involved:**
- `app/Http/Controllers/WarehouseController.php`, `BranchController.php`
- `app/Models/Warehouse.php`, `Branch.php`
- `resources/views/warehouses/`, `resources/views/branches/`

**Purpose:**
Manages physical storage warehouses (with capacity tracking) and retail/online branches. Branches are linked to warehouses. Users are assigned to branches.

---

### Module: Settings

**Files Involved:**
- `app/Http/Controllers/SettingController.php`
- `app/Models/Setting.php`, `MailSetting.php`
- `app/Providers/MailConfigServiceProvider.php`
- `app/Helpers/SettingHelper.php`
- `resources/views/settings/`

**Purpose:**
Configures business identity (name, logo, currency, colors, address) and SMTP mail settings. Mail settings are loaded from DB at boot time via `MailConfigServiceProvider`. The `posSetting()` helper provides global access to settings.

**Routes:**

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/settings` | `settings.index` | Settings page |
| POST | `/settings/save` | `settings.save` | Save business settings |
| POST | `/mail-settings/save` | `mail-settings.save` | Save mail SMTP settings |

---

### Module: Units

**Files Involved:**
- `app/Http/Controllers/UnitController.php`
- `app/Models/Unit.php`
- `app/Helpers/UnitHelper.php`
- `resources/views/units/`

**Purpose:**
Manages measurement units with conversion factors. Products have a `base_unit` and `default_display_unit`. Inventory is always stored in base units; display conversion is applied via `UnitHelper`.

---

## 4. Database Schema Overview

### Tables Summary (53 migrations → ~30 application tables)

#### User & Auth Tables
| Table | Key Columns | Relationships |
|-------|-------------|---------------|
| `users` | id, name, email, password, role_id, branch_id, status | belongs to role, branch |
| `roles` | id, name, description | has many users |
| `password_reset_tokens` | email, token, created_at | — |
| `sessions` | id, user_id, ip_address, payload | belongs to user |

#### Business Configuration Tables
| Table | Key Columns | Notes |
|-------|-------------|-------|
| `settings` | business_name, logo_path, currency_symbol, currency_code, primary_color, address | Single row |
| `mail_settings` | mail_mailer, mail_host, mail_port, mail_username, mail_password, mail_encryption | Single row |
| `branches` | id, name, location, contact, warehouse_id | belongs to warehouse |
| `warehouses` | id, name, location, capacity, used_capacity | has many branches, stocks |

#### Product & Category Tables
| Table | Key Columns | Relationships |
|-------|-------------|---------------|
| `categories` | id, name, parent_id (self-ref) | parent/children (hierarchical) |
| `units` | id, name, base_unit, conversion_factor | used by products |
| `products` | id, name, sku, barcode, category_id, base_unit_id, default_display_unit_id, has_variants, tax_rate, actual_price, low_stock, track_expiry | belongs to category, 2 units |
| `product_variants` | id, product_id, variant_name, sku, barcode, actual_price, color, size, low_stock | belongs to product |
| `product_supplier` | product_id, supplier_id | pivot table |

#### Inventory Tables
| Table | Key Columns | Relationships |
|-------|-------------|---------------|
| `inventory_stocks` | product_id, variant_id (nullable), warehouse_id, quantity_in_base_unit | unique(product, variant, warehouse) |
| `stock_ledgers` | product_id, variant_id, warehouse_id, ref_type, ref_id, quantity_change_in_base_unit, direction, unit_cost, created_by | audit trail |

#### Transaction Tables
| Table | Key Columns | Relationships |
|-------|-------------|---------------|
| `sales` | id, customer_id, branch_id, invoice_number, sale_date, total_amount, discount_amount, tax_amount, final_amount, paid_amount, due_amount, payment_method, shipping, sale_origin, status, created_by | belongs to customer, branch, user |
| `sale_items` | sale_id, product_id, variant_id, quantity, unit_id, quantity_in_base_unit, unit_price, discount, tax, total_price | belongs to sale, product |
| `purchases` | id, supplier_id, warehouse_id, invoice_number, purchase_date, total_amount, paid_amount, due_amount, notes, created_by | belongs to supplier, warehouse |
| `purchase_items` | purchase_id, product_id, variant_id, quantity, unit_id, quantity_in_base_unit, unit_cost, total_cost, batch_no, expiry_date | belongs to purchase |
| `customers` | id, name, last_name, phone, email, address, balance, card_id, password, country, city, postcode | Authenticatable model |
| `suppliers` | id, name, contact_person, phone, email, address, balance | has many purchases |
| `payments` | entity_type, entity_id, transaction_type, amount, payment_method, ref_type, ref_id, note, created_by | polymorphic |

#### Other Tables
| Table | Key Columns | Notes |
|-------|-------------|-------|
| `sales_returns` | sale_id, customer_id, return_date, total_return_amount, refund_amount, payment_method | |
| `sales_return_items` | sales_return_id, sale_id, product_id, variant_id, quantity, unit_price | |
| `quotations` | customer_id, branch_id, quotation_number, grand_total, status, order_tax_amount | |
| `quotation_items` | quotation_id, product_id, product_variant_id, unit_price, quantity, subtotal | |
| `expenses` | branch_id, category_id, amount, description, expense_date, created_by | |
| `expense_categories` | id, name | |
| `cash_registers` | user_id, opened_at, closed_at, opening_cash, closing_cash, total_sales, total_expense, cash_difference | |
| `discount_rules` | name, type (category/product/coupon), target_ids (JSON), discount, coupon_code, start_date, end_date | |

### ER Diagram (Textual)
```
users ──────────────────────── roles
  │
  └── branch_id ──────────────── branches ── warehouse_id ── warehouses
                                                                  │
products ── category_id ────── categories                         │
  │ └── base_unit_id ─────────── units                            │
  │                                                               │
  ├── product_variants                                            │
  │                                                               │
  └── inventory_stocks ─────────────────── (warehouse_id) ───────┘
        │
        └── stock_ledgers (audit trail)

sales ── customer_id ─────────── customers
  │ └── branch_id
  └── sale_items ── product_id ── products
                 └── variant_id ── product_variants

purchases ── supplier_id ─────── suppliers
  └── purchase_items ── product_id

payments (polymorphic)
  ├── entity: customer | supplier
  └── ref: sale | purchase | sales_return | manual
```

---

## 5. Middleware & Global Logic

### Middleware

| Middleware | File | Applied To | Purpose |
|-----------|------|-----------|---------|
| `auth` | Laravel built-in | All admin routes | Ensure user is logged in |
| `auth:customer` | Laravel built-in | `/store/checkout/*`, `/store/profile` | Ensure customer is logged in |
| `verified` | Laravel built-in | All admin routes | Ensure email is verified |
| `guest` | Laravel built-in | Auth routes | Redirect if already logged in |
| `CheckOpenRegister` | `app/Http/Middleware/CheckOpenRegister.php` | `/pos`, `/pos/checkout` | Require open cash register |
| `RedirectIfAuthenticated` | `app/Http/Middleware/RedirectIfAuthenticated.php` | Guest routes | Redirect authenticated users |

### Service Providers

| Provider | Purpose |
|---------|---------|
| `AppServiceProvider` | Sets Bootstrap 5 pagination, registers polymorphic morph map |
| `MailConfigServiceProvider` | Loads SMTP settings from `mail_settings` DB table at boot time; wrapped in try-catch for safety |

### Global Helpers (auto-loaded via composer.json)

| Helper | Function | Purpose |
|--------|---------|---------|
| `SettingHelper.php` | `posSetting($key, $default)` | Retrieve business settings from DB |
| `UnitHelper.php` | Unit conversion functions | Convert between units using conversion_factor |

---

## 6. Configuration & Environment

### Critical Environment Variables

```ini
APP_NAME=POS-Ecommerce
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos
DB_USERNAME=root
DB_PASSWORD=

# Mail (also overridden by mail_settings table)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### Key Config Files

| File | Purpose |
|------|---------|
| `config/app.php` | App name, timezone, locale, providers |
| `config/auth.php` | Guards (`web` for users, `customer` for customers), providers |
| `config/mail.php` | Mail driver defaults (overridden by DB settings at runtime) |

### auth.php Guards
```php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'customer' => ['driver' => 'session', 'provider' => 'customers'],
],
'providers' => [
    'users' => ['driver' => 'eloquent', 'model' => App\Models\User::class],
    'customers' => ['driver' => 'eloquent', 'model' => App\Models\Customer::class],
],
```

---

## 7. Inter-Module Dependencies

```
Settings ◄──────────── All Modules (via posSetting() helper)
    │
    ▼
MailConfig ◄─────────── Quotations, Checkout (email sending)

Warehouses ◄──────────── Branches ◄──────── Users
                              │
                        Inventory Stocks ◄── Products ◄─── Categories
                              │                    │              │
                        StockLedger           Variants      Discount Rules
                              │
              ┌───────────────┼─────────────────┐
              ▼               ▼                 ▼
          Purchases         Sales           SalesReturns
              │               │
          Suppliers       Customers ◄──── E-Commerce Cart
                              │               │
                           Payments       Checkout → Sale
                           Finance

Units ◄────────────────── PurchaseItems, SaleItems (unit conversions)
Roles ◄────────────────── Users (currently cosmetic, not enforced)
ExpenseCategories ◄────── Expenses ◄── CashRegister (affects closing balance)
```

### Shared Services / Helpers Used Across Modules:
- `posSetting()` — Used in views, controllers, and email templates
- `DiscountRule` model's date-scoped query — Used in `Product::getDiscountedPriceAttribute()` and `CartController::applyCoupon()`
- `InventoryStock` + `StockLedger` — Updated by Purchases, Sales, SalesReturns, StockAdjustments
- Polymorphic `Payment` — Shared by FinanceController, SaleController, PurchaseController, SalesReturnController
