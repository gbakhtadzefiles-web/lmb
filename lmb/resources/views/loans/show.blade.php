@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h6 class="mb-4 text-center">დეტალები</h6>
    <div class="card">
        <div class="card-header">
            სესხის დეტალები
        </div>
        <div class="card-body">
            <p><strong>სესხი#:</strong> {{ $loan->loan_id }}</p>
            <p><strong>თანხა:</strong> {{ number_format($loan->loan_amount, 2) }}</p>
            <p><strong>პროცენტი:</strong> {{ $loan->interest_rate }}%</p>
            <p><strong>გაცემის თარიღი:</strong> {{ $loan->loan_start_date->format('Y-m-d') }}</p>
            <p><strong>შემდეგი გადახდის თარიღი:</strong> {{ $loan->next_payment_date->format('Y-m-d') }}</p>
            <p><strong>შემოსატალი პროცენტი</strong> {{ number_format($loan->interest, 2) }}</p>
            <p><strong>სტატუსი:</strong> {{ $loan->status->name ?? 'N/A' }}</p>
        </div>
    </div>

    @if($loan->client)
    <div class="card mt-4">
        <div class="card-header">
            კლიენტის ინფორმაცია
        </div>
        <div class="card-body">
            <p><strong>სახელი:</strong> {{ $loan->client->name }}</p>
            <p><strong>პირადი ნომერი:</strong> {{ $loan->client->personal_id }}</p>
            <p><strong>ტელეფონი:</strong> {{ $loan->client->phone }}</p>
        </div>
    </div>
    @endif

    @if($loan->collateral)
    <div class="card mt-4">
        <div class="card-header">
            უზრუნველყოფა
        </div>
        <div class="card-body">
            <p><strong>ბრენდი:</strong> {{ $loan->collateral->brand }}</p>
            <p><strong>მოდელი:</strong> {{ $loan->collateral->model }}</p>
            <p><strong>მეილი:</strong> {{ $loan->collateral->email }}</p>
            <p><strong>კოდი:</strong> {{ $loan->collateral->pass }}</p>
        </div>
    </div>
    @endif
    <!-- Payment Button -->
    <div class="container mt-4">

    <!-- Existing content... -->

    <!-- New Actions Section -->
    <div class="row justify-content-between mt-4">
        <div class="col-auto">
            <h6>ქმედებები</h6>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#updateEmailModal">
          მეილი
          </button>
        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#updatePassModal">
           კოდი
         </button>
        
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paymentModal">
                პროცენტის შეტანა
            </button>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#principalModal">
                სესხის განულება
            </button>
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#commentModal">
                კომენტარი
            </button>
        </div>
    </div>

    <!-- Existing content... -->
</div>
            @if(!empty($loan->note))
<div class="alert alert-danger mt-4" role="alert">
    <strong>შენიშვნა:</strong> {{ $loan->note }}
