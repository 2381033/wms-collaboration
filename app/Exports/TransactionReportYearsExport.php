<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Master\Principal as MasterPrincipal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;

class TransactionReportYearsExport implements FromView
{
    protected $branch_id = null;
    protected $principal_id = null;
    protected $start = null;
    protected $end = null;
    protected $product_code = null;
    protected $site_id = null;
    protected $area_id = null;
    protected $location_code = null;
    protected $batch = null;
    protected $report_type = null;

    public function __construct($branch_id, $principal_id, $start, $end, $product_code, $site_id, $area_id, $location_code, $batch, $report_type)
    {
        $this->branch_id = $branch_id;
        $this->principal_id = $principal_id;
        $this->start = $start;
        $this->end = $end;
        $this->product_code = $product_code;
        $this->site_id = $site_id;
        $this->area_id = $area_id;
        $this->location_code = $location_code;
        $this->batch = $batch;
        $this->report_type = $report_type;
    }

    public function view(): View
    {
        $type = '';
        if($this->report_type == 'inbound'){
            $type = 'IMP';
        }else{
            $type = 'EXP';
        }
        $data = DB::table('iv_stock_transaction as a')
            ->select(
                'a.job_no',
                'a.site_id',
                'a.area_id',
                'a.job_date',
                'a.job_type',
                'a.product_code',
                'b.product_name',
                'a.lot_no',
                'a.mfg_date',
                'a.exp_date',
                'a.location_code',
                'b.uppp',
                'b.muppp',
                'a.qty',
                'b.puom',
                'b.muom',
                'a.pqty',
                'a.mqty',
                'b.volume',
                'b.gross_weight',
                'e.container_no',
                DB::raw("CASE WHEN a.job_type = 'IMP' THEN 'Inbound' ELSE 'Outbound' END as jobType"),
            )
            ->join('iv_product as b', 'a.product_id', 'b.id')
            ->leftjoin('iv_inbound_vehicle as e', 'a.job_no', 'e.job_no')
            ->where('a.company_id', 1)
            ->where('a.principal_id', $this->principal_id)
            ->where('a.branch_id', $this->branch_id)
            ->where('a.job_type', $type)
            ->whereBetween('a.created_at', [date($this->start), date($this->end)])
            ->orderBy('a.job_date', 'asc')
            ->get();

            if(!is_null($this->product_code)){
                $data = $data->where('product_code', $this->product_code);
            }
            if(!is_null($this->batch)){
                $data =  $data->where('lot_no', $this->batch);
            }
            if(!is_null($this->batch)){
                $data =  $data->where('site_id', $this->batch);
            }
            if(!is_null($this->area_id)){
                $data =  $data->where('area_id', $this->area_id);
            }
            if(!is_null($this->location_code)){
                $data =  $data->where('location_code', $this->location_code);
            }
            $principal = MasterPrincipal::find($this->principal_id);
        return view('report.transaction-report.productYearsExcel', compact('data','principal'));
    }
}
