<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\User;
use App\Models\Status;
use App\Exports\loanReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;

class ReportsController extends Controller
{
    public function loanReport(Request $request)
{
    $query = Loan::query()->with(['client', 'collateral', 'user', 'status']);

    if ($request->filled('start_date')) {
        $query->whereDate('loan_start_date', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('loan_start_date', '<=', $request->end_date);
    }

    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    if ($request->filled('status_id')) {
        $query->where('status_id', $request->status_id);
    }

    if ($request->filled('loan_color')) {
        $query->where('loan_color', $request->loan_color);
    }

   $loans = $query->orderBy('loan_id', 'desc')->paginate(15)->appends($request->except('page'));


    $users = User::all();
    $statuses = Status::all();
    $colors = [
        1 => 'წითელი',
        2 => 'ყვითელი',
        3 => 'მწვანე',
        4 => 'ლურჯი'
    ];

    return view('reports.loans', compact('loans', 'users', 'statuses', 'colors'));
}

public function exportExcel(Request $request)
{
    $timestamp = Carbon::now()->format('Y-m-d-His');
    $filename = "loans-{$timestamp}.xlsx";

    return Excel::download(new LoanReportExport($request), $filename);
}
}
