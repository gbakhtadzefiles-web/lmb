@extends('layouts.app')

@section('content')
<div class="text-center mb-4">
    <h6>ფილტრი გადახდები</h6>
</div>

<form action="{{ route('payments.filter') }}" method="GET" class="form-row align-items-center mb-4">
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
            <th>სესხის ნომერი</th>
            <th>კლიენტი</th>
            <th>ტიპი</th>
            <th>თანხა</th>
            <th>კომენტარი</th>
            <th>გადახდის დრო</th>
            <th>ოპერატორი</th>
            <th>ქმედებები</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr>
            <td>{{ $payment->payment_id }}</td>
            <td>{{ $payment->loan_id }}</td>
            <td>{{ $payment->loan->client->name ?? 'N/A' }}</td>
            <td>
            @if($payment->payment_type == 1)
            ძირი თანხის შემოტანა
        @elseif($payment->payment_type == 2)
            პროცენტის შემოტანა
        @else
            სხვა
        @endif
</td>
            <td>{{ $payment->payment_amount }}</td>
            <td>{{ $payment->comment }}</td>
            
            <td>{{ $payment->payment_time ? $payment->payment_time->format('Y-m-d H:i') : 'N/A' }}</td>
            <td>{{ $payment->user->name ?? 'N/A' }}</td>
            <td>
                <a href="{{ route('loans.show', $payment->loan->loan_id) }}" class="btn btn-info btn-sm">სესხის დეტალები</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $payments->appends(request()->except('page'))->links() }}

@endsection
