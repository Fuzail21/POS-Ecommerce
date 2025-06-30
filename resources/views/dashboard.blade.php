@extends('layouts.app')

<style>
    /* Custom styles for professional card look */
    .dashboard-card {
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        min-height: 120px; /* Increased minimum height for the card */
        /* d-flex align-items-center justify-content-between already applied in HTML */
    }

    .dashboard-card-icon-container {
        width: 65px; /* Slightly increased size for the icon background */
        height: 65px; /* Slightly increased size for the icon background */
        flex-shrink: 0;
        margin-right: 1.25rem; /* Increased space between icon and text */
    }

    .dashboard-card .fa-2x {
        font-size: 2em; /* Ensure the icon is the standard 2x size */
    }

    .dashboard-card .card-value {
        font-size: 1.55rem; /* Slightly larger font size for the value */
        font-weight: 600;
        line-height: 1.2; /* Adjusted line-height for value */
    }

    .dashboard-card .card-title {
        font-size: 0.90rem; /* Slightly adjusted font size for the title */
        opacity: 0.9;
        color: white;
        display: block;
        line-height: 1.2; /* Adjusted line-height for title */
        margin-top: 0.1rem; /* Reduced space between value and title for tighter look */
    }

    /* Hover effects for the whole card */
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
        text-decoration: none;
    }

    .dashboard-card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    @media (max-width: 767.98px) {
        .dashboard-card {
            padding: 1rem;
            min-height: 100px;
        }
        .dashboard-card-icon-container {
            width: 50px;
            height: 50px;
            margin-right: 1rem;
        }
        .dashboard-card .fa-2x {
            font-size: 1.8em;
        }
        .dashboard-card .card-value {
            font-size: 1.5rem;
        }
        .dashboard-card .card-title {
            font-size: 0.85rem;
        }
    }
