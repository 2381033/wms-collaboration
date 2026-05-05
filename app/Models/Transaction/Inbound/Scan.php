<?php

// app/Models/Scan.php

namespace App\Models\Transaction\Inbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $table = "ex_scan_carton";

    protected $guarded = [];
    public $timestamps = false; // Optional, if you don't want automatic timestamps
}
