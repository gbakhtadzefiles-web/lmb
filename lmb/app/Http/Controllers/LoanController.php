<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Client;
use App\Models\Status;
use App\Models\Collateral;
use App\Models\InterestCalculation;
use App\Models\Payment;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\InterestCalculationService;
class LoanController extends Controller
{
  public function index(Request $request)
{
    $statuses = Status::all(); // For status dropdown
    $query = Loan::with(['client', 'collateral', 'status', 'user']);
    $activeLoanSums = Loan::whereIn('status_id', [1, 3, 4])
    ->selectRaw('SUM(loan_amount) as total_amount')
    ->first();

$monthlyPayments = DB::table('payments')
    ->select('payment_type', DB::raw('SUM(payment_amount) as total'))
    ->whereMonth('payment_time', now()->month)
    ->whereYear('payment_time', now()->year)
    ->groupBy('payment_type')
    ->pluck('total', 'payment_type');

$monthlyInterest = $monthlyPayments[2] ?? 0;
$monthlyPrincipal = $monthlyPayments[1] ?? 0;


    // Filter by search text (client or collateral)
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($query) use ($search) {
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('personal_id', 'like', "%$search%")
                  ->orWhere('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            })
            ->orWhereHas('collateral', function ($q) use ($search) {
                $q->where('brand', 'like', "%$search%")
                  ->orWhere('model', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            })
            ->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        });
    }

// Filter by status
$statusId = $request->input('status_id', 'active_group');

if ($statusId === 'active_group') {
    $query->whereIn('status_id', [1, 3, 4]);
} elseif ($statusId === '0') {
    // No filter - all statuses
} else {
    $query->where('status_id', $statusId);
}


    // Filter by loan color
    if ($request->filled('loan_color')) {
        $query->where('loan_color', $request->input('loan_color'));
    }

    // Filter by branch (non-admins only)
    if (auth()->user()->role_id != 1) {
        $branch_id = auth()->user()->branch_id;
        $query->whereHas('user', function ($q) use ($branch_id) {
            $q->where('branch_id', $branch_id);
        });
    }

    $loans = $query->orderBy('loan_id', 'desc')->paginate(100);

    return view('loans.index', compact('loans', 'statuses', 'activeLoanSums', 'monthlyInterest', 'monthlyPrincipal'))->withRequest($request->all());
}

    
    
    public function create()
    {
        return view('loans.create');
    }

   public function store(Request $request)
{
    DB::beginTransaction();
    try {
        $validated = $request->validate([
            'personal_id' => 'required|string|size:11',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|size:9',
            'loan_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'loan_start_date' => 'required|date',
            'number_of_d' => 'required|integer|min:1',
            'collateral_type' => 'required|integer',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'email' => 'required|email',
            'pass' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'loan_type' => 'required|in:1,2',
        ]);

        $client = Client::firstOrCreate(
            ['personal_id' => $validated['personal_id']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
            ]
        );

        // Create collateral
        $collateral = new Collateral();
        $collateral->type_id = $validated['collateral_type'];
        $collateral->brand = $validated['brand'];
        $collateral->model = $validated['model'];
        $collateral->email = $validated['email'];
        $collateral->pass = $validated['pass'] ?? '00000';
        $collateral->emai = $validated['imei'];
        $collateral->client_id = $client->client_id; // Assuming you want to link collateral to client
        $collateral->save();

        $interest = $validated['loan_amount'] * ($validated['interest_rate'] / 100);
        $nextPaymentDate = now()->addDays((int)$validated['number_of_d'])->format('Y-m-d');
        $nextPaymentAmount = $validated['loan_amount'] + $interest;

        $loan = new Loan([
            'client_id' => $client->client_id,
            'collateral_id' => $collateral->collateral_id,
            'loan_amount' => $validated['loan_amount'],
            'interest_rate' => $validated['interest_rate'],
            'loan_start_date' => $validated['loan_start_date'],
            'next_payment_date' => $nextPaymentDate,
            'next_payment_amount' => $nextPaymentAmount,
            'user_id' => Auth::id(),
            'interest' => $interest,
            'number_of_d' => $validated['number_of_d'],
            'loan_type' => $validated['loan_type'],
        ]);
        $loan->save();

        if ($request->filled('comment')) {
            Comment::create([
                'loan_id' => $loan->loan_id,
                'user_id' => Auth::id(),
                'comment' => $request->input('comment'),
            ]);
        }

        DB::commit();
        return redirect()->route('loans.index')->with('success', 'Loan, client, and collateral created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Loan creation failed: ' . $e->getMessage());
        return back()->withErrors('Failed to create loan: ' . $e->getMessage())->withInput();
    }
}


