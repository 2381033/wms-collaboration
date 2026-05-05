<?php

namespace App\Exports\Crossdock;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockLedgerExport implements FromCollection, WithHeadings
{
    protected $id_branch;
    protected $id_warehouse;
    protected $id_customer;
    protected $report_type;

    public function __construct($id_branch, $id_warehouse, $id_customer, $report_type)
    {
        $this->id_branch   = $id_branch;
        $this->id_warehouse = $id_warehouse;
        $this->id_customer = $id_customer;
        $this->report_type   = $report_type;
    }

    public function collection(): Collection
    {
        $baseQuery = DB::table('cross_stock_ledger as c')
            ->join('cross_mt_warehouse as w', 'w.id', '=', 'c.id_warehouse')
            ->join('cross_mt_customer as cu', 'cu.id', '=', 'c.id_customer')
            ->leftJoin('cross_inbound_header as ih', 'ih.id', '=', 'c.id_inbound')
            ->where('c.id_branch', $this->id_branch)
            ->where('c.id_warehouse', $this->id_warehouse)
            ->where('c.on_hand', '>', 0);
        if (is_numeric($this->id_customer)) {
            $baseQuery->where('c.id_customer', $this->id_customer);
            // $id_customer = $this->id_customer;
        } else {
            // $id_customer = 0;
        }

        if ($this->report_type == 'summary') {
            return $baseQuery
                ->selectRaw('
                w.name as warehouse,
                cu.name as customer,
                SUM(c.on_hand) as on_hand,
                SUM(c.on_booking) as on_booking,
                SUM(c.on_actual) as on_actual,
                SUM(c.w) as weight_total,
                SUM(c.on_hand * c.cbm_per_unit) as total_cbm
            ')
                ->groupBy('cu.name', 'w.name')
                ->orderBy('cu.name')
                ->get();
        } else {
            return $baseQuery
                // ->where('c.id_customer', $id_customer)
                ->select([
                    'w.name as warehouse',
                    'c.job_no',
                    'cu.name as customer',
                    'ih.remarks as inbound_remark',
                    'c.id_cargo',
                    'c.created_at',
                    'c.p',
                    'c.l',
                    'c.t',
                    'c.w',
                    'c.cbm_per_unit',
                    'c.on_hand',
                    DB::raw('COALESCE(c.on_hand, 0) as on_hand'),
                    DB::raw('COALESCE(c.on_booking, 0) as on_booking'),
                    DB::raw('COALESCE(c.on_actual, 0) as on_actual'),
                    'c.unit',
                ])
                ->orderBy('cu.name')
                ->orderBy('c.sku')
                ->get();
        }
    }

    public function headings(): array
    {
        if ($this->report_type == 'summary') {
            return [
                'Warehouse',
                'Customer',
                'SOH',
                'SOB',
                'SOA',
                'Weight Total (Kg)',
                'Vol. Total (Cbm)',
            ];
        } else {
            return [
                "Warehouse",
                "Job No",
                "Customer",
                "Description",
                "Cargo ID",
                "Date In",
                "P",
                "L",
                "T",
                "Weight Per Unit (Kg)",
                "Vol. Per Unit (Cbm)",
                "SOH",
                "SOB",
                "SOA",
                "UOM",
            ];
        }
    }
}
