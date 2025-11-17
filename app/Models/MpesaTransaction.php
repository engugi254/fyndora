<?php

namespace App\Models;  // Must be this

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model  // Class name exact
{
    use HasFactory;

    protected $fillable = [  // Add your fields to allow mass assignment
        'MerchantRequestID',
        'CheckoutRequestID',
        'ResultCode',
        'ResultDesc',
        'Amount',
        'MpesaReceiptNumber',
        'PhoneNumber',
    ];
}