@extends('layouts.app')


@section('css')

<style>
    #datatable_length,
    #datatable_filter,
    #datatable_info,
    #datatable_paginate {
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Units List</h4>
                    <a href="{{ route('units.create') }}" class="btn text-white" style="background-color: {{ $primaryColor }};"><i class="las la-plus mr-2"></i>Add Unit</a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="datatable" class="table data-tables table-striped">
                                <thead style="background-color: #F4F5FA;">
                                   <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Base Unit</th>
                                        <th>Conversion Factor</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($units as $unit)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $unit->name }}</td>
                                            <td>{{ $unit->base_unit }}</td>
                                            <td>{{ $unit->conversion_factor }}</td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    <a class="badge bg-success mr-2 p-1" href="{{ route('units.edit', $unit->id) }}" data-toggle="tooltip" title="Edit">
                                                        <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                    <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" href="{{ route('units.destroy', $unit->id) }}">
                                                        <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $units->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
