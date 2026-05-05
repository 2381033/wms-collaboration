<?php

namespace App\Models\Transaction\Api;

use Illuminate\Database\Eloquent\Model;

class Epmlog extends Model
{
    protected $table = "iv_epm_api_logs";
    protected $fillable = ["activity", "activity_id", "job_no", "status", "body", "error", "created_date", "send_status"];
    protected $dates = ["created_date"];
}
