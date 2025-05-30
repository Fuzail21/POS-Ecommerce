@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Purchases List</h4>
                    </div>
                    <a href="{{ route('purchases.create') }}" class="btn btn-primary add-list">
                        <i class="las la-plus mr-3"></i>Add Purchase
                    </a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">

                    <!-- Flash Messages -->
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
                            <h4 class="card-title">Purchases</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice No</th>
                                        <th>Supplier</th>
                                        <th>Branch</th>
                                        <th>Purchase Date</th>
                                        <th>Grand Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchases as $index => $purchase)
                                        <tr>
                                            <td>{{ $purchase->id }}</td>
                                            <td>{{ $purchase->invoice_number }}</td>
                                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                            <td>{{ $purchase->branch->name ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                                            <td>{{ number_format($purchase->total_amount, 2) }}</td>
                                            <td>{{ number_format($purchase->paid_amount, 2) }}</td>
                                            <td class="{{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($purchase->due_amount, 2) }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    {{-- <a class="badge bg-success mr-2 p-1" href="{{ route('purchases.edit', $purchase->id) }}" data-toggle="tooltip" title="Edit">
                                                        <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                    </a> --}}
                                                    
                                                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="badge bg-danger mr-2 p-1 border-0" data-toggle="tooltip" title="Delete">
                                                            <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                        </button>
                                                    </form>



                                                    <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Invoice" href="{{ route('purchases.invoice', $purchase->id) }}">
                                                        <i class="ri-file-list-3-line" style="font-size: 1.1rem;"></i>
                                                    </a>


                                                    {{-- <a class="badge bg-info p-1" href="" data-toggle="tooltip" title="View">
                                                        <i class="ri-eye-line" style="font-size: 1.1rem;"></i>
                                                    </a> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No purchases found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-3">
                            {{ $purchases->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
