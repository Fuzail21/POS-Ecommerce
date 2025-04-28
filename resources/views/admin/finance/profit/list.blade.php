@extends('layouts.app')

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Profit List</h4>
                    </div>
                    <a href="{{ route('profit.create') }}" class="btn btn-primary add-list">
                        <i class="las la-plus mr-2"></i>Add Profit
                    </a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Profits</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table data-tables table-striped">
                                <thead>
                                    <tr class="ligth">
                                        <th>ID</th>
                                        <th>Store</th>
                                        <th>Total Income</th>
                                        <th>Total Expense</th>
                                        <th>Net Profit</th>
                                        <th>Created At</th>
                                        <th colspan="2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($profits as $profit)
                                        <tr>
                                            <td>{{ $profit->id }}</td>
                                            <td>{{ $profit->store->name ?? 'N/A' }}</td>
                                            <td>${{ number_format($profit->total_income, 2) }}</td>
                                            <td>${{ number_format($profit->total_expense, 2) }}</td>
                                            <td>${{ number_format($profit->net_profit, 2) }}</td>
                                            <td>{{ $profit->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('profit.edit', $profit->id) }}">
                                                        <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                    <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" href="{{ route('profit.delete', $profit->id) }}">
                                                        <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No records found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $profits->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
