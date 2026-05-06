<?php

namespace App\Models\Transaction\Export;

use Illuminate\Database\Eloquent\Model;

class InboundHeader extends Model
{
    protected $table = "ex_inbound_header";
    protected $fillable = [
        "id",
        "branch_id",
        "job_no",
        "job_date",
        "vehicle_no",
        "po_number",
        "forwarder_id",
        "shipper_id",
        "consignee_id",
        "destination",
        "peb_no",
        "pic_name",
        "qty_cargo",
        "qty_actual",
        "cbm",
        "vgm",
        "weight",
        "total_pallet",
        "status_flag",
        "user_id",
        "vehicle_no_by_ao",
        "gate_in_by_ao",
    ];
}
