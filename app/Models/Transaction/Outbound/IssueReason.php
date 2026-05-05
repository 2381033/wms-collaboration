<?php

namespace App\Models\Transaction\Outbound;

use Illuminate\Database\Eloquent\Model;

class IssueReason extends Model
{
    protected $table = "iv_issue_reason";
    protected $fillable = [ "principal_id", "job_no", "job_date", "outbound_id", "order_no", "rating", "issue_id", "notes", "user_id"];    
}