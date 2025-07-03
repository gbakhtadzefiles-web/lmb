@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Edit Loan</h2>
    <form method="POST" action="{{ route('loans.update', $loan->loan_id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="client_id">Client ID</label>
            <input type="number" class="form-control" id="client_id" name="client_id" value="{{ $loan->client_id }}" required>
        </div>

        <div class="form-group">
            <label for="collateral_id">Collateral ID</label>
            <input type="number" class="form-control" id="collateral_id" name="collateral_id" value="{{ $loan->collateral_id }}" required>
        </div>

        <div class="form-group">
            <label for="loan_amount">Loan Amount</label>
            <input type="text" class="form-control" id="loan_amount" name="loan_amount" value="{{ $loan->loan_amount }}" required>
        </div>

        <div class="form-group">
            <label for="interest_rate">Interest Rate (%)</label>
            <input type="number" class="form-control" id="interest_rate" name="interest_rate" value="{{ $loan->interest_rate }}" required>
        </div>

        <div class="form-group">
            <label for="loan_start_date">Loan Start Date</label>
            <input type="date" class="form-control" id="loan_start_date" name="loan_start_date" value="{{ $loan->loan_start_date->format('Y-m-d') }}" required>
        </div>

        <div class="form-group">
            <label for="next_payment_date">Next Payment Date</label>
            <input type="date" class="form-control" id="next_payment_date" name="next_payment_date" value="{{ $loan->next_payment_date->format('Y-m-d') }}" required>
        </div>

        <div class="form-group">
            <label for="next_payment_amount">Next Payment Amount</label>
            <input type="text" class="form-control" id="next_payment_amount" name="next_payment_amount" value="{{ $loan->next_payment_amount }}" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <input type="number" class="form-control" id="status" name="status" value="{{ $loan->status }}" required>
        </div>

        <div class="form-group">
            <label for="user_id">User ID</label>
            <input type="number" class="form-control" id="user_id" name="user_id" value="{{ $loan->user_id }}" required>
        </div>

        <div class="form-group">
            <label for="branch_id">Branch ID</label>
            <input type="number" class="form-control" id="branch_id" name="branch_id" value="{{ $loan->branch_id }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
