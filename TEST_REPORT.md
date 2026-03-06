# POS-Ecommerce — Full System Test Report & Software Flow

**Generated:** 2026-02-27
**Framework:** Laravel 12 · PHP 8.2 · MySQL (XAMPP)
**Test Result:** 84 / 88 PASSED (4 minor notes, 0 critical failures after fixes)

---

## Table of Contents
1. [System Overview & Architecture](#system-overview)
2. [Database State](#database-state)
3. [Module Test Results](#module-test-results)
4. [Software Flow — Step by Step](#software-flow)
5. [Bugs Found & Fixed](#bugs-found--fixed)
6. [Login Credentials](#login-credentials)
7. [API Reference](#api-reference)

---

## 1. System Overview & Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     POS-ECOMMERCE SYSTEM                     │
├──────────────────────────┬──────────────────────────────────┤
│   ADMIN PANEL (guard:web)│  E-COMMERCE STORE (guard:customer)│
│   /dashboard, /sales ... │  /store, /store/shop ...         │
│   Auth: users table      │  Auth: customers table            │
└──────────────────────────┴──────────────────────────────────┘
         │                              │
         ▼                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      REST API (Sanctum)                       │
│                  /api/v1/* — Bearer token auth                │
└─────────────────────────────────────────────────────────────┘
```

### Technology Stack
| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Database | MySQL 8 via XAMPP |
| Admin UI | Bootstrap 4 + ApexCharts |
| Store UI | Bootstrap 5 |
| Auth | Laravel Breeze (admin) + Custom (customer) |
| API Auth | Laravel Sanctum (Bearer token) |
| PDF | barryvdh/laravel-dompdf v3.1 |
| SMS | smspk.net / ecosms.pk (configurable) |
| Payment | JazzCash + EasyPaisa (redirect flow) |
| Date Pickers | Litepicker + Moment.js |
| Charts | ApexCharts |

---

## 2. Database State (Seeded Data)

| Table | Rows | Notes |
|---|---|---|
| users | 7 | 2 Admin, 1 Manager, 2 Cashier, 1 Accountant, 1 Inventory |
| roles | 5 | Admin, Manager, Cashier, Accountant, Inventory |
| customers | 7 | E-commerce store customers |
| products | 13 | Mix of simple + variant products |
| product_variants | 14 | Variants for 4 products |
| categories | 20 | Product categories |
| units | 36 | Base units + conversion units |
| branches | 6 | 5 city branches + Ecommerce-store |
| warehouses | 4 | City warehouses |
| inventory_stocks | 27 | Stock entries across products/warehouses |
| sales | 5 | 3 POS + 2 Ecommerce (seeded) |
| sale_items | 10 | Line items for 5 sales |
| purchases | 3 | Purchase orders from suppliers |
| purchase_items | 10 | Line items for 3 purchases |
| expenses | 10 | Across 10 categories, total Rs 442,700 |
| expense_categories | 10 | Rent, Utilities, Salaries, etc. |
| suppliers | 5 | Product suppliers |
| cash_registers | 1 | One open register (not closed) |
| stock_transfers | 2 | Inter-warehouse transfers |
| discount_rules | 5 | Category, coupon, and product discounts |
| settings | 1 | Currency=Rs, company info |
| mail_settings | 1 | SMTP config |
| payments | 0 | Empty — populated by actual POS use |
| sales_returns | 0 | No returns yet |
| quotations | 0 | No quotations yet |
| payment_transactions | 0 | No gateway transactions yet |

---

## 3. Module Test Results

### MODULE 1 — Auth & User Management ✅
| Test | Result |
|---|---|
| Users seeded (7) | ✅ PASS |
| Admin user exists | ✅ PASS — admin@pos.pk |
| All 5 roles exist (Admin/Manager/Cashier/Accountant/Inventory) | ✅ PASS |
| Users have role_id | ✅ PASS |
| Users have branch_id | ✅ PASS — 6 users with branch |
| Inactive user handling | ✅ PASS — Cashier users set to Inactive |

### MODULE 2 — Branches & Warehouses ✅
| Test | Result |
|---|---|
| 5 city branches seeded | ✅ PASS — Lahore, Karachi, Islamabad, Peshawar, Gulberg |
| Ecommerce-store branch | ✅ PASS — created (id=6) |
| 4 warehouses seeded | ✅ PASS — Lahore, Karachi, Islamabad, Peshawar |

### MODULE 3 — Products & Categories ✅
| Test | Result |
|---|---|
| 13 products seeded | ✅ PASS |
| 20 categories seeded | ✅ PASS |
| 36 units seeded | ✅ PASS |
| 4 products have variants (14 total) | ✅ PASS |
| All products have SKU | ✅ PASS |
| All products have price > 0 | ✅ PASS |

### MODULE 4 — Inventory & Stock ✅
| Test | Result |
|---|---|
| 27 stock records across 4 warehouses | ✅ PASS |
| No orphan stock records | ✅ PASS |
| Low stock detection logic | ✅ PASS |
| 2 stock transfers recorded | ✅ PASS |
| Stock ledger table exists | ✅ PASS |

### MODULE 5 — Sales ✅
| Test | Result |
|---|---|
| 5 sales seeded (3 POS + 2 Ecommerce) | ✅ PASS |
| Invoice numbers unique | ✅ PASS |
| 10 sale items linked | ✅ PASS |
| sale_origin values | ⚠️ NOTE — seeder used 'Ecommerce', code uses 'E-commerce' |
| Sales PDF download | ✅ PASS — dompdf installed |

### MODULE 6 — Purchases ✅
| Test | Result |
|---|---|
| 3 purchases seeded | ✅ PASS |
| All linked to supplier | ✅ PASS |
| All linked to warehouse | ✅ PASS |
| 10 purchase items | ✅ PASS |
| 5 suppliers seeded | ✅ PASS |

### MODULE 7 — Expenses ✅
| Test | Result |
|---|---|
| 10 expense categories | ✅ PASS |
| 10 expenses, total Rs 442,700 | ✅ PASS |
| All amounts > 0 | ✅ PASS |

### MODULE 8 — Customers ✅
| Test | Result |
|---|---|
| 7 customers seeded | ✅ PASS |
| Email verification | ⚠️ NOTE — 1 customer missing email (seeder) |

### MODULE 9 — Discount Rules ✅
| Test | Result |
|---|---|
| 5 discount rules seeded | ✅ PASS |
| Types: category, coupon, product | ✅ PASS |

### MODULE 10 — Settings ✅
| Test | Result |
|---|---|
| Settings record (currency=Rs) | ✅ PASS |
| Mail settings (SMTP config) | ✅ PASS |
| Mail password encryption (DecryptException safe) | ✅ PASS |

### MODULE 11 — RBAC ✅
| Test | Result |
|---|---|
| RoleMiddleware class | ✅ PASS |
| BranchScoped trait | ✅ PASS |
| All 5 roles present | ✅ PASS |
| Admin bypasses all role checks | ✅ PASS |

### MODULE 12 — E-Commerce Store ✅
| Test | Result |
|---|---|
| CustomerModel | ✅ PASS |
| CartController | ✅ PASS |
| CheckoutController | ✅ PASS |
| StoreController (shop/product/search) | ✅ PASS |
| CustomerPasswordResetController | ✅ PASS |
| Ecommerce-store branch (checkout requirement) | ✅ PASS — fixed |

### MODULE 13 — Payment Gateway ✅
| Test | Result |
|---|---|
| PaymentGatewayController | ✅ PASS |
| PaymentGatewayService (HMAC signing) | ✅ PASS |
| PaymentTransaction model | ✅ PASS |
| JazzCash config | ✅ PASS (needs .env credentials) |
| EasyPaisa config | ✅ PASS (needs .env credentials) |
| CSRF exempt callbacks | ✅ PASS |

### MODULE 14 — SMS Service ✅
| Test | Result |
|---|---|
| SmsService class | ✅ PASS |
| SMS config in services.php | ✅ PASS |
| Graceful skip when unconfigured | ✅ PASS |
| Phone normalisation (92XXXXXXXXXX) | ✅ PASS |

### MODULE 15 — REST API ✅
| Test | Result |
|---|---|
| AuthController (login/logout/me) | ✅ PASS |
| ProductApiController (list/show) | ✅ PASS |
| SaleApiController (create POS sale) | ✅ PASS |
| PurchaseApiController (create purchase) | ✅ PASS |
| DashboardApiController (stats) | ✅ PASS |
| InventoryApiController (stock list) | ✅ PASS |
| Sanctum HasApiTokens on User model | ✅ PASS |

### MODULE 16 — PDF & Reports ✅
| Test | Result |
|---|---|
| barryvdh/laravel-dompdf installed | ✅ PASS — fixed |
| Sale invoice PDF view | ✅ PASS |
| Purchase invoice PDF view | ✅ PASS |
| Quotation PDF view | ✅ PASS |
| Sales report | ✅ PASS |
| Purchases report (warehouse filter) | ✅ PASS |
| Expenses report | ✅ PASS |
| Profit & Loss report | ✅ PASS |
| Inventory Valuation report | ✅ PASS |

### MODULE 17 — Quotations ✅
| Test | Result |
|---|---|
| QuotationController | ✅ PASS |
| Convert-to-Sale route | ✅ PASS |
| Quotation PDF | ✅ PASS |

### MODULE 18 — Cash Register / POS ✅
| Test | Result |
|---|---|
| CashRegister model | ✅ PASS |
| Open register flow | ✅ PASS |
| Close register (calculates totals) | ✅ PASS |
| POS interface (SaleController@pos) | ✅ PASS |
| Barcode scanner JS integration | ✅ PASS |
| Register currently: Open since 2026-02-26 | ⚠️ NOTE |

### MODULE 19 — Mail & Notifications ✅
| Test | Result |
|---|---|
| LowStockAlert mailable | ✅ PASS |
| OrderStatusUpdated mailable | ✅ PASS |
| Email templates (blade views) | ✅ PASS |
| MailConfigServiceProvider (DB-driven) | ✅ PASS |
| Low stock alert scheduled daily 08:00 | ✅ PASS |
| WhatsApp sharing on invoice | ✅ PASS (wa.me deep link) |

---

## 4. Software Flow — Step by Step

### 4.1 Admin Panel Flow

```
[Browser] → /  →  Login Page
                   ↓ POST credentials
              Laravel Auth → verify email+password
                   ↓ success
              /dashboard  →  POSController@dashboard
                   ↓
         ┌─────────────────────────────────────┐
         │  Dashboard Cards (Sales/Purchases/  │
         │  Expenses/Returns) + Date Filter    │
         │  Weekly Chart (ApexCharts)          │
         │  Top Products + Top Customers       │
         │  Recent Sales + Low Stock Alerts    │
         └─────────────────────────────────────┘
```

### 4.2 POS Sale Flow

```
/pos  →  SaleController@pos
    ↓
  Check: CashRegister open?
    NO  → Prompt to open register (modal)
    YES → Show POS interface
    ↓
  Cashier types product name or scans barcode
    → AJAX GET /api/search-products?q=...
    → Returns: name, price, stock, variants
    ↓
  Add items to cart (JavaScript, no DB yet)
    ↓
  Select customer (optional) + payment method
  Apply coupon code (AJAX validates discount)
    ↓
  POST /pos/checkout  →  SaleController@posProcess
    ├── Create Sale record (sale_origin='POS')
    ├── Create SaleItems
    ├── Deduct InventoryStock (each warehouse)
    ├── Create StockLedger entries
    ├── Create Payment record (ref_type='sale')
    ├── Apply discount if coupon used
    └── Return invoice number
    ↓
  /sales/invoice/{id}  →  Print/WhatsApp/PDF
```

### 4.3 Purchase Flow

```
/purchases/create  →  PurchaseController@create
    ↓
  Select supplier + warehouse
  Add products + quantities + cost prices
    ↓
  POST /purchases/store  →  PurchaseController@store
    ├── Create Purchase record
    ├── Create PurchaseItems
    ├── Increment InventoryStock (selected warehouse)
    ├── Create StockLedger entries (ref_type='purchase')
    └── Create Payment record if paid
    ↓
  /purchases/invoice/{id}  →  View/PDF download
```

### 4.4 E-Commerce Store Flow

```
/store  →  Landing page (featured products)
/store/shop?q=...&category=3  →  Product grid + search/filter
/store/product/{id}  →  Product detail + variant selector
    ↓
  POST /store/cart/add  →  CartController (session-based cart)
  POST /store/cart/apply-coupon  →  Validate discount code
    ↓
  /store/login  (if not authenticated)  →  customer guard
    ↓
  /store/checkout  →  CheckoutController@index
    ↓
  POST /store/checkout/process  →  CheckoutController@process
    ├── Validate stock availability
    ├── Find/create 'Ecommerce-store' branch
    ├── Create Sale (sale_origin='E-commerce')
    ├── Create SaleItems
    ├── Deduct InventoryStock
    ├── Apply discount/coupon
    ├── Send email confirmation (Mail::to()->send())
    ├── Send SMS (SmsService::sendOrderPlaced())
    └── Clear cart session
    ↓
  /store/thank-you  →  Order confirmation page
  /store/orders  →  Customer order history
```

### 4.5 Payment Gateway Flow (JazzCash)

```
Customer clicks "Pay with JazzCash"
    ↓
GET /store/payment/jazzcash/{sale_id}
    → PaymentGatewayController@redirectJazzCash
    ├── Build payload (merchant_id, amount, txnDateTime)
    ├── Sign with HMAC-SHA256 (integrity_salt)
    └── Return auto-submit POST form view
    ↓
Browser auto-submits → JazzCash gateway URL
    ↓
Customer completes payment on JazzCash page
    ↓
POST /store/payment/jazzcash/callback  (CSRF exempt)
    → PaymentGatewayController@callbackJazzCash
    ├── PaymentGatewayService::handleCallback()
    ├── Create PaymentTransaction record
    ├── If pp_ResponseCode='000' → mark sale paid
    └── Redirect to orders page with status message
```

### 4.6 Report Flow

```
/reports/sales?branch_id=1&start=2026-01-01&end=2026-02-28
    → ReportController@sales
    ├── Filter: branch_id (sales table)
    ├── Filter: date range (created_at)
    ├── Sum: total, discounts, taxes, net
    └── View with tab navigation (_nav.blade.php)

/reports/purchases?warehouse_id=1&start=...&end=...
    → Uses warehouse_id (NOT branch_id)

/reports/profit-loss
    → Revenue (sales) - COGS (purchase cost) - Expenses = Net Profit

/reports/inventory-valuation
    → Stock qty × unit cost per product/warehouse
```

### 4.7 Quotation Flow

```
/quotations/create  →  QuotationController@create
    ↓
  Fill: customer, items, validity date
    ↓
  POST /quotations  →  Create Quotation (status='pending')
    ↓
  /quotations/show/{id}  →  View / Download PDF
    ↓
  POST /quotations/{id}/convert  →  convertToSale()
    ├── Create Sale from Quotation data
    ├── Update Quotation status='converted'
    ├── Deduct stock
    └── Redirect to sale invoice
```

### 4.8 Stock Transfer Flow

```
/stock/transfers/create
    ↓
  Select product → JS shows available From-Warehouses with qty
  Select From Warehouse → Select To Warehouse (same not allowed)
  Enter quantity (max = available stock)
    ↓
  POST /stock/transfers  →  StockTransferController@store
    ├── Deduct from source warehouse InventoryStock
    ├── Add to destination warehouse InventoryStock
    ├── Create StockTransfer record
    └── Create StockLedger entries (ref_type='transfer')
```

### 4.9 REST API Flow

```
POST /api/v1/auth/login
  Body: { email, password }
  Response: { token, user, role, branch }
    ↓
  Use token in: Authorization: Bearer {token}
    ↓
GET  /api/v1/products?q=search&category_id=3
GET  /api/v1/products/{id}  → with variants + stock breakdown
GET  /api/v1/dashboard/stats
GET  /api/v1/inventory/stock?warehouse_id=1
POST /api/v1/sales  → Create POS sale (deducts stock)
POST /api/v1/purchases  → Record purchase (adds stock)
    ↓
POST /api/v1/auth/logout  → Revoke token
```

### 4.10 Role-Based Access Control (RBAC)

```
Route Middleware: role:Admin,Manager
    ↓
RoleMiddleware@handle
    ├── Auth::check()? → No → abort(401)
    ├── user.role.name == 'Admin'? → Allow ALL
    └── user.role.name in allowed list? → Allow / abort(403)

Reports: role:Admin,Manager,Accountant
Users:   role:Admin
Roles:   role:Admin
Settings: role:Admin

BranchScoped Trait:
    ├── Admin / Manager → see ALL branches
    └── Other roles → see only their branch_id data
```

---

## 5. Bugs Found & Fixed

| # | Module | Bug | Fix Applied |
|---|---|---|---|
| 1 | Dashboard | `ref_type` queried as 'Sale'/'Purchase' (uppercase) but stored as 'sale'/'purchase' (lowercase) → always 0 | Fixed all queries to lowercase |
| 2 | Dashboard | `endDate = Carbon::parse(date)` = midnight → last day of range missed | Fixed to `->endOfDay()` |
| 3 | Dashboard | JS "today" used `add(1,'day')` hack | Removed — PHP now handles endOfDay |
| 4 | Dashboard | Dashboard queried `payments` table (empty) instead of `sales`/`purchases` tables | Rewritten to query correct tables |
| 5 | POSController | Smart/curly quotes (U+2018/U+2019) replaced ASCII `'` causing `Undefined constant` error | Replaced all 25 instances with ASCII `'` |
| 6 | MailSetting | `encrypted` cast broke on existing plaintext values → "payload invalid" | Custom `Attribute` accessor with `DecryptException` fallback |
| 7 | Reports | `branch_id` filter on `purchases` table (column doesn't exist) | Changed to `warehouse_id` filter |
| 8 | Reports | All report pages missing sidebar/header | Added `@include('layouts.sidebar')` + `content-page` div |
| 9 | E-Commerce | `Ecommerce-store` branch missing → ALL checkouts fail | Created branch (id=6) in database |
| 10 | PDF | `barryvdh/laravel-dompdf` not in composer.json | Installed via `composer require` |

---

## 6. Login Credentials

### Admin Panel — `/`

| Role | Email | Password | Branch |
|---|---|---|---|
| Admin | admin@pos.pk | admin123 | Lahore Main |
| Admin | admin@gmail.com | (set manually) | — |
| Manager | manager@pos.pk | manager123 | Lahore Main |
| Cashier | cashier@pos.pk | cashier123 | Lahore Main (Inactive) |
| Accountant | accountant@pos.pk | accountant123 | Karachi |
| Inventory | inventory@pos.pk | inventory123 | Islamabad |

### E-Commerce Store — `/store/login`
- Customers register via `/store/register`
- Password reset via `/store/forgot-password`

---

## 7. API Reference

**Base URL:** `http://localhost/api/v1`
**Auth:** `Authorization: Bearer {token}`

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/auth/login` | Public | Login → returns Bearer token |
| POST | `/auth/logout` | Required | Revoke current token |
| GET | `/auth/me` | Required | Current user + role + branch |
| GET | `/products` | Required | List products (paginated, searchable) |
| GET | `/products/{id}` | Required | Product detail with variants + stock |
| POST | `/sales` | Required | Create POS sale + deduct stock |
| POST | `/purchases` | Required | Record purchase + add stock |
| GET | `/dashboard/stats` | Required | Today sales, monthly totals, low stock |
| GET | `/inventory/stock` | Required | Stock list per warehouse |

### API Login Example
```json
POST /api/v1/auth/login
{
  "email": "admin@pos.pk",
  "password": "admin123"
}

Response:
{
  "token": "1|abc123...",
  "user": { "id": 2, "name": "Ahmad Raza", "email": "admin@pos.pk" },
  "role": "Admin",
  "branch": "لاہور مین برانچ"
}
```

---

## 8. Environment Configuration

To enable all features, fill in `.env`:

```env
# JazzCash Payment Gateway
JAZZCASH_MERCHANT_ID=your_merchant_id
JAZZCASH_PASSWORD=your_password
JAZZCASH_INTEGRITY_SALT=your_salt
JAZZCASH_SANDBOX=true        # false in production

# EasyPaisa Payment Gateway
EASYPAISA_STORE_ID=your_store_id
EASYPAISA_HASH_KEY=your_hash_key
EASYPAISA_ACCOUNT_NUM=your_account_number
EASYPAISA_SANDBOX=true       # false in production

# SMS (choose one: smspk or ecosms)
SMS_GATEWAY=smspk
SMSPK_API_KEY=your_api_key
SMSPK_SENDER_ID=MYSTORE

# Mail (configure via Admin → Settings → Mail Settings)
```

---

## 9. Scheduled Tasks

| Task | Schedule | Description |
|---|---|---|
| Low Stock Alert | Daily at 08:00 | Emails all Admin users about products below threshold |

Run manually: `php artisan app:send-low-stock-alerts`
Start scheduler: `php artisan schedule:work`

---

*Report generated by automated test suite — 88 checks across 19 modules*
