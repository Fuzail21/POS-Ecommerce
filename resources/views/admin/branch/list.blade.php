@extends('layouts.app')

@section('css')

<style>
    #DataTables_Table_0_filter,
    #DataTables_Table_0_length,
    #DataTables_Table_0_info,
    #DataTables_Table_0_paginate {
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
                    <h4 class="mb-3">Branch List</h4>
                    <a href="{{ route('branch.create') }}" class="btn text-white" style="background-color: {{ $primaryColor }};"><i class="las la-plus mr-2"></i>Add Branch</a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="card-header">
                        <h4 class="card-title">Branches</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table data-tables table-striped">
                                <thead style="background-color: #F4F5FA;">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Warehouse</th>
                                        <th>Contact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($branches as $branch)
                                        <tr>
                                            <td>{{ $branch->id }}</td>
                                            <td>{{ $branch->name }}</td>
                                            <td>{{ $branch->location }}</td>
                                            <td>{{ $branch->warehouse->name ?? '-' }}</td>
                                            <td>{{ $branch->contact }}</td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('branch.edit', $branch->id) }}">
                                                        <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                    <form action="{{ route('branch.delete', $branch->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this branch?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="badge bg-warning border-0 mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" style="cursor:pointer">
                                                            <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $branches->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
