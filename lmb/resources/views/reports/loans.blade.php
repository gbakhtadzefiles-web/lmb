@extends('layouts.app')

@section('content')
<div class="text-center mb-4">
    <h6>სესხების რეპორტი</h6>
</div>

<form method="GET" action="{{ route('reports.loans') }}" class="form-row align-items-center mb-4">
    <div class="col-2">
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="საწყისი თარიღი">
    </div>
    <div class="col-2">
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="დასასრული თარიღი">
    </div>
    <div class="col-2">
        <select name="user_id" class="form-control">
            <option value="">ყველა ოპერატორი</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-2">
        <select name="status_id" class="form-control">
            <option value="">ყველა სტატუსი</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-2">
        <select name="loan_color" class="form-control">
            <option value="">ყველა ფერი</option>
            @foreach($colors as $key => $name)
                <option value="{{ $key }}" {{ request('loan_color') == $key ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-2">
        <button type="submit" class="btn btn-primary">ძიება</button>
    </div>
</form>
<div class="mb-3">
    <a href="{{ route('reports.loans.export', request()->all()) }}" class="btn btn-success">
        Excel ექსპორტი
    </a>
</div>

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
            @php
                $rowColor = match($loan->loan_color) {
                    2 => '#f8d7da',  // light red
                    3 => '#d4edda',  // light green
                    4 => '#fff3cd',  // light yellow
                    default => '#ffffff',
                };

                if ($loan->loan_color == 1 && $loan->loan_type == 2) {
                    $rowColor = '#d1ecf1';  // light blue
                }

                $noteText = $loan->note ?? '';
            @endphp

            <tr style="background-color: {{ $rowColor }}" title="{{ $noteText }}">
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

{{ $loans->links() }}

@endsection
