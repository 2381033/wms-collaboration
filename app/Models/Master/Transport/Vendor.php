<?php

namespace App\Models\Master\Transport;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = "tm_vendor";
    protected $fillable = [ 
        "id", 
        "vendor_code",
        "vendor_name",
        "active" 
    ];
}