@extends('layouts.app')

@section('content')
<div class="text-center mb-4">
    <h6>კონტრაქტები</h6>
</div>

<form action="{{ route('loans.index') }}" method="GET" class="form-row align-items-center mb-4">
    <div class="col-md-4 offset-md-1">
        <input type="text" class="form-control" name="search" placeholder="ძებნა კლიენტის ან უზრუნველყოფის მიხედვით" value="{{ request()->get('search') }}">
    </div>
<div class="col-md-2">
    <select class="form-control" name="status_id">
        <option value="0" {{ request()->get('status_id') === '0' ? 'selected' : '' }}>ყველა სტატუსი</option>
        <option value="active_group" {{ request()->get('status_id', 'active_group') === 'active_group' ? 'selected' : '' }}>მიმდინარე</option>
        @foreach($statuses as $status)
            <option value="{{ $status->id }}" {{ request()->get('status_id') == $status->id ? 'selected' : '' }}>
                {{ $status->name }}
            </option>
        @endforeach
    </select>
</div>

    <div class="col-md-2">
        <select class="form-control" name="loan_color">
            <option value="">ყველა ფერი</option>
            <option value="1" {{ request()->get('loan_color') == '1' ? 'selected' : '' }}>თეთრი</option>
            <option value="2" {{ request()->get('loan_color') == '2' ? 'selected' : '' }}>წითელი</option>
            <option value="3" {{ request()->get('loan_color') == '3' ? 'selected' : '' }}>მწვანე</option>
            <option value="4" {{ request()->get('loan_color') == '4' ? 'selected' : '' }}>ყვითელი</option>
        </select>
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-primary">ძებნა</button>
    </div>
</form>



<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('loans.create') }}" class="btn btn-primary">ახალი კონტრაქტი</a>
    
    <div class="p-3 rounded" style="background-color:rgb(233, 238, 234); max-width: 400px;">
        
        <p class="mb-0">ჯამი {{ number_format($activeLoanSums->total_amount, 2) }} ₾ , % {{ number_format($monthlyInterest, 2) }} ₾, ძირი {{ number_format($monthlyPrincipal, 2) }} ₾ </p>
    </div>
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
                <th>@</th>
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
        2 => '#f8d7da', // light red
        3 => '#d4edda', // light green
        4 => '#fff3cd', // light yellow
        default => '#ffffff' // white
    };
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
    <td>{{ $loan->collateral->email ?? 'N/A' }}</td>
    <td>{{ number_format($loan->loan_amount, 2) }}</td>
    <td>{{ number_format($loan->next_payment_amount, 2) }}</td>
    <td>{{ number_format($loan->interest, 2) }}</td>
    <td>{{ $loan->next_payment_date ? $loan->next_payment_date->format('Y-m-d') : 'N/A' }}</td> 
    <td>{{ $loan->user->name ?? 'N/A' }}</td>
    <td>{{ $loan->status->name ?? 'N/A' }}</td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="loanActions{{ $loan->loan_id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                მოქმედება
            </button>
            <div class="dropdown-menu" aria-labelledby="loanActions{{ $loan->loan_id }}">
                <a class="dropdown-item" href="{{ route('loans.show', $loan->loan_id) }}">დეტალები</a>
                <button type="button" class="dropdown-item" data-toggle="modal" data-target="#setColorModal{{ $loan->loan_id }}">ფერის დაყენება</button>
                <button type="button" class="dropdown-item" data-toggle="modal" data-target="#setNoteModal{{ $loan->loan_id }}">შენიშვნა</button>
            </div>
        </div>
    </td>
</tr>

<!-- Set Color Modal -->
<div class="modal fade" id="setColorModal{{ $loan->loan_id }}" tabindex="-1" role="dialog" aria-labelledby="setColorModalLabel{{ $loan->loan_id }}" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('loans.setColor', $loan->loan_id) }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">ფერის დაყენება</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="დახურვა">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn" style="background-color: #ffffff; border: 1px solid #ccc;">
              <input type="radio" name="loan_color" value="1" required> თეთრი
            </label>
            <label class="btn" style="background-color: #f8d7da;">
              <input type="radio" name="loan_color" value="2" required> წითელი
            </label>
            <label class="btn" style="background-color: #d4edda;">
              <input type="radio" name="loan_color" value="3" required> მწვანე
            </label>
            <label class="btn" style="background-color: #fff3cd;">
              <input type="radio" name="loan_color" value="4" required> ყვითელი
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">შენახვა</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Set Note Modal -->
<div class="modal fade" id="setNoteModal{{ $loan->loan_id }}" tabindex="-1" role="dialog" aria-labelledby="setNoteModalLabel{{ $loan->loan_id }}" aria-hidden="true">
  <div class="modal-dialog" role="document">
<form action="{{ route('loans.setNote', $loan->loan_id) }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">შენიშვნა</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="დახურვა">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <textarea class="form-control" name="note" rows="3" placeholder="შეიყვანეთ შენიშვნა..." required></textarea>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">შენახვა</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endforeach
        </tbody>
    </table>
    {{ $loans->appends(request()->except('page'))->links() }}
<!-- </div> -->
@endsection
