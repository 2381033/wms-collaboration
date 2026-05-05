<?php

namespace App\Models\Master\Export;

use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    protected $table = "mt_shipper";
    protected $fillable = [
        "branch_id",
        "shipper_name",
        "active"
    ];
}
