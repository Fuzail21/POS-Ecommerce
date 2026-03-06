{{-- Report Navigation Tabs --}}
<div class="mb-4">
    <ul class="nav nav-tabs flex-wrap">
        <li class="nav-item">
            <a class="nav-link {{ Route::is('reports.sales') ? 'active' : '' }}"
               href="{{ route('reports.sales') }}">
                <i class="las la-chart-bar mr-1"></i> Sales
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('reports.purchases') ? 'active' : '' }}"
               href="{{ route('reports.purchases') }}">
                <i class="las la-shopping-cart mr-1"></i> Purchases
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('reports.expenses') ? 'active' : '' }}"
               href="{{ route('reports.expenses') }}">
                <i class="las la-receipt mr-1"></i> Expenses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('reports.profit-loss') ? 'active' : '' }}"
               href="{{ route('reports.profit-loss') }}">
                <i class="las la-balance-scale mr-1"></i> Profit &amp; Loss
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('reports.inventory-valuation') ? 'active' : '' }}"
               href="{{ route('reports.inventory-valuation') }}">
                <i class="las la-boxes mr-1"></i> Inventory Valuation
            </a>
        </li>
    </ul>
</div>
