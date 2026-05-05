<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\Master\Handling as MasterHandling;
use App\Models\Master\Storage as MasterStorage;

class HandlingExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $principal_id = null;
    protected $date_from = null;
    protected $date_to = null;
    protected $date_diff = null;

    public function __construct($principal, $date_from, $date_to, $date_diff) {
        $this->principal_id = $principal;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->date_diff = $date_diff;
    }

    public function collection()
    {
        $company_id = Auth::user()->company_id;

        $stock_before = DB::table("iv_stock_transaction as a")
                            ->select(
                                DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty * b.volume ELSE -1 * a.qty * b.volume END ) as qty_open"),
                            )
                            ->join("iv_product as b", "a.product_id", "b.id")
                            ->where("a.company_id", $company_id)
                            ->where("a.principal_id", $this->principal_id)
                            ->where("a.job_date", "<", date($this->date_from))
                            ->first();

        $list = DB::table("iv_stock_transaction as a")
                        ->select(
                            "a.job_date",
                            DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty * b.volume ELSE -1 * a.qty * b.volume END ) as qty"),
                            DB::raw("sum(CASE WHEN a.job_type IN ('IMP') THEN a.qty * b.volume ELSE 0 END ) as qty_inbound"),
                            DB::raw("sum(CASE WHEN a.job_type IN ('EXP') THEN a.qty * b.volume ELSE 0 END ) as qty_outbound")
                        )
                        ->join('iv_product as b', 'a.product_id', 'b.id')
                        ->where('a.company_id', $company_id)
                        ->where('a.principal_id', $this->principal_id)
                        ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                        ->groupBy("a.job_date")
                        ->orderBy('a.job_date', 'asc')
                        ->get();

        $storage = [];
        $storage_list = [];
        $hand_in = [];
        $hand_out = [];

        if ( isset($stock_before) ) {
            if ( $stock_before->qty_open > 0 ) {
                $open_qty = $stock_before->qty_open;
            } else {
                $open_qty = 0;
            }
        } else {
            $open_qty = 0;
        }

        $balance = $open_qty;

        for ($i=0; $i <= $this->date_diff; $i++) {
            $date = \Carbon\Carbon::parse($this->date_from)->addDays($i);

            $data = $list->where("job_date", $date)->first();

            $handling_in = 0;
            $handling_out = 0;

            if ( isset($data) ) {
                $balance = $balance + $data->qty;
                $handling_in = $data->qty_inbound;
                $handling_out = $data->qty_outbound;
            } else {
                $balance = $balance;
                $handling_in = 0;
                $handling_out = 0;
            }

            if ( $date > \Carbon\Carbon::today() ) {
                $balance = 0;
                $handling_in = 0;
                $handling_out = 0;
            }

            $storage_list[] = [
                "date" => $date->format("Y-m-d"),
                "qty_storage" => $balance,
                "handling_in" => $handling_in,
                "handling_out" => $handling_out
            ];

            $storage[] = $balance;
            $hand_in[] = $handling_in;
            $hand_out[] = $handling_out;
        }

        $qty_storage = array_sum($storage) / count($storage);
        $qty_inbound = array_sum($hand_in);
        $qty_outbound = array_sum($hand_out);

        $storage_master = MasterStorage::where("principal_id", $this->principal_id)->first();

        if ( isset($storage_master) ) {
            $cpu_storage = $qty_storage >= $storage_master->quota ? $qty_storage : $storage_master->quota;

            $amount_storage = $cpu_storage * $storage_master->cpu;
        } else {
            $amount_storage = 0;
        }

        $handling_master = MasterHandling::where("principal_id", $this->principal_id)->get();

        if ( isset($handling_master) ) {
            $inbound_handling = $handling_master->where("job_type", "IMP")->first();

            if ( isset($inbound_handling) ) {
                $cpu_inbound = $qty_inbound >= $inbound_handling->cpu_middle ? $qty_inbound : $inbound_handling->cpu_middle;

                $amount_inbound = $cpu_inbound * $inbound_handling->cpu;
            } else {
                $amount_inbound = 0;
            }

            $outbound_handling = $handling_master->where("job_type", "EXP")->first();

            if ( isset($outbound_handling) ) {
                $cpu_outbound = $qty_outbound >= $outbound_handling->cpu_middle ? $qty_outbound : $outbound_handling->cpu_middle;

                $amount_outbound = $cpu_outbound * $outbound_handling->cpu;
            } else {
                $amount_outbound = 0;
            }
        } else {
            $amount_inbound = 0;
            $amount_outbound = 0;
        }

        return new Collection($storage_list);
    }

    public function headings(): array
    {
        return [
            "Date",
            'Handling In',
            "Handling Out",
            "Storage",
        ];
    }
}
