<?php
namespace App\Exports;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class LoanReportExport implements FromCollection, WithHeadings, WithTitle
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('loans')
            ->join('users', 'loans.user_id', '=', 'users.id')
            ->join('collaterals', 'loans.collateral_id', '=', 'collaterals.collateral_id')
            ->join('clients', 'loans.client_id', '=', 'clients.client_id')
            ->select(
                'loans.loan_id', 'loans.loan_type', 'loans.loan_color', 'loans.note', 'loans.loan_amount',
                'loans.interest_rate', 'loans.number_of_d', 'loans.loan_start_date', 'loans.next_payment_date',
                'loans.interest', 'loans.next_payment_amount', 'loans.status_id',
                'loans.created_at', 'loans.updated_at',
                'collaterals.brand as collateral_brand', 'collaterals.model as collateral_model',
                'collaterals.email as collateral_email', 'collaterals.pass as collateral_pass',
                'collaterals.created_at as collateral_created_at', 'collaterals.updated_at as collateral_updated_at',
                'users.id as user_id', 'users.name as user_name', 'users.email as user_email', 'users.branch_id as user_branch',
                'clients.client_id', 'clients.name as client_name', 'clients.personal_id', 'clients.phone', 'clients.email as client_email'
            );

        // 🧠 Apply filters from the request
        if ($this->request->filled('start_date')) {
            $query->whereDate('loans.loan_start_date', '>=', $this->request->start_date);
        }

        if ($this->request->filled('end_date')) {
            $query->whereDate('loans.loan_start_date', '<=', $this->request->end_date);
        }

        if ($this->request->filled('user_id')) {
            $query->where('loans.user_id', '=', $this->request->user_id);
        }

        if ($this->request->filled('status_id')) {
            $query->where('loans.status_id', '=', $this->request->status_id);
        }

        if ($this->request->filled('loan_color')) {
            $query->where('loans.loan_color', '=', $this->request->loan_color);
        }

        return $query->orderByDesc('loans.loan_id')->get();
    }
    public function headings(): array
{
    return [
        'loan_id', 'loan_type', 'loan_color', 'note', 'loan_amount',
        'interest_rate', 'number_of_d', 'loan_start_date', 'next_payment_date',
        'interest', 'next_payment_amount', 'status_id', 'loan_created_at', 'loan_updated_at',
        'collateral_brand', 'collateral_model', 'collateral_email', 'collateral_pass',
        'collateral_created_at', 'collateral_updated_at',
        'user_id', 'user_name', 'user_email', 'user_branch',
        'client_id', 'client_name', 'personal_id', 'phone', 'client_email'
    ];
}

    public function title(): string
    {
        return 'Loan Report';
    }
}

   