    public function show($id)
    {
        $loan = Loan::with(['client', 'collateral', 'comments.user'])->findOrFail($id);
        $interestCalculations = InterestCalculation::where('loan_id', $loan->loan_id)->get();
        // Load payments and their related user data
        $payments = Payment::where('loan_id', $loan->loan_id)->with('user')->get();
        // Assuming you have set up a relationship named 'comments' in Loan model
        // that relates to Comment model, which in turn has a 'user' relationship.
        $comments = $loan->comments; // This will contain user data due to eager loading
    
        return view('loans.show', compact('loan', 'interestCalculations', 'payments', 'comments'));
    }

    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        return view('loans.edit', compact('loan'));
    }

    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update($request->all());
        return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
    }

    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();
        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
    }
    public function payInterest(Request $request, $loan_id)
    {
        // Log the Loan ID and User ID immediately since they're already defined
       // \Log::debug("Loan ID: $loan_id");
       // \Log::debug("User ID: " . Auth::id());
    
        // Start a database transaction
        DB::beginTransaction();
    
        try {
            // Find the loan by ID or fail
            $loan = Loan::findOrFail($loan_id);
    
            // Validate the request data
            $request->validate([
                'payment_amount' => 'required|numeric|min:0.01',
            ]);
    
            // Now that $paymentAmount is defined, it's safe to log it
            $paymentAmount = $request->input('payment_amount');
          //  \Log::debug("Payment Amount: $paymentAmount");
    
            // Check if the payment amount exceeds the current interest
            if ($paymentAmount > $loan->interest) {
                return back()->withErrors('Payment amount exceeds the current interest.');
            }
    
            // Deduct the payment amount from the loan's interest and update the loan
            $loan->interest -= $paymentAmount;
            $loan->save();
    
            // Record this payment in the payments table
            $payment = new Payment([
                'loan_id' => $loan->loan_id,
                'payment_amount' => $paymentAmount,
                'payment_time' => now(),
                'user_id' => Auth::id(), // Use Auth facade to get the authenticated user's ID
                'payment_type' => 2, // Assuming 2 stands for interest payment
                'comment' => $request->input('comment'), // Capture the optional comment
            ]);
            $payment->save();
    
            // Commit the transaction
            DB::commit();
    
            // Redirect back with a success message
            return redirect()->route('loans.show', $loan_id)->with('success', 'Interest payment successful.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            
            // Log the error
            \Log::error("Interest payment failed for Loan ID {$loan_id}: " . $e->getMessage());
    
            // Redirect back with an error message
            return back()->withErrors('Failed to make interest payment: ' . $e->getMessage())->withInput();
        }
    }
    
    public function payLoanPrincipal(Request $request, $loan_id)
    {
        // Start a database transaction
        DB::beginTransaction();
    
        try {
            // Find the loan by ID or fail
            $loan = Loan::findOrFail($loan_id);
    
            // Validate the request data
            $request->validate([
                'payment_amount' => 'required|numeric|min:0.01',
            ]);
    
            $paymentAmount = $request->input('payment_amount');
    
            // Check if the payment amount exceeds the current loan amount
            if ($paymentAmount > $loan->loan_amount) {
                return back()->withErrors('Payment amount exceeds the current loan amount.');
            }
    
            // Deduct the payment amount from the loan's amount and update the loan
            $loan->loan_amount -= $paymentAmount;
            $loan->save();
    
            // Record this payment in the payments table
            $payment = new Payment([
                'loan_id' => $loan->loan_id,
                'payment_amount' => $paymentAmount,
                'payment_time' => now(),
                'user_id' => Auth::id(), // Use Auth facade to get the authenticated user's ID
                'payment_type' => 1, // Payment type for loan principal payment
                'comment' => $request->input('comment'), // Capture the optional comment
            ]);
            $payment->save();
    
            // Commit the transaction
            DB::commit();
    
            // Redirect back with a success message
            return redirect()->route('loans.show', $loan_id)->with('success', 'Loan principal payment successful.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
    
            // Log the error
            \Log::error("Loan principal payment failed for Loan ID {$loan_id}: " . $e->getMessage());
    
            // Redirect back with an error message
            return back()->withErrors('Failed to make loan principal payment: ' . $e->getMessage())->withInput();
        }
    }
    public function toblock()
    {
        $query = Loan::select('loans.*')
                     ->join('collaterals', 'loans.collateral_id', '=', 'collaterals.collateral_id')
                     ->where('loans.status_id', '=', '3') // Assuming status_id '3' indicates loans to be blocked
                     ->with(['client', 'collateral', 'user']); // Eager load related data
    
        // Restrict loans display for non-admin users
        if (auth()->user()->role_id != 1) {
            $branch_id = auth()->user()->branch_id;
            $query->whereHas('user', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
        }
    
        $loansToBlock = $query->orderBy('collaterals.email', 'asc')->get();
    
        return view('loans.toblock', compact('loansToBlock'));
    }
public function blocked()
{
    $query = Loan::select('loans.*')
                 ->join('collaterals', 'loans.collateral_id', '=', 'collaterals.collateral_id')
                 ->where('loans.status_id', '=', '4') // Assuming status_id '4' indicates loans that are blocked
                 ->with(['client', 'collateral', 'user']); // Eager load related data

    // Restrict loans display for non-admin users
    if (auth()->user()->role_id != 1) {
        $branch_id = auth()->user()->branch_id;
        $query->whereHas('user', function ($q) use ($branch_id) {
            $q->where('branch_id', $branch_id);
        });
    }

    $blocked = $query->orderBy('collaterals.email', 'asc')->get();

    return view('loans.blocked', compact('blocked'));
}


public function changeStatus(Request $request, $loan_id)
{
    $request->validate([
        'status_id' => 'required|integer|exists:status,id',
    ]);

    DB::beginTransaction();
    try {
        $loan = Loan::findOrFail($loan_id);
        $status = Status::findOrFail($request->status_id);
        $statusName = $status->name; // Name of the new status

        // Update the loan's status
        $loan->status_id = $request->status_id;
        $loan->save();

        // Compose the comment string
        $commentText = "სტატუსის ცვლილება - {$statusName}.";

        // Add the comment
        $comment = new Comment();
        $comment->loan_id = $loan->loan_id;
        $comment->comment = $commentText;
        $comment->user_id = Auth::id();
        $comment->save();

        DB::commit();
        // Redirect back to the previous page with a success message
        return redirect()->back()->with('success', "Loan status updated to '{$statusName}' and comment added successfully.");
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Failed to change status for Loan ID {$loan_id}: " . $e->getMessage());
        // Redirect back to the previous page with an error message
        return back()->withErrors('Failed to change loan status and add comment: ' . $e->getMessage());
    }
}  
public function filter(Request $request)
    {
        $query = Loan::query();

        if ($request->has('start_date') && $request->start_date) {
            $query->where('loan_start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('loan_start_date', '<=', $request->end_date);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $loans = $query->paginate(10);

        $users = User::all();
        $statuses = Status::all();

        return view('loans.filter', compact('loans', 'users', 'statuses'));
    }
    
    public function UpdateColor(Request $request, $loan_id)
    {
        $request->validate([
            'loan_color' => 'required|integer|in:1,2,3,4',
        ]);

        $loan = Loan::findOrFail($loan_id);
        $oldColor = $loan->loan_color;
        $loan->loan_color = $request->loan_color;
        $loan->save();

        Comment::create([
            'loan_id' => $loan->loan_id,
            'user_id' => Auth::id(),
            'comment' => "ფერი შეიცვალა ({$oldColor} → {$request->loan_color})",
        ]);

        return redirect()->route('loans.index')->with('success', 'ფერი წარმატებით განახლდა.');
    }

    public function UpdateNote(Request $request, $loan_id)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $loan = Loan::findOrFail($loan_id);
        $oldNote = $loan->note;
        $loan->note = $request->note;
        $loan->save();

        Comment::create([
            'loan_id' => $loan->loan_id,
            'user_id' => Auth::id(),
            'comment' => "შენიშვნა განახლდა: \"{$request->note}\"",
        ]);

        return redirect()->route('loans.index')->with('success', 'შენიშვნა წარმატებით განახლდა.');
    }
    
}
