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
                                    <h4>{{ isset($category) ? 'Edit' : 'Add' }} Category</h4>
                                </div>
                            </div>

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                            <div class="card-body">
                                
                                <form action="{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Category Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Parent Category</label>
                                        <select name="parent_id" class="form-control">
                                            <option value="">None</option>
                                            @foreach($parents as $parent)
                                                <option value="{{ $parent->id }}" {{ (old('parent_id', $category->parent_id ?? '') == $parent->id) ? 'selected' : '' }}>
                                                    {{ $parent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Create' }}</button>
                                    <a href="{{ route('categories.list') }}" class="btn btn-secondary">Back</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page end  -->
            </div>
      </div>
        



    @endsection


    