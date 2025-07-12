@extends('layouts.app')

@section('content')
{{-- Include the sidebar layout --}}
@include('layouts.sidebar')

{{-- Main content area --}}
<div class="content-page">
    {{-- Inner container for content, using container-fluid as per your layout --}}
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Discount Rules</h4>
                    </div>
                    {{-- Assuming $setting is available from a view composer or similar for primary color --}}
                    @php
                        use App\Models\Setting;
                        $setting = Setting::first(); // Fetch setting here if not already available
                        $primaryColor = $setting->primary_color ?? '#0d6efd'; // default blue
                    @endphp
                    <a href="{{ route('discount_rules.create') }}" class="btn text-white add-list" style="background-color: {{ $primaryColor }};">
                        <i class="las la-plus mr-3"></i>Add New Rule
                    </a>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Discount Rules</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table data-tables table-striped table-bordered table-hover"> {{-- Added table-hover for better UX --}}
                                <thead>
                                    <tr class="ligth">
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Coupon Code</th> {{-- New Column Header --}}
                                        <th>Discount</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($discountRules as $rule)
                                        <tr>
                                            <td>{{ $rule->name }}</td>
                                            <td>{{ ucfirst($rule->type) }}</td>
                                            <td>{{ $rule->coupon_code ?? '-' }}</td> {{-- Display Coupon Code or '-' if null --}}
                                            <td>{{ $rule->discount }}%</td>
                                            {{-- Format dates for better readability --}}
                                            <td>{{ $rule->start_date ? \Carbon\Carbon::parse($rule->start_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $rule->end_date ? \Carbon\Carbon::parse($rule->end_date)->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center list-action">
                                                    {{-- Edit Button --}}
                                                    <a class="badge bg-success mr-2 p-1" data-toggle="tooltip" data-placement="top" title="Edit"
                                                       href="{{ route('discount_rules.edit', $rule->id) }}">
                                                        <i class="ri-pencil-line" style="font-size: 1.1rem;"></i>
                                                    </a>
                                                    {{-- Delete Form --}}
                                                    <form action="{{ route('discount_rules.destroy', $rule->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="badge bg-warning mr-2 p-1 border-0" data-toggle="tooltip" data-placement="top" title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete this discount rule?')">
                                                            <i class="ri-delete-bin-line" style="font-size: 1.1rem;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No discount rules found.</td> {{-- Changed colspan to 7 --}}
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination links --}}
                        <div class="d-flex justify-content-end mt-3">
                            {{ $discountRules->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection