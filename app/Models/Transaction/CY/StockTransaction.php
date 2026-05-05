<?php

namespace App\Models\Transaction\CY;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $table = "cy_stock_transaction";
    protected $fillable = [ 
        "id", 
        "branch_id", 
        "principal_id",
        "booking_id",
        "inbound_id",
        "booking_no",
        "job_no",
        "job_date",
        "job_type",
        "serial_no",
        "invoice_type",
        "reference_no",
        "driver_name",
        "vehicle_no",
        "size_id",
        "container_status",
        "container_no",
        "qty",
        "reference_job",
        "user_id"
    ];
}