</style>

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="row">
                @php
                    use App\Models\Setting;
                    $settings = Setting::first();
                    $primaryColor = $settings->primary_color ?? '#0d6efd'; // default blue
                    $secondaryColor = $settings->secondary_color ?? '#6c757d'; // default gray
                @endphp
                <div class="col-lg-9">
                    <div class="card card-transparent card-block card-stretch card-height border-none">
                        <div class="card-body p-0 mt-lg-2 mt-0">
                            <h3 class="mb-3">Hi {{ Auth::user()->name }},</h3>
                            <p class="mb-0 mr-4">POS system offers real-time insights into sales, inventory management, and
                                business operations.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 d-flex justify-content-end align-items-center">
                    <div class="input-group date-range-picker">
                        <input type="text" id="litepicker" class="form-control" placeholder="Select Date Range"
                            style="margin-right: 5px;">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-calendar-alt"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-range="today">Today</a></li>
                            <li><a class="dropdown-item" href="#" data-range="this_week">This Week</a></li>
                            <li><a class="dropdown-item" href="#" data-range="last_week">Last Week</a></li>
                            <li><a class="dropdown-item" href="#" data-range="this_month">This Month</a></li>
                            <li><a class="dropdown-item" href="#" data-range="last_month">Last Month</a></li>
                            <li><a class="dropdown-item" href="#" data-range="custom">Custom Range</a></li>
                            {{-- Removed Apply and Reset buttons as per discussion --}}
                        </ul>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="row">
                        @php
                            $cards = [
                                [
                                    'title' => 'Sales', // Changed to 'Sales' to match image
                                    'icon' => 'fas fa-shopping-cart',
                                    'value' => $sales,
                                    'color' => '#6C63FF', // Original purple
                                    'route' => route('sales.list'),
                                ],
                                [
                                    'title' => 'Purchases', // Changed to 'Purchases' to match image
                                    'icon' => 'fas fa-shopping-basket',
                                    'value' => $purchases,
                                    'color' => '#28a745', // Green
                                    'route' => route('purchases.list'),
                                ],
                                [
                                    'title' => 'Sales Returns',
                                    'icon' => 'fas fa-arrow-right', // Changed icon to match image
                                    'value' => $salesReturns,
                                    'color' => '#007bff', // Blue
                                    'route' => route('sale_return.list'),
                                ],
                                [
                                    'title' => 'Today Total Sales', // Changed to match image
                                    'icon' => 'fas fa-dollar-sign',
                                    'value' => $todaySales,
                                    'color' => '#6f42c1', // Darker purple (from image, previously was sales returns)
                                    'route' => route('sales.list'),
                                ],
                                [
                                    'title' => 'Today Total Received(Sales)', // Changed to match image
                                    'icon' => 'fas fa-money-bill-wave',
                                    'value' => $todayReceived,
                                    'color' => '#e83e8c', // Pink
                                    'route' => route('sales.list'),
                                ],
                                [
                                    'title' => 'Today Total Purchases', // Changed to match image
                                    'icon' => 'fas fa-shopping-cart', // Changed icon to match image
                                    'value' => $todayPurchases,
                                    'color' => '#17a2b8', // Teal
                                    'route' => route('purchases.list'),
                                ],
                                [
                                    'title' => 'Today Total Expense', // Changed to match image
                                    'icon' => 'fas fa-minus-circle',
                                    'value' => $todayExpense,
                                    'color' => '#dc3545', // Red
                                    'route' => route('expense.list'),
                                ],
                            ];
                        @endphp

                        @foreach ($cards as $card)
                            <div class="col-md-3 mb-4">
                                <a href="{{ $card['route'] }}"
                                   class="dashboard-card shadow-sm rounded text-white p-4 d-flex align-items-center justify-content-between text-decoration-none"
                                   style="background-color: {{ $card['color'] }};">
                                    {{-- Icon section --}}
                                    <div class="dashboard-card-icon-container d-flex align-items-center justify-content-center rounded-circle"
                                         style="background-color: rgba(255, 255, 255, 0.3);">
                                        <i class="{{ $card['icon'] }} text-white" style="font-size: 1.7em !important;"></i>
                                    </div>
                                    {{-- Text section (value and title) --}}
                                    <div class="text-right d-flex flex-column justify-content-center"> {{-- Added flex-column and justify-content-center here --}}
                                        <h4 class="mb-0 card-value">{{ $settings->currency_symbol }}
                                            {{ number_format($card['value'], 2) }}</h4>
                                        <small class="card-title">{{ $card['title'] }}</small>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">This Week Sales & Purchases</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="sales-purchases-chart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Top Selling Products (Current Month)</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>PRODUCT</th>
                                            <th>GRAND TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topProductTableData as $product)
                                            <tr>
                                                {{-- Displays product name and variant name --}}
                                                <td>{{ $product['product_name'] }}
                                                    ({{ number_format($product['quantity'], 0) }})</td>
                                                <td>{{ $product['grand_total'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2">No top selling products for this month yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div id="top-selling-products-chart" style="min-height: 250px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card card-block">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Top Customers (Current Month)</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="top-customers-chart" style="min-height: 250px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Recent Sales</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Sale ID</th>
                                            <th>Customer</th>
                                            <th>Products</th>
                                            <th>Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSales as $sale)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d') }}</td>
                                                <td>{{ $sale->id }}</td>
                                                <td>{{ $sale->customer->name ?? 'walk-in-customer' }}</td>
                                                <td>
                                                    @foreach ($sale->items as $item)
                                                        {{-- Display product name and variant name if available --}}
                                                        {{ $item->product->name ?? 'N/A' }}
                                                        @if ($item->variant)
                                                            ({{ $item->variant->variant_name }})
                                                        @endif
                                                        ({{ number_format($item->quantity, 0) }})@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>{{ $settings->currency_symbol }}
                                                    {{ number_format($sale->total_amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">No recent sales found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($stockAlertProducts->isNotEmpty())
                    <div class="col-lg-12 mb-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title text-warning" style="display: flex; align-items: center;">
                                        {{-- <i class="fas fa-exclamation-triangle me-2"></i> --}}
                                        Low Stock Alerts!
                                    </h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Current Stock</th>
                                                <th>Category</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stockAlertProducts as $product)
                                                @php
                                                    $conversion = $product->baseUnit->conversion_factor ?? 1;
                                                    $productLowStockThreshold = $product->low_stock ?? 0;
                                                @endphp
                
                                                {{-- Display base product stock if applicable and it's low --}}
                                                @if ($product->inventoryStock && ($product->inventoryStock->quantity_in_base_unit / $conversion) <= $productLowStockThreshold)
                                                    <tr>
                                                        <td>{{ $product->name ?? 'N/A' }} (Base Product)</td>
                                                        <td>
                                                            {{ number_format($product->inventoryStock->quantity_in_base_unit / $conversion, 0) }}
                                                            {{ $product->baseUnit->name ?? '' }}
                                                        </td>
                                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                    </tr>
                                                @elseif (!$product->inventoryStock && !$product->has_variants)
                                                    {{-- If no base inventory stock and no variants, treat as 0 and low --}}
                                                    <tr>
                                                        <td>{{ $product->name ?? 'N/A' }}</td>
                                                        <td>0 {{ $product->baseUnit->name ?? '' }} (No Stock Record)</td>
                                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                    </tr>
                                                @endif
                
                                                {{-- Display variant stock alerts --}}
                                                @foreach ($product->variants as $variant)
                                                    @php
                                                        $variantConversion = $product->baseUnit->conversion_factor ?? 1; // Assuming variant stock is in base unit
                                                        $variantLowStockThreshold = $variant->low_stock ?? $productLowStockThreshold; // Use variant's threshold, fallback to product's
                                                    @endphp
                                                    @if ($variant->inventoryStock && ($variant->inventoryStock->quantity_in_base_unit / $variantConversion) <= $variantLowStockThreshold)
                                                        <tr>
                                                            <td>
                                                                {{ $product->name ?? 'N/A' }} ({{ $variant->variant_name ?? 'N/A' }})
                                                            </td>
                                                            <td>
                                                                {{ number_format($variant->inventoryStock->quantity_in_base_unit / $variantConversion, 0) }}
                                                                {{ $product->baseUnit->name ?? '' }}
                                                            </td>
                                                            {{-- Assuming variants share baseUnit with parent product --}}
                                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                        </tr>
                                                    @elseif(!$variant->inventoryStock)
                                                        {{-- If variant exists but has no stock record --}}
                                                        <tr>
                                                            <td>
                                                                {{ $product->name ?? 'N/A' }} ({{ $variant->variant_name ?? 'N/A' }})
                                                            </td>
                                                            <td>0 {{ $product->baseUnit->name ?? '' }} (No Stock Record)</td>
                                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            @if ($stockAlertProducts->isEmpty())
                                                <tr>
                                                    <td colspan="3">No products or variants are currently low in stock.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                
                                {{-- Pagination Links --}}
                                <div class="d-flex justify-content-end mt-4">
                                    {{ $stockAlertProducts->links() }}
                                </div>
                
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- REQUIRED BOOTSTRAP JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    {{-- Include Litepicker CSS and JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Litepicker
            const picker = new Litepicker({
                element: document.getElementById('litepicker'),
                singleMode: false,
                allowRepick: true,
                format: 'YYYY-MM-DD',
                tooltipText: {
                    one: 'day',
                    other: 'days'
                },
                setup: (picker) => {
                    picker.on('selected', (date1, date2) => {
                        const startDate = date1.format('YYYY-MM-DD');
                        const endDate = date2.format('YYYY-MM-DD');
                        console.log('Selected date range:', startDate, 'to', endDate);
                        applyDateRange(startDate, endDate);
                    });
                }
            });

            // Handle dropdown menu clicks for predefined ranges
            document.querySelectorAll('.dropdown-item[data-range]').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const range = this.dataset.range;
                    let startDate = null;
                    let endDate = null;

                    const today = moment();

                    switch (range) {
                        case 'today':
                            startDate = today.clone().startOf('day');
                           // endDate = today.clone().endOf('day');
                            endDate = today.clone().add(1, 'day').startOf('day');
                            break;
                        case 'this_week':
                            startDate = today.clone().startOf('week');
                            endDate = today.clone().endOf('week');
                            break;
                        case 'last_week':
                            startDate = today.clone().subtract(1, 'week').startOf('week');
                            endDate = today.clone().subtract(1, 'week').endOf('week');
                            break;
                        case 'this_month':
                            startDate = today.clone().startOf('month');
                            endDate = today.clone().endOf('month');
                            break;
                        case 'last_month':
                            startDate = today.clone().subtract(1, 'month').startOf('month');
                            endDate = today.clone().subtract(1, 'month').endOf('month');
                            break;
                        case 'custom':
                            picker.show();
                            return;
                    }

                    if (startDate && endDate) {
                        picker.setStartDate(startDate.toDate());
                        picker.setEndDate(endDate.toDate());
                        picker.hide();
                        applyDateRange(
                            startDate.format('YYYY-MM-DD'),
                            endDate.format('YYYY-MM-DD')
                        );
                    }
                });
            });


            function applyDateRange(startDate, endDate) {
                const url = new URL(window.location.href);
                url.searchParams.set('start_date', startDate);
                url.searchParams.set('end_date', endDate);
                window.location.href = url.toString();
            }


            // This Week Sales & Purchases Chart:
            var salesPurchasesData = {
                series: [{
                    name: 'Sales',
                    data: @json($weekSalesData)
                }, {
                    name: 'Purchases',
                    data: @json($weekPurchasesData)
                }],
                chart: {
                    type: 'bar',
                    height: 300
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        endingShape: 'flat'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: @json($weekDates),
                },
                yaxis: {
                    title: {
                        text: 'Amount'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "{{ $settings->currency_symbol }}" + " " + val.toFixed(2)
                        }
                    }
                },
                colors: ['#476CD7', '#5BC65B']
            };

            if (document.querySelector("#sales-purchases-chart")) {
                var salesPurchasesChart = new ApexCharts(document.querySelector("#sales-purchases-chart"),
                    salesPurchasesData);
                salesPurchasesChart.render();
            }

            // Top Selling Products (Current Month) - Table and Pie Chart:
            var topSellingProductsData = {
                series: @json($topProductQuantities),
                labels: @json($topProductNames),
                colors: ['#476CD7', '#97D293', '#F2C643', '#EF6F6F', '#8E44AD'],
                chart: {
                    type: 'donut',
                    height: 250
                },
                legend: {
                    position: 'right'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            if (document.querySelector("#top-selling-products-chart")) {
                var topSellingProductsChart = new ApexCharts(document.querySelector("#top-selling-products-chart"),
                    topSellingProductsData);
                topSellingProductsChart.render();
            }

            // Top Customers (Current Month) - Pie Chart:
            var topCustomersData = {
                series: @json($topCustomerAmounts),
                labels: @json($topCustomerNames),
                colors: ['#476CD7', '#97D293', '#F2C643', '#EF6F6F', '#8E44AD'],
                chart: {
                    type: 'donut',
                    height: 250
                },
                legend: {
                    position: 'right'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            if (document.querySelector("#top-customers-chart")) {
                var topCustomersChart = new ApexCharts(document.querySelector("#top-customers-chart"),
                    topCustomersData);
                topCustomersChart.render();
            }
        });
    </script>
@endsection
