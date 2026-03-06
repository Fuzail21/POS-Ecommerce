@extends('layouts.app')


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
                        <h4 class="mb-3">Expense</h4>
                    </div>
                    <a href="{{ route('expense.create') }}" class="btn text-white add-list" style="background-color: {{ $primaryColor }};">
                      <i class="las la-plus mr-3"></i>Add Expense
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

                  <div class="card-header d-flex justify-content-between align-items-center">
                     <div class="header-title">
                        <h4 class="card-title">Expense</h4>
                     </div>
                     <form method="GET" action="{{ route('expense.list') }}" class="d-flex align-items-center gap-2">
                         <input type="month" name="month" value="{{ $month ?? '' }}"
                                class="form-control form-control-sm" style="width:180px;">
                         <button type="submit" class="btn btn-sm text-white" style="background-color: {{ $primaryColor }};">Filter</button>
                         @if($month)
                             <a href="{{ route('expense.list') }}" class="btn btn-sm btn-secondary">Clear</a>
                         @endif
                     </form>
                  </div>

                  <div class="card-body">
                    @if($month)
                    <p class="text-muted mb-2">
                        Showing expenses for <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</strong>
                        &mdash; Total: <strong>{{ $setting->currency_symbol ?? '$' }} {{ number_format($expenses->sum('amount'), 2) }}</strong>
                    </p>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="ligth">
                                    <th>#</th>
                                    <th>Branch</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Expense Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->id }}</td>
                                    <td>{{ $expense->branch->name ?? '-' }}</td>
                                    <td>{{ $expense->category->name ?? '-' }}</td>
                                    <td>{{$setting->currency_symbol ?? '$'}} {{ number_format($expense->amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                                    <td>{{ $expense->creator->name ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('expense.edit', $expense->id) }}">
                                                <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                            </a>
                                            <form action="{{ route('expense.destroy', $expense->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this expense?')">
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
                        {{ $expenses->links('pagination::bootstrap-5') }}
                    </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
    </div>


@endsection
