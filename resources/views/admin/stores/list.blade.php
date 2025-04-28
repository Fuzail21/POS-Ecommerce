@extends('layouts.app')

@section('content')

    @include('layouts.sidebar')

    <div class="content-page">
      <div class="container-fluid">
         <div class="row">

            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Stores List</h4>
                        {{-- <p></p> --}}

                    </div>
                    <a href="{{ route('store.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Store</a>
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
                        <h4 class="card-title">Stores</h4>
                     </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                       <table id="datatable" class="table data-tables table-striped">
                          <thead>
                             <tr class="ligth">
                                <th>Id</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th colspan="2">Action</th>
                             </tr>
                          </thead>
                           <tbody>
                               @forelse($stores as $store)
                                   <tr>
                                       <td>{{ $store->id }}</td>
                                       <td>{{ $store->name }}</td>
                                       <td>{{ $store->location }}</td>
                                       <td>
                                           <div class="d-flex align-items-center list-action">
                                               <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('store.edit', $store->id) }}">
                                                   <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                               </a>
                                               <a class="badge bg-warning mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Delete" href="{{ route('store.delete', $store->id) }}">
                                                   <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                               </a>
                                           </div>
                                       </td>
                                   </tr>
                               @empty
                                   <tr>
                                       <td colspan="4" class="text-center">No records found.</td>
                                   </tr>
                               @endforelse
                           </tbody>

                       </table>
                    </div>

                    {{-- Pagination Links (outside table) --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $stores->links('pagination::bootstrap-5') }}
                    </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>

@endsection
