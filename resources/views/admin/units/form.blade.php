@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">{{ $unit->exists ? 'Edit Unit' : 'Add Unit' }}</h4>
                    <a href="{{ route('units.list') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ $unit->exists ? route('units.update', $unit->id) : route('units.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Unit Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $unit->name) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="base_unit">Base Unit</label>
                                <input type="text" name="base_unit" class="form-control" value="{{ old('base_unit', $unit->base_unit) }}">
                            </div>

                            <div class="form-group">
                                <label for="conversion_factor">Conversion Factor</label>
                                <input type="number" step="any" name="conversion_factor" class="form-control" value="{{ old('conversion_factor', $unit->conversion_factor) }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ $unit->exists ? 'Update' : 'Create' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
