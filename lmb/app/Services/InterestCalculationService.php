<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\InterestCalculation;
use Illuminate\Support\Facades\DB;

class InterestCalculationService
{
    /**
     * Calculate and update interest for a given loan.
     *
     * @param  \App\Models\Loan  $loan
     * @return bool
     */
    public function calculateInterest(Loan $loan)
    {
        try {
            DB::beginTransaction();

            // Calculate interest amount
            $interestAmount = ($loan->loan_amount * $loan->interest_rate) / 100;

            // Update loan with new interest and next payment amount
            $loan->interest = $loan->interest + $interestAmount;
            $loan->next_payment_amount = $loan->next_payment_amount + $interestAmount;
            $loan->save();

            // Log interest calculation
            $interestCalculation = new InterestCalculation([
                'loan_id' => $loan->loan_id,
                'calculation_date' => now(),
                'interest_amount' => $interestAmount,
            ]);
            $interestCalculation->save();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            // Log error or handle exception
            // Log::error("Failed to calculate interest: " . $e->getMessage());

            return false;
        }
    }
}
