<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table = "iv_email";
    protected $fillable = [ "company_id", "description", "subject", "email_to", "email_cc", "email_bcc", "active" ];
}