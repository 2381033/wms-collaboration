<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "cy_payment";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "job_no",
        "job_date",
        "forwarder_id",
        "invoice_amount",
        "payment_amount",
        "payment_date",
        "confirmed_flag",
        "confirmed_by",
        "confirmed_date",
        "user_id"
    ];
}