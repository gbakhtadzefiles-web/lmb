<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    use HasFactory;
    protected $primaryKey = 'client_id';

    public function loans()
    {
        return $this->hasMany(Loan::class, 'client_id');
    }
   

    protected $fillable = [
        'name',
        'personal_id', // Add this line
        'phone',
        // Add any other fields that you want to be mass-assignable
    ];

}
