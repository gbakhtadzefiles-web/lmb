<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestCalculation extends Model
{
    protected $table = 'interestcalculations'; // Define the table name if it's not the default naming convention

    protected $fillable = [
        'loan_id',
        'calculation_date',
        'interest_amount',
        // Add other columns as fillable attributes as needed
    ];

    // If you have relationships, define them here. For example:
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}