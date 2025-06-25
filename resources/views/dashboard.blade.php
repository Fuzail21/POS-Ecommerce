@extends('layouts.app')

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
                                    'title' => 'Total Sales',
                                    'icon' => 'fas fa-shopping-cart',
                                    'value' => $sales,
                                    'color' => '#6C63FF',
                                    'route' => route('sales.list'), // Placeholder route
                                ],
                                [
                                    'title' => 'Total Purchases',
                                    'icon' => 'fas fa-shopping-basket',
                                    'value' => $purchases,
                                    'color' => '#28a745',
                                    'route' => route('purchases.list'), // Placeholder route
                                ],
                                [
                                    'title' => 'Sales Returns',
                                    'icon' => 'fas fa-undo-alt',
                                    'value' => $salesReturns,
                                    'color' => '#007bff',
                                    'route' => route('sale_return.list'), // Placeholder route
                                ],
                                [
                                    'title' => "Today's Sales",
                                    'icon' => 'fas fa-dollar-sign',
                                    'value' => $todaySales,
                                    'color' => '#6f42c1',
                                    'route' => route('sales.list'), // Placeholder route
                                ],
                                [
                                    'title' => 'Today Received (Sales)',
                                    'icon' => 'fas fa-money-bill-wave',
                                    'value' => $todayReceived,
                                    'color' => '#e83e8c',
                                    'route' => route('sales.list'), // Placeholder route, might be same as today's sales
                                ],
                                [
                                    'title' => "Today's Purchases",
                                    'icon' => 'fas fa-truck-loading',
                                    'value' => $todayPurchases,
                                    'color' => '#17a2b8',
                                    'route' => route('purchases.list'), // Placeholder route
                                ],
                                [
                                    'title' => "Today's Expense",
                                    'icon' => 'fas fa-minus-circle',
                                    'value' => $todayExpense,
                                    'color' => '#dc3545',
                                    'route' => route('expense.list'), // Placeholder route
                                ],
                            ];
                        @endphp

                        @foreach ($cards as $card)
                            <div class="col-md-3 mb-4">
                                {{-- Wrapped the card content in an <a> tag --}}
                                <a href="{{ $card['route'] }}"
                                   class="shadow-sm rounded text-white p-4 h-100 d-flex align-items-center text-decoration-none"
                                   style="background-color: {{ $card['color'] }}; display: block;">
                                    <div class="me-3" style="margin-right: 20px;">
                                        <i class="{{ $card['icon'] }} fa-2x"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">{{ $settings->currency_symbol }}
                                            {{ number_format($card['value'], 2) }}</h4>
                                        <small class="text-white-50">{{ $card['title'] }}</small>
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
                                                {{-- Display base product stock if applicable --}}
                                                @if ($product->inventoryStock && $product->inventoryStock->quantity_in_base_unit <= 5)
                                                    <tr>
                                                        <td>{{ $product->name ?? 'N/A' }} (Base Product)</td>
                                                        <td>{{ number_format($product->inventoryStock->quantity_in_base_unit, 0) }}
                                                            {{ $product->baseUnit->name ?? '' }}</td>
                                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                    </tr>
                                                @endif

                                                {{-- Display variant stock alerts --}}
                                                @foreach ($product->variants as $variant)
                                                    @if ($variant->inventoryStock && $variant->inventoryStock->quantity_in_base_unit <= 5)
                                                        <tr>
                                                            <td>{{ $product->name ?? 'N/A' }}
                                                                ({{ $variant->variant_name ?? 'N/A' }})</td>
                                                            <td>{{ number_format($variant->inventoryStock->quantity_in_base_unit, 0) }}
                                                                {{ $product->baseUnit->name ?? '' }}</td>
                                                            {{-- Assuming variants share baseUnit with parent product --}}
                                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                        </tr>
                                                    @elseif(!$variant->inventoryStock && $product->variants->isNotEmpty())
                                                        {{-- If variant exists but has no stock record --}}
                                                        <tr>
                                                            <td>{{ $product->name ?? 'N/A' }}
                                                                ({{ $variant->variant_name ?? 'N/A' }})</td>
                                                            <td>0 {{ $product->baseUnit->name ?? '' }} (No Stock Record)
                                                            </td>
                                                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                {{-- Handle products with no base inventory and no variants having inventory --}}
                                                @if (!$product->inventoryStock && $product->variants->isEmpty())
                                                    <tr>
                                                        <td>{{ $product->name ?? 'N/A' }}</td>
                                                        <td>0 {{ $product->baseUnit->name ?? '' }} (No Stock Record)</td>
                                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @if ($stockAlertProducts->isEmpty())
                                                <tr>
                                                    <td colspan="3">No products or variants are currently low in stock.
                                                    </td>
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
                        columnWidth: '55%',
                        endingShape: 'rounded'
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
