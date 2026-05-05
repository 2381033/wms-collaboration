<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = "cy_invoice_detail";
    protected $fillable = [ 
        "id", 
        "invoice_id", 
        "outbound_id",
        "outbound_no",
        "job_no",
        "serial_id",
        "serial_no",
        "size_id",
        "container_no",
        "received_date",
        "dispatch_date",
        "leadtime",
        "lolo_amount",
        "storage_amount",
        "total_amount",
    ];
}