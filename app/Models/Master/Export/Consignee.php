<?php

namespace App\Models\Master\Export;

use Illuminate\Database\Eloquent\Model;

class Consignee extends Model
{
    protected $table = "mt_consignee";
    protected $fillable = [
        "branch_id",
        "consignee_name",
        "active"
    ];
}
