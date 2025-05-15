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
                        <h4 class="mb-3">Users List</h4>
                    </div>
                    <a href="{{ route('user.create') }}" class="btn btn-primary add-list">
                      <i class="las la-plus mr-3"></i>Add User
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
                        <h4 class="card-title">Users</h4>
                     </div>
                  </div>

                  <div class="card-body">
                    <div class="table-responsive">
                       <table id="datatable" class="table data-tables table-striped">
                          <thead>
                             <tr class="ligth">
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Action</th>
                             </tr>
                          </thead>
                          <tbody>
                             @forelse($users as $user)
                                <tr>
                                  <td>{{ $user->id }}</td>
                                  <td>{{ $user->name }}</td>
                                  <td>{{ $user->email }}</td>
                                  <td>{{ $user->role->name ?? '-' }}</td>
                                  <td>
                                     {{-- Example status badge --}}
                                     @if($user->status == 'Active')
                                       <span class="badge badge-success">Active</span>
                                     @else
                                       <span class="badge badge-secondary">Inactive</span>
                                     @endif
                                  </td>
                                  <td>
                                     <div class="d-flex align-items-center list-action">
                                         <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('user.edit', $user->id) }}">
                                             <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                         </a>
                                         <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" href="{{ route('user.delete', $user->id) }}">
                                             <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                         </a>
                                     </div>
                                  </td>
                                </tr>
                             @empty
                               <tr>
                                 <td colspan="6" class="text-center">No records found.</td>
                               </tr>
                             @endforelse
                          </tbody>
                       </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>

@endsection
