<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $table = "cy_payment_detail";
    protected $fillable = [ 
        "id", 
        "payment_id", 
        "invoice_id",
        "invoice_no",
        "invoice_amount",
        "payment_amount",
        "user_id"
    ];
}