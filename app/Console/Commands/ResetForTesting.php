<?php

namespace App\Console\Commands;

use Database\Seeders\BranchSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\DiscountRuleSeeder;
use Database\Seeders\ElectronicsProductSeeder;
use Database\Seeders\ExpenseCategorySeeder;
use Database\Seeders\ExpenseSeeder;
use Database\Seeders\InventoryStockSeeder;
use Database\Seeders\MailSettingSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\PaymentSeeder;
use Database\Seeders\PurchaseSeeder;
use Database\Seeders\QuotationSeeder;
use Database\Seeders\SaleSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\StockLedgerSeeder;
use Database\Seeders\StockTransferSeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\UnitSeeder;
use Database\Seeders\WarehouseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetForTesting extends Command
{
    protected $signature   = 'db:reset-test {--force : Skip confirmation prompt}';
    protected $description = 'Truncate all tables (except users & roles) and re-run seeders with English Pakistani data';

    /** Tables that must never be truncated */
    protected array $protected = ['users', 'roles', 'migrations'];

    /**
     * Seeders to run in dependency order.
     * UserSeeder and RoleSeeder are excluded — their tables are preserved.
     */
    protected array $seeders = [
        // 1. Foundation
        UnitSeeder::class,
        WarehouseSeeder::class,
        SettingSeeder::class,
        MailSettingSeeder::class,
        // 2. Depends on warehouses
        BranchSeeder::class,
        // 3. Masters
        CategorySeeder::class,
        SupplierSeeder::class,
        CustomerSeeder::class,
        ExpenseCategorySeeder::class,
        // 4. Products
        ProductSeeder::class,
        ElectronicsProductSeeder::class,
        // 5. Stock (central + branch stores)
        InventoryStockSeeder::class,
        // 6. Transactions
        ExpenseSeeder::class,
        DiscountRuleSeeder::class,
        PurchaseSeeder::class,
        SaleSeeder::class,
        PaymentSeeder::class,
        // 7. Quotations
        QuotationSeeder::class,
        // 8. Stock transfers (WH → Branch)
        StockTransferSeeder::class,
        // 9. Ledger (must be last)
        StockLedgerSeeder::class,
    ];

    public function handle(): int
    {
        if (! $this->option('force') &&
            ! $this->confirm('This will DELETE all data except users & roles then re-seed. Continue?', false)) {
            $this->info('Aborted.');
            return self::SUCCESS;
        }

        $database = config('database.connections.' . config('database.default') . '.database');

        $tables = DB::select(
            'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?',
            [$database]
        );

        $toTruncate = array_filter(
            array_map(fn ($t) => $t->TABLE_NAME, $tables),
            fn ($name) => ! in_array($name, $this->protected)
        );

        $this->info('Disabling foreign key checks...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($toTruncate as $table) {
            DB::table($table)->truncate();
            $this->line("  Truncated: <comment>{$table}</comment>");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Foreign key checks re-enabled.');
        $this->newLine();

        $this->info('Running seeders (skipping UserSeeder & RoleSeeder — tables are preserved)...');
        foreach ($this->seeders as $seeder) {
            $this->call('db:seed', ['--class' => $seeder, '--force' => true]);
        }

        $this->newLine();
        $this->info('Database reset complete. System is test-ready.');
        $this->table(
            ['Module', 'Count'],
            [
                ['Warehouses',        DB::table('warehouses')->count()],
                ['Branches',          DB::table('branches')->count()],
                ['Products',          DB::table('products')->count()],
                ['Inventory records', DB::table('inventory_stocks')->count()],
                ['Purchases',         DB::table('purchases')->count()],
                ['Sales',             DB::table('sales')->count()],
                ['Payments',          DB::table('payments')->count()],
                ['Quotations',        DB::table('quotations')->count()],
                ['Stock Transfers',   DB::table('stock_transfers')->count()],
                ['Ledger entries',    DB::table('stock_ledgers')->count()],
                ['Customers',         DB::table('customers')->count()],
                ['Suppliers',         DB::table('suppliers')->count()],
                ['Expenses',          DB::table('expenses')->count()],
            ]
        );
        $this->newLine();
        $this->line('  Admin    : <info>admin@pos.pk</info> / <info>admin123</info>');
        $this->line('  Manager  : <info>manager@pos.pk</info> / <info>manager123</info>');
        $this->line('  Cashier  : <info>cashier@pos.pk</info> / <info>cashier123</info>');
        $this->line('  Customer : <info>ali.ahmad@gmail.com</info> / <info>customer123</info>');

        return self::SUCCESS;
    }
}
