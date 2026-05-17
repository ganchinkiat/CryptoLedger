<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'pair',
        'asset',
        'side',
        'price',
        'quantity',
        'amount',
        'currency',
        'fee',
        'fee_currency',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'quantity' => 'decimal:8',
        'price' => 'decimal:8',
        'amount' => 'decimal:8',
        'fee' => 'decimal:8',
    ];
}
