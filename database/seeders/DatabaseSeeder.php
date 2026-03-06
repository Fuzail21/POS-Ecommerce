<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Foundation — no foreign key dependencies
            RoleSeeder::class,
            UnitSeeder::class,
            WarehouseSeeder::class,
            SettingSeeder::class,
            MailSettingSeeder::class,

            // 2. Depends on warehouses
            BranchSeeder::class,

            // 3. Depends on roles + branches
            UserSeeder::class,

            // 4. Standalone masters
            CategorySeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            ExpenseCategorySeeder::class,

            // 5. Depends on categories + units
            ProductSeeder::class,
            ElectronicsProductSeeder::class,

            // 6. Depends on products + warehouses
            InventoryStockSeeder::class,

            // 7. Depends on branches + expense_categories + users
            ExpenseSeeder::class,

            // 8. Depends on categories + products
            DiscountRuleSeeder::class,

            // 9. Depends on suppliers + warehouses + users + products + units
            PurchaseSeeder::class,

            // 10. Depends on customers + branches + users + products + units
            SaleSeeder::class,

            // 11. Payments (sale receipts + purchase payments + manual)
            PaymentSeeder::class,

            // 12. Quotations
            QuotationSeeder::class,

            // 13. Stock transfers (WH → Branch)
            StockTransferSeeder::class,

            // 14. Stock ledger entries (purchases + transfers + sales)
            StockLedgerSeeder::class,
        ]);
    }
}
