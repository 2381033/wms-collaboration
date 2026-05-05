<?php

namespace App\Models\Transaction\Api;

use Illuminate\Database\Eloquent\Model;

class EpmResponseApi extends Model
{
    protected $table = "iv_epm_response_api";
    protected $fillable = ["activity", "activity_id", "job_no", "status", "body", "error", "created_date"];
    protected $dates = ["created_date"];
}
