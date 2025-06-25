<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SalesReturn;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
         Relation::morphMap([
            'customer' => Customer::class,
            'supplier' => Supplier::class,
            'purchase' => Purchase::class,
            'sale' => Sale::class,
            'sales_return' => SalesReturn::class,

        ]);
    }
}
