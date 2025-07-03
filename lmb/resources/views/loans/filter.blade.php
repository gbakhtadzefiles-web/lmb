@extends('layouts.app')

@section('content')
<div class="text-center mb-4">
    <h6>ფილტრი სესხები</h6>
</div>

<form action="{{ route('loans.filter') }}" method="GET" class="form-row align-items-center mb-4">
    <div class="col-3">
        <input type="date" class="form-control" name="start_date" placeholder="საწყისი თარიღი" value="{{ request()->get('start_date') }}">
    </div>
    <div class="col-3">
        <input type="date" class="form-control" name="end_date" placeholder="დასასრული თარიღი" value="{{ request()->get('end_date') }}">
    </div>
    <div class="col-3">
        <select class="form-control" name="user_id">
            <option value="">ყველა ოპერატორი</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request()->get('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-3">
        <button type="submit" class="btn btn-primary">ძებნა</button>
    </div>
</form>

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>პირადი ნომერი</th>
            <th>სახელი</th>
            <th>ტელეფონი</th>
            <th>ბრენდი</th>
            <th>მოდელი</th>
            <th>კოდი</th>
            <th>თანხა</th>
            <th>შემოსატანი</th>
            <th>%</th>
            <th>შემოტანის თარიღი</th>
            <th>ოპერატორი</th>
            <th>სტატუსი</th>
            <th>ქმედებები</th>
        </tr>
    </thead>
    <tbody>
        @foreach($loans as $loan)
        <tr>
            <td>{{ $loan->loan_id }}</td>
            <td>{{ $loan->client->personal_id ?? 'N/A' }}</td>
            <td>{{ $loan->client->name ?? 'N/A' }}</td>
            <td>{{ $loan->client->phone ?? 'N/A' }}</td>
            <td>{{ $loan->collateral->brand ?? 'N/A' }}</td>
            <td>{{ $loan->collateral->model ?? 'N/A' }}</td>
            <td>{{ $loan->collateral->pass ?? 'N/A' }}</td>
            <td>{{ number_format($loan->loan_amount, 2) }}</td>
            <td>{{ number_format($loan->next_payment_amount, 2) }}</td>
            <td>{{ number_format($loan->interest, 2) }}</td>
            <td>{{ $loan->next_payment_date ? $loan->next_payment_date->format('Y-m-d') : 'N/A' }}</td>
            <td>{{ $loan->user->name ?? 'N/A' }}</td>
            <td>{{ $loan->status->name ?? 'N/A' }}</td>
            <td>
                <a href="{{ route('loans.show', $loan->loan_id) }}" class="btn btn-info btn-sm">დეტალები</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $loans->appends(request()->except('page'))->links() }}

@endsection
