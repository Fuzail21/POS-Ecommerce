@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            @php
                use App\Models\Setting;
                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
            @endphp
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">E-commerce Orders</h4>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Orders from E-commerce Store</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice No</th>
                                        <th>Customer</th>
                                        <th>Order Date</th>
                                        <th>Total Amount</th>
                                        <th>Discount</th>
                                        <th>Paid Amount</th>
                                        <th>Due Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $index => $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->invoice_number }}</td>
                                            <td>{{ $order->customer->name ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($order->sale_date)->format('d M Y') }}</td>
                                            <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($order->final_amount, 2) }}</td>
                                            <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($order->discount_amount, 2) }}</td>
                                            <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($order->paid_amount, 2) }}</td>
                                            <td class="{{ $order->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $setting->currency_symbol ?? '$' }} {{ number_format($order->due_amount, 2) }}
                                            </td>
                                            <td>{{ $order->payment_method }}</td>
                                            <td>
                                                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="d-inline status-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" class="form-control-sm order-status-select" data-current-status="{{ $order->status }}">
                                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="View Order" href="{{ route('orders.show', $order->id) }}">
                                                        <i class="ri-eye-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                    <a class="badge bg-info mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Print Invoice" href="{{ route('sales.invoice', $order->id) }}" target="_blank">
                                                        <i class="ri-printer-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">No E-commerce orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-3">
                            {{ $orders->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.order-status-select').forEach(selectElement => {
            selectElement.addEventListener('change', function () {
                const newStatus = this.value;
                const oldStatus = this.dataset.currentStatus; // Get the original status
                const form = this.closest('form');

                if (newStatus === 'cancelled') {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action will cancel the order. This cannot be undone and will revert stock and process refunds.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, cancel it!',
                        cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If confirmed, submit the form
                            form.submit();
                        } else {
                            // If cancelled, revert the select box to its old status
                            this.value = oldStatus;
                        }
                    });
                } else {
                    // For other status changes, just submit the form directly
                    form.submit();
                }
            });
        });
    });
</script>
@endsection