</div>
@endif
  
    <div class="card mt-4">
        <div class="card-header">
            პროცენტის კალკულაციები
        </div>
        @if($interestCalculations->isNotEmpty())
        <table class="table">
            <thead>
                <tr>
                    <th>თარიღი</th>
                    <th>პროცენტის თანხა</th>
                </tr>
            </thead>
            <tbody>
                @foreach($interestCalculations as $calculation)
                <tr>
                    <td>{{ $calculation->calculation_date }}</td>
                    <td>{{ number_format($calculation->interest_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif


   
<div class="card mt-4">
    <div class="card-header">
        გადახდები
    </div>
    @if($payments->isNotEmpty())
    <table class="table">
        <thead>
            <tr>
                <th>დრო</th>
                <th>თანხა</th>
                <th>მომხმარებელი</th>
                <th>ტიპი</th>
                <th>კომენტარი</th>
            </tr>
        </thead>

        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->payment_time }}</td>
                <td>{{ number_format($payment->payment_amount, 2) }}</td>
                <td>{{ $payment->user->name }}</td>
                <td>
                    @if($payment->payment_type == 1)
                        სესხის განულება
                    @elseif($payment->payment_type == 2)
                        პროცენტის შემოტანა
                    @else
                        სხვა
                    @endif
                </td>
                <td>{{ $payment->comment }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
<div class="card mt-4">
    <div class="card-header">
        კომენტარები
    </div>
@if($comments->isNotEmpty())
<table class="table mt-4">
    <thead>
        <tr>
        <th>თარიღი</th>
            <th>ოპერატორი</th>
            <th>კოემნტარი</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach($comments as $comment)
        <tr>
        <td>{{ $comment->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $comment->user->name }}</td>
            <td>{{ $comment->comment }}</td>
            
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endif
</div>
<br/>
<div class="row justify-content-end">
<div class="col-auto">
@if($loan->status_id == 1)
<form action="{{ route('loans.changeStatus', $loan->loan_id) }}" method="POST">
            @csrf
            <input type="hidden" name="status_id" value="5"> <!-- The status ID for "blocked" -->
            <button type="submit" class="btn btn-secondary" onclick="return confirm('დარწმუნებული ხართ რომ საჭიროა გაუქმება?')">გაუქმება</button>
                </form>
@elseif($loan->status_id == 5)   
<form action="{{ route('loans.changeStatus', $loan->loan_id) }}" method="POST">
            @csrf
            <input type="hidden" name="status_id" value="1"> <!-- The status ID for "blocked" -->
            <button type="submit" class="btn btn-success" onclick="return confirm('დარწმუნებული ხართ რომ საჭიროა თავიდან გახსნა?')">გააქტიურება</button>
                </form>
@endif

                </div>
                </div>
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">პროცენტის გადახდა</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
            </div>
            <form action="{{ route('loans.payInterest', $loan->loan_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">თანხა</label>
                        <input type="number" step="0.01" class="form-control" id="payment_amount" name="payment_amount" value="{{ number_format($loan->interest, 2, '.', '') }}" readonly>
                    </div>
                    <div class="mb-3">
            <label for="comment" class="form-label">კომენტარი</label>
            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                        </div>
                </div>
      
                <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">დახურვა</button> -->

                    <button type="submit" class="btn btn-primary">გადახდა</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="principalModal" tabindex="-1" aria-labelledby="principalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="principalModalLabel">სესხის განულება</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('loans.payLoanPrincipal', $loan->loan_id) }}" method="POST" id="principalPaymentForm">
                @csrf
                <div class="modal-body">
                    <div id="principalModalInfo"></div> <!-- Info or error panel -->
                    <div class="mb-3">
                        <label for="principal_payment_amount" class="form-label">თანხა</label>
                        <input type="number" step="0.01" class="form-control" id="principal_payment_amount" name="payment_amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="principal_comment" class="form-label">კომენტარი</label>
                        <textarea class="form-control" id="principal_comment" name="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">დახურვა</button> -->
                    <button type="submit" class="btn btn-primary" id="submitPrincipalPayment">გადახდა</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="updateEmailModal" tabindex="-1" aria-labelledby="updateEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateEmailModalLabel">მეილის განახლება</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('collaterals.updateEmail', $loan->collateral->collateral_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_email" class="form-label">ახალი მეილი</label>
                        <input type="email" class="form-control" id="new_email" name="new_email" required>
                    </div>
                    <input type="hidden" name="loan_id" value="{{ $loan->loan_id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">დახურვა</button>
                    <button type="submit" class="btn btn-primary">განახლება</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updatePassModal" tabindex="-1" aria-labelledby="updatePassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePassModalLabel">ხელის კოდი შეყვანა</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('collaterals.updatePass', $loan->collateral->collateral_id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_pass" class="form-label">შეიყვანეთ კოდი</label>
                        <input type="number" class="form-control" id="new_pass" name="new_pass" required>
                    </div>
                    <input type="hidden" name="loan_id" value="{{ $loan->loan_id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">დახურვა</button>
                    <button type="submit" class="btn btn-primary">განახლება</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">ახალი კომენტარი</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('loans.comments.store', $loan->loan_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="comment">ტექსტი</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">შენახვა</button>
                </div>
            </form>
        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Assuming the loan interest and amount are available as JavaScript variables
    // Convert them from PHP variables using blade syntax
    var loanInterest = @json($loan->interest);
    var loanAmount = @json($loan->loan_amount);
    // Target the elements in the modal
    var infoPanel = document.getElementById('principalModalInfo');
    var paymentAmountInput = document.getElementById('principal_payment_amount');
    var submitBtn = document.getElementById('submitPrincipalPayment');
    var form = document.getElementById('principalPaymentForm');

    // Display loan amount or error message based on interest
    if (loanInterest != 0) {
        infoPanel.innerHTML = `<div class="alert alert-danger" role="alert">დაფაპარია პროცენტი - ${loanInterest} ლარი</div>`;
        paymentAmountInput.disabled = true;
        submitBtn.disabled = true;
    } else {
        infoPanel.innerHTML = `<div class="alert alert-info" role="alert">შემოსატანი თანხა: ${loanAmount} ლარი</div>`;
        paymentAmountInput.disabled = false;
        submitBtn.disabled = false;
    }

    // Validate the payment amount equals the loan amount on form submission
// Validate the payment amount equals the loan amount on form submission
form.addEventListener('submit', function(event) {
    var paymentAmount = parseFloat(paymentAmountInput.value);
    var difference = Math.abs(paymentAmount - loanAmount); // Absolute difference

    // Check if the difference is too small to matter (e.g., less than a cent)
    if (difference >= 0.01) {
        event.preventDefault(); // Prevent form submission
        alert(`სესხის გასანულებლად უნდა შემოიტანოთ -  ${loanAmount}`);
    }
});
});
</script>

@endsection
<!-- Payment Modal -->
