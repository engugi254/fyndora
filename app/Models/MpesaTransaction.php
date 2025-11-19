<?php

namespace App\Models;  // Must be this

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model  // Class name exact
{
    use HasFactory;

    protected $fillable = [
    'MerchantRequestID',
    'CheckoutRequestID',
    'ResultCode',
    'ResultDesc',
    'Amount',
    'MpesaReceiptNumber',
    'PhoneNumber',
    'customer_name',
    'amount_paid',
    'customer_phone',
    'paid_at',
];

// Optional: format date nicely in JSON/admin views
protected $casts = [
    'paid_at' => 'datetime',
];
}