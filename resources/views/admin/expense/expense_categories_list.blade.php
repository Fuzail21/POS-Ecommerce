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
                        <h4 class="mb-3">Expense Categories</h4>
                    </div>
                    {{-- <a href="{{ route('customers.create') }}" class="btn btn-primary add-list">
                      <i class="las la-plus mr-3"></i>Add Expense Category
                    </a> --}}
                    <div class="col-md-2 mb-3 text-right">
                        <button type="button" class="btn text-white mt-4 w-100" style="background-color: {{ $primaryColor }};" data-toggle="modal" data-target="#addExpenseCategoryModal" onclick="openCategoryModal('add')">
                            Add Expense Category
                        </button>
                    </div>
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
                        <h4 class="card-title">Expense Category</h4>
                     </div>
                  </div>

                  <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table data-tables table-striped">
                            <thead>
                                <tr class="ligth">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenseCategories as $expenseCategory)
                                <tr>
                                    <td>{{ $expenseCategory->id }}</td>
                                    <td>{{ $expenseCategory->name }}</td>
                                    <td>
                                     <div class="d-flex align-items-center list-action">
                                         <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" onclick="openCategoryModal('edit', { id: {{ $expenseCategory->id }}, name: '{{ $expenseCategory->name }}' })" title="Edit">
                                             <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                         </a>
                                         <form action="{{ route('expense_categories.destroy', $expenseCategory->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this expense category?')">
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

                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $expenseCategories->links('pagination::bootstrap-5') }}
                    </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>


    <div class="modal fade" id="expenseCategoryModal" tabindex="-1" role="dialog" aria-labelledby="expenseCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" id="expenseCategoryForm">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseCategoryModalTitle">Add New Expense Category</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="categoryName" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="submitButton" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>


<script>
    function openCategoryModal(type, data = null) {
        const form = document.getElementById('expenseCategoryForm');
        const methodInput = document.getElementById('formMethod');
        const modalTitle = document.getElementById('expenseCategoryModalTitle');
        const submitButton = document.getElementById('submitButton');
        const nameInput = document.getElementById('categoryName');

        if (type === 'add') {
            form.action = "{{ route('expense_categories.store') }}";
            methodInput.value = "POST";
            modalTitle.textContent = "Add New Expense Category";
            submitButton.textContent = "Save";
            nameInput.value = "";
        } else if (type === 'edit' && data) {
            form.action = "/expense_categories/" + data.id;
            methodInput.value = "PUT";
            modalTitle.textContent = "Edit Expense Category";
            submitButton.textContent = "Update";
            nameInput.value = data.name;
        }

        $('#expenseCategoryModal').modal('show');
    }
</script>


@endsection
