<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Handling extends Model
{
    protected $table = "iv_handling";
    protected $fillable = [ 
        "company_id", 
        "principal_id", 
        "job_type",
        "foc", 
        "cpu", 
        "cpu_lowest", 
        "cpu_middle", 
        "cpu_ext", 
        "quota", 
        "foc_return",
        "cpu_return",
        "quota_return",
        "cpu_ext_return",
        "remarks",
        "active"
    ];
}