<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    use HasFactory;

    protected $primaryKey = 'collateral_id';

    public $incrementing = true; // Set to true if it's auto-incrementing
    protected $keyType = 'int';

    protected $fillable = [
        'type_id', 'brand', 'model', 'email', 'pass', 'imei',
    ];
}