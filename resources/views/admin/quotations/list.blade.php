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
            @php
                use App\Models\Setting;
                $setting = Setting::first();
                $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                $secondaryColor = $setting->secondary_color ?? '#6c757d'; // default gray
            @endphp

            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Quotations List</h4> {{-- Changed title from Roles List --}}
                    </div>
                    <a href="{{ route('quotations.create') }}" class="btn text-white add-list" style="background-color: {{ $primaryColor }};">
                      <i class="las la-plus mr-3"></i>Add Quotation
                    </a>
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
                        <h4 class="card-title">Quotations</h4> {{-- Changed title from Roles --}}
                     </div>
                  </div>

                  <div class="card-body">
                    <div class="table-responsive">
                       <table id="datatable" class="table data-tables table-striped">
                          <thead>
                             <tr class="ligth">
                                <th>ID</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Branch</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Action</th>
                             </tr>
                          </thead>
                          <tbody>
                            @forelse($quotations as $quotation) {{-- Iterating over $quotations --}}
                                <tr>
                                    <td>{{ $quotation->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($quotation->quotation_date)->format('Y-m-d') }}</td> {{-- Format date --}}
                                    <td>{{ $quotation->customer->name ?? 'N/A' }}</td> {{-- Access customer name --}}
                                    <td>{{ $quotation->branch->name ?? 'N/A' }}</td>
                                    <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($quotation->grand_total, 2) }}</td> {{-- Display grand total --}}
                                    <td>
                                        <span class="badge {{ ($quotation->status == 'pending') ? 'badge-warning' : (($quotation->status == 'sent') ? 'badge-success' : 'badge-secondary') }}">
                                            {{ ucfirst($quotation->status) }}
                                        </span>
                                    </td>{{-- Display status --}}
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('quotations.edit', $quotation->id) }}">
                                                <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                            </a>

                                            <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" href="{{ route('quotations.destroy', $quotation->id) }}">
                                                <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                            </a>

                                            <a class="badge bg-info p-1" data-toggle="tooltip" data-placement="top" title="View Quotation" href="{{ route('quotations.show', $quotation->id) }}">
                                                <i class="ri-eye-line" style="font-size: 1.1rem;"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                  <td colspan="7" class="text-center">No quotations found.</td> {{-- Adjusted colspan --}}
                                </tr>
                            @endforelse
                          </tbody>
                       </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $quotations->links('pagination::bootstrap-5') }}
                    </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>

@endsection