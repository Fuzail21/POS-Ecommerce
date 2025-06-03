@extends('layouts.app')

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4>{{ isset($supplier) ? 'Edit' : 'Add' }} Supplier</h4>
                            </div>
                        </div>

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                        <div class="card-body">
                            <form action="{{ isset($supplier) ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" method="POST">
                                @csrf
                                @if(isset($supplier))
                                    @method('POST')
                                @endif

                                <div class="form-group">
                                    <label>Supplier Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name ?? '') }}" required>
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
                                    @error('contact_person') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}">
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}">
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control" rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
                                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Opening Balance</label>
                                    <input type="number" name="balance" step="0.01" class="form-control" value="{{ old('balance', $supplier->balance ?? '') }}">
                                    @error('balance') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    {{ isset($supplier) ? 'Update Supplier' : 'Add Supplier' }}
                                </button>
                                <a href="{{ route('suppliers.list') }}" class="btn btn-secondary">Back</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
