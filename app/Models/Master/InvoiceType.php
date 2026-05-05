<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class InvoiceType extends Model
{
    protected $table = "cy_invoice_type";
    protected $fillable = [ 
        "company_id", 
        "type_name", 
        "invoice_flag", 
        "free_flag", 
        "free_storage", 
        "active" 
    ];
}