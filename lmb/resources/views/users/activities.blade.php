@extends('layouts.app')

@section('content')
<div class="text-center mb-4">
    <h6>User Activities</h6>
</div>

@include('users.user_activities_form')

<table class="table">
    <thead>
        <tr>
            <th>Loan ID</th>
            <th>Client Name</th>
            <th>Loan Amount</th>
            <th>Comments</th>
            <th>Payments</th>
        </tr>
    </thead>
    <tbody>
        @foreach($loans as $loan)
        <tr>
            <td>{{ $loan->loan_id }}</td>
            <td>{{ $loan->client->name }}</td>
            <td>{{ number_format($loan->loan_amount, 2) }}</td>
            <td>
                @foreach($loan->comments as $comment)
                    <p>{{ $comment->comment }}</p>
                @endforeach
            </td>
            <td>
                @foreach($loan->payments as $payment)
                    <p>${{ number_format($payment->payment_amount, 2) }}</p>
                @endforeach
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
