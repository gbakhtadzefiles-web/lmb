<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    protected $casts = [
        'payment_time' => 'datetime',
    ];


    protected $fillable = [
        'loan_id', 'payment_amount', 'payment_time', 'user_id', 'payment_type', 'comment',
    ];
    

    // Define relationships if necessary, e.g., Payment belongs to a Loan
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'loan_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}