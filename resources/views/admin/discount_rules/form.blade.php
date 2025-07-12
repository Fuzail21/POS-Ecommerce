@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row justify-content-start">
            <div class="col-md-12">
                <h2 class="mb-4">{{ $edit ? 'Edit' : 'Add' }} Discount Rule</h2>
                <form action="{{ $edit ? route('discount_rules.update', $rule->id) : route('discount_rules.store') }}" method="POST" class="mb-3">
                    @csrf
                    @if($edit)
                        @method('PUT')
                    @endif

                    <div class="form-group mb-3">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $rule->name ?? '') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="type-select">Type</label>
                        <select name="type" class="form-control" id="type-select">
                            <option value="product" {{ old('type', $rule->type ?? '') === 'product' ? 'selected' : '' }}>Product</option>
                            <option value="category" {{ old('type', $rule->type ?? '') === 'category' ? 'selected' : '' }}>Category</option>
                            {{-- New option for Coupon --}}
                            <option value="coupon" {{ old('type', $rule->type ?? '') === 'coupon' ? 'selected' : '' }}>Coupon</option>
                        </select>
                        @error('type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- New input for Coupon Code, initially hidden --}}
                    <div class="form-group mb-3" id="coupon-code-group" style="display: none;">
                        <label for="coupon_code">Coupon Code</label>
                        <input type="text" name="coupon_code" id="coupon_code" class="form-control" value="{{ old('coupon_code', $rule->coupon_code ?? '') }}">
                        @error('coupon_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3" id="targets-group">
                        <label for="targets-select">Targets</label>
                        {{-- The multiple attribute is crucial for Select2 to display tags --}}
                        <select name="target_ids[]" class="form-control" id="targets-select" multiple="multiple">
                            {{-- Options will be loaded dynamically by JavaScript --}}
                        </select>
                        @error('target_ids')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="discount">Discount (%)</label>
                        <input type="number" name="discount" id="discount" class="form-control" value="{{ old('discount', $rule->discount ?? '') }}" required min="0" max="100">
                        @error('discount')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $rule->start_date ?? '') }}" required>
                        @error('start_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $rule->end_date ?? '') }}" required>
                        @error('end_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">{{ $edit ? 'Update' : 'Create' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- JQuery (Select2 depends on it) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Custom styles for Select2 to give it a more "textarea-like" appearance */
        .select2-container--default .select2-selection--multiple {
            min-height: 180px; /* Adjust as needed to make it look like a textarea */
            border: 1px solid #ced4da; /* Bootstrap's default form-control border color */
            border-radius: 0.25rem; /* Bootstrap's default border-radius */
            padding: 0.375rem 0.75rem; /* Bootstrap's default padding for form-control */
            display: block; /* Ensure it takes full width */
            width: 100%;
        }

        /* Adjust padding for the search input within the Select2 container */
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding-right: 0; /* Remove default right padding */
            padding-left: 0; /* Remove default left padding */
        }

        /* Style for individual tags */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff; /* Primary color for tags */
            color: white;
            border: 1px solid #007bff;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            margin: 0.25rem;
            line-height: 1.2;
            display: inline-flex; /* Use flex for alignment of text and close icon */
            align-items: center;
        }

        /* Style for the close button on tags */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-left: 0.5rem;
            font-weight: bold;
            cursor: pointer;
        }

        /* Adjust placeholder text color */
        .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
            color: #6c757d; /* Muted text color */
        }
    </style>

    <script>
        $(document).ready(function () {
            // Function to load targets based on the selected type (product or category)
            function loadTargets(type) {
                let data = [];
                // Only load data if type is product or category
                if (type === 'product') {
                    data = @json($products ?? []);
                } else if (type === 'category') {
                    data = @json($categories ?? []);
                }

                let select = $('#targets-select');

                // Clear existing options from the Select2 instance
                select.empty();

                // Populate the select element with new options
                data.forEach(item => {
                    select.append(`<option value="${item.id}">${item.name}</option>`);
                });

                // Get the initially selected target IDs (for edit mode)
                // Ensure $rule->target_ids is correctly JSON decoded if it's stored as a string
                let selected = @json(json_decode($rule->target_ids ?? '[]', true));

                // Set the selected values and trigger 'change' to update Select2's display
                select.val(selected).trigger('change');
            }

            // Function to toggle visibility of coupon code and targets fields
            function toggleFields(type) {
                if (type === 'coupon') {
                    $('#coupon-code-group').show();
                    $('#targets-group').hide();
                    // Clear Select2 selections when targets are hidden for coupon type
                    $('#targets-select').val(null).trigger('change');
                } else {
                    $('#coupon-code-group').hide();
                    $('#targets-group').show();
                }
            }

            // Initialize Select2 on the #targets-select element
            $('#targets-select').select2({
                tags: false,
                placeholder: 'Select targets',
                allowClear: true
            });

            // Listen for changes on the 'Type' select dropdown
            $('#type-select').change(function () {
                const selectedType = $(this).val();
                loadTargets(selectedType); // Load targets based on the new type
                toggleFields(selectedType); // Toggle visibility of fields
            });

            // Initial load and toggle when the page is ready
            // This ensures the correct targets are loaded and selected on page load (especially in edit mode)
            const initialType = $('#type-select').val();
            loadTargets(initialType);
            toggleFields(initialType);
        });
    </script>
@endsection
