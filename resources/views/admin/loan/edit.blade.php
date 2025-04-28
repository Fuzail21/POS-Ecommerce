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
                            <h4 class="card-title">Edit Loan</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('loan.update', $loan->id) }}" method="post" data-toggle="validator">
                            @csrf
                            <input type="hidden" name="loan_id" value="{{ $loan->id }}">

                            <div class="row">  
                                <div class="col-md-12">                      
                                    <div class="form-group">
                                        <label>User Type *</label>
                                        <select class="form-control mb-3" name="user_type" id="user_type" required>
                                            <option value="">Select User Type...</option>
                                            <option value="customer" {{ $loan->user_type == 'customer' ? 'selected' : '' }}>Customer</option>
                                            <option value="vendor" {{ $loan->user_type == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>User *</label>
                                        <select class="form-control mb-3" name="user_id" id="user_id" required>
                                            <option value="">Select User...</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $user->id == $loan->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Amount *</label>
                                        <input type="number" name="amount" class="form-control" placeholder="Enter Amount"
                                            value="{{ $loan->amount }}" required>                                                                                                   
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                {{-- <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Status *</label>
                                        <select class="form-control mb-3" name="status" required>
                                            <option value="">Select Status...</option>
                                            <option value="pending" {{ $loan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="paid" {{ $loan->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        </select>
                                    </div>
                                </div> --}}

                            </div>                            

                            <button type="submit" class="btn btn-primary mr-2">Update Loan</button>
                            <a href="{{ route('loan.list') }}" class="btn btn-secondary">Cancel</a>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#user_type').on('change', function () {
        var userType = $(this).val();
        if (userType !== '') {
            $.ajax({
                url: '{{ route("get.users.by.type") }}',
                type: 'GET',
                data: { user_type: userType },
                success: function (response) {
                    $('#user_id').empty().append('<option value="">Select User...</option>');
                    $.each(response, function (key, user) {
                        $('#user_id').append('<option value="' + user.id + '">' + user.name + '</option>');
                    });
                }
            });
        } else {
            $('#user_id').empty().append('<option value="">Select User...</option>');
        }
    });

    // Optional: Load users when page loads for pre-selected user type
    $(document).ready(function () {
        const userType = $('#user_type').val();
        const selectedUserId = '{{ $loan->user_id }}';

        if (userType) {
            $.ajax({
                url: '{{ route("get.users.by.type") }}',
                type: 'GET',
                data: { user_type: userType },
                success: function (response) {
                    $('#user_id').empty().append('<option value="">Select User...</option>');
                    $.each(response, function (key, user) {
                        const selected = user.id == selectedUserId ? 'selected' : '';
                        $('#user_id').append('<option value="' + user.id + '" ' + selected + '>' + user.name + '</option>');
                    });
                }
            });
        }
    });
</script>

@endsection
