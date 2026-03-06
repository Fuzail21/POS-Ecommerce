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
                        <h4 class="mb-3">Customers List</h4>
                    </div>
                    <a href="{{ route('customers.create') }}" class="btn text-white add-list" style="background-color: {{ $primaryColor }};">
                      <i class="las la-plus mr-3"></i>Add Customer
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
                        <h4 class="card-title">Customers</h4>
                     </div>
                  </div>

                  <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table data-tables table-striped">
                            <thead>
                                <tr class="ligth">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $setting->currency_symbol ?? '$' }} {{ number_format($customer->balance, 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            {{-- Edit --}}
                                            <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit"
                                               href="{{ route('customers.edit', $customer->id) }}">
                                                <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                            </a>
                                    
                                            {{-- Delete --}}
                                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="badge bg-warning border-0 mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" style="cursor:pointer">
                                                    <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                </button>
                                            </form>
                                    
                                            {{-- Show Card --}}
                                            <a class="badge text-white p-1" style="background-color: {{ $secondaryColor }};" data-toggle="tooltip" data-placement="top" title="Show Card"
                                               href="{{ route('customers.card', $customer->id) }}" target="_blank">
                                                <i class="ri-profile-line" style="font-size: 1.1rem;"></i>
                                            </a>

                                        </div>
                                    </td>

                                  </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $customers->links('pagination::bootstrap-5') }}
                    </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>

@endsection
