<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class InvoiceHeader extends Model
{
    protected $table = "cy_invoice_header";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "job_no",
        "job_date",
        "forwarder_id",
        "forwarder_payment",
        "amount",
        "adm_amount",
        "tax_flag",
        "tax_amount",
        "invoice_amount",
        "payment_amount",
        "review_flag",
        "confirmed_flag",
        "confirmed_by",
        "confirmed_date",
        "payment_flag",
        "payment_by",
        "payment_date",
        "user_id"
    ];
}