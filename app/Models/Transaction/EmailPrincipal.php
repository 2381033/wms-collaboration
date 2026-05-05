<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class EmailPrincipal extends Model
{
    protected $table = "iv_email_principal";
    protected $fillable = [ "company_id", "branch_id", "principal_id", "description", "subject", "email_to", "email_cc", "email_bcc", "active" ];
}