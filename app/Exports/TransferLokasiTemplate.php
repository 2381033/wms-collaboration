<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction\Transfer\Job as TransferJob;

class TransferLokasiTemplate implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $job_id = null;

    public function __construct($job_id) {
        $this->job_id = $job_id;
    }
    
    private function getMySite()
    {
        $site_arr = DB::table('users_site')
            ->where('user_id', Auth::user()->id)
            ->pluck('site_id')
            ->toArray();
        $site = DB::table('iv_site')->whereIn('id', $site_arr)->get();

        return $site;
    }

    public function collection()
    {
        $job = TransferJob::find($this->job_id);
        $stock = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.id",
                            "a.product_code",
                            "a.lot_no",
                            "c.product_name",
                            "a.qtya",
                            "a.puom",
                            "a.location_code",
                        )
                        ->join("iv_product as c", "a.product_id", "c.id")
                        ->orderBy('a.location_code', 'ASC')
                        ->where("a.company_id", $job->company_id)
                        ->where("a.branch_id", $job->branch_id)
                        ->where("a.principal_id", $job->principal_id)
                        ->whereIn('site_id', $this->getMySite()->pluck('id')->toArray())
                        ->where("a.qtya", ">", 0)
                        ->where("a.freeze_flag", "No")
                        ->get();
        return $stock;
    }

    public function headings(): array
    {
        return [
            "ID",
            "Product Code",
            "Batch No",
            "Product Name",
            "SOA",
            "Unit",
            "Location Code From",
            "Qty Move",
            "Site",
            "Location Code To",
        ];
    }
}