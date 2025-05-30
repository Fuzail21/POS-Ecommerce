@extends('layouts.app')

@section('css')
<style>
    #datatable_info,
    #datatable_paginate,
    #datatable_length {
        display: none !important;
    }
</style>
@endsection

@section('content')

@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

             <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Payemnts List</h4>
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

                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="paymentTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="all-tab" data-toggle="tab" href="#allPayments" role="tab">All Payments</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link" id="pending-tab" data-toggle="tab" href="#pendingPayments" role="tab">Pending Payments</a>
                            </li> --}}
                        </ul>
                    </div>

                    <div class="card-body tab-content">

                        <!-- All Payments Tab -->
                        <div class="tab-pane fade show active" id="allPayments" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="ligth">
                                            <th>#</th>
                                            <th>Entity</th>
                                            <th>Entity Type</th>
                                            <th>Reference</th>
                                            <th>Paid Amount</th>
                                            {{-- <th>Status</th> --}}
                                            <th>Method</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                        @php
                                            $dueAmount = method_exists($payment->reference, 'total') 
                                                ? $payment->reference->total - $payment->amount 
                                                : null;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $payment->entity->name ?? '-' }}</td>
                                            <td>{{ ucfirst($payment->entity_type) ?? '-' }}</td>
                                            <td>{{ class_basename($payment->ref_type) }} #{{ $payment->reference->id ?? '-' }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            {{-- <td>
                                                @if($dueAmount === null)
                                                    <span class="badge bg-secondary">N/A</span>
                                                @elseif($dueAmount > 0)
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-success">Paid</span>
                                                @endif
                                            </td> --}}
                                            <td>{{ ucfirst($payment->payment_method) }}</td>
                                            <td>{{ $payment->creator->name ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    {{-- <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" title="Edit" href="{{ route('payments.edit', $payment->id) }}">
                                                        <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                    </a> --}}
                                                    <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" title="Delete" href="{{ route('payments.destroy', $payment->id) }}">
                                                        <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pending Payments Tab -->
                        <div class="tab-pane fade" id="pendingPayments" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="ligth">
                                            <th>#</th>
                                            <th>Entity</th>
                                            <th>Reference</th>
                                            <th>Paid Amount</th>
                                            <th>Due Amount</th>
                                            <th>Status</th>
                                            <th>Method</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                            @php
                                                $dueAmount = method_exists($payment->reference, 'total') 
                                                    ? $payment->reference->total - $payment->amount 
                                                    : null;
                                            @endphp

                                            @if($dueAmount !== null && $dueAmount > 0)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $payment->entity->name ?? '-' }}</td>
                                                <td>{{ class_basename($payment->reference_type) }} #{{ $payment->reference->id ?? '-' }}</td>
                                                <td>{{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ number_format($dueAmount, 2) }}</td>
                                                <td><span class="badge bg-warning">Pending</span></td>
                                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                                <td>{{ $payment->creator->name ?? '-' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center list-action">
                                                        <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" title="Edit" href="{{ route('payments.edit', $payment->id) }}">
                                                            <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                        </a>
                                                        <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" title="Delete" href="{{ route('payments.destroy', $payment->id) }}">
                                                            <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $payments->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
