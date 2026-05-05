<?php

namespace App\Models\Transaction\Outbound;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "iv_outbound_order";
    protected $fillable = [ "company_id", "outbound_id", "principal_id", "job_no", "customer_id", "order_no", "po_number", "order_date", "due_date", "confirmed_flag", "user_id" ];
    protected $dates = ["order_date", "due_date"];
}