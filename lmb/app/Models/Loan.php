<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $primaryKey = 'loan_id';

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
    public function collateral()
    {
        return $this->belongsTo(Collateral::class, 'collateral_id', 'collateral_id');
    }
    public function status()
{
    return $this->belongsTo(Status::class, 'status_id', 'id');
}
public function comments()
{
    return $this->hasMany(Comment::class, 'loan_id');
}

    // Assuming you already have the $fillable property set
    protected $fillable = [
        'client_id', 'collateral_id', 'loan_amount', 'interest_rate', 
        'loan_start_date', 'next_payment_date', 'next_payment_amount', 
        'status', 'user_id', 'branch_id', 'interest', 'number_of_d',
    ];

    // Add or update the $casts property to include your date fields
    protected $casts = [
        'loan_start_date' => 'datetime',
        'next_payment_date' => 'datetime',
        // Include any other date/datetime fields here
    ];
}
