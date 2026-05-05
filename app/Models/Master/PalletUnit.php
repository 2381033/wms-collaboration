<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class PalletUnit extends Model
{
    protected $table = "iv_pallet_unit";
    protected $fillable = [ 
        "id", 
        "company_id", 
        "principal_id", 
        "product_id", 
        "type_id", 
        "uom", 
        "pallet_qty", 
        "base_qty" 
    ];
}