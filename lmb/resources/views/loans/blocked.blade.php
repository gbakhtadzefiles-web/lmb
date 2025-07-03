@extends('layouts.app')

@section('content')
<div class="container">
    <h6 class="text-center">დაბლოკილების სია</h6>
    <table class="table">
        <br/>
        <thead>
            <tr>
                <th>#</th>
                <th>კლიენტი</th>
                <th>პირადი ნომ.</th>
                <th>ტელეფონი</th>
                <th>მოდელი</th>
                <th>მეილი</th> <!-- Add header for Collateral Email -->
                <th>სესხი</th>
                <th>სტატუსი</th>
                <th>დეტალები</th>
                <th>განბლიკვა</th>
            </tr>
        </thead>
        <tbody>
            @foreach($blocked as $loan)
            <tr>
                <td>{{ $loan->loan_id }}</td>
                <td>{{ $loan->client->name }}</td>
                <td>{{ $loan->client->personal_id }}</td>
                <td>{{ $loan->client->phone }}</td>
                <td>{{ $loan->collateral->model }}
                <td>{{ $loan->collateral->email }}</td> <!-- Display Collateral Email -->
                <td>{{ number_format($loan->loan_amount, 2) }}</td>
                <td>{{ $loan->status->name }}</td>
                <td>
                <a href="{{ route('loans.show', $loan->loan_id) }}" class="btn btn-info btn-sm">დეტალები</a>
                </td>
                <td>
                <form action="{{ route('loans.changeStatus', $loan->loan_id) }}" method="POST">
            @csrf
            <input type="hidden" name="status_id" value="1"> <!-- The status ID for "blocked" -->
            <button type="submit" class="btn btn-warning btn-sm">განბლოკვა</button>
                </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
