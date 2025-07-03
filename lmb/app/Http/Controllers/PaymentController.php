<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\User;
use App\Models\Loan;

class PaymentController extends Controller
{
    public function filter(Request $request)
    {
        $query = Payment::query();

        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_time', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_time', '<=', $request->end_date);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $payments = $query->with(['loan.client', 'loan'])->paginate(10);

        $users = User::all();

        return view('payments.filter', compact('payments', 'users'));
    }
}