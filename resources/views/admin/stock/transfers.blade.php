@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between align-items-center">
                    <div class="iq-header-title">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                    <a href="{{ route('stock.transfers.create') }}" class="btn btn-primary">
                        <i class="fas fa-exchange-alt mr-1"></i> New Transfer
                    </a>
                </div>

                <div class="iq-card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Reference</th>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Variant</th>
                                    <th>From Warehouse</th>
                                    <th>To Warehouse</th>
                                    <th>Quantity</th>
                                    <th>Created By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfers as $i => $transfer)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><code>{{ $transfer->transfer_reference }}</code></td>
                                    <td>{{ $transfer->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transfer->product->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->variant->variant_name ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-warning">{{ $transfer->fromWarehouse->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $toBranch = $branchByWarehouseId[$transfer->to_warehouse_id] ?? null;
                                        @endphp
                                        <span class="badge badge-success">{{ $transfer->toWarehouse->name ?? '-' }}</span>
                                        @if($toBranch)
                                            <br><small class="text-muted"><i class="fas fa-store mr-1"></i>{{ $toBranch->name }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($transfer->quantity, 2) }}</td>
                                    <td>{{ $transfer->creator->name ?? '-' }}</td>
                                    <td>{{ $transfer->notes ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">No stock transfers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $transfers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
