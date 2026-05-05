<?php

namespace App\Exports;

// use Illuminate\Contracts\View\View;

use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
// use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportPickingExcel implements FromView
{
    protected $outbound_id;

    public function __construct($outbound_id)
    {
        $this->outbound_id = $outbound_id;
    }

    public function view(): View
    {

        $header = DB::table('iv_outbound_job')->where('id', $this->outbound_id)->first();

        $getName =  DB::table('iv_principal')->where('id', $header->principal_id)->value('principal_name');

        $pickingan = DB::table("iv_outbound_batch")
            ->select("serial_id", "product_code", "location_code",  "location_id", "qty", "job_no", 'lot_no', "product_id")
            ->where("outbound_id", $this->outbound_id)
            ->orderBy("location_code", 'ASC')
            ->get();
        $productID = $pickingan->pluck('product_id')->toArray();
        $locationID = $pickingan->pluck('location_id')->toArray();
        $lotNo = $pickingan->pluck('lot_no')->toArray();
        $transaction = DB::table('iv_stock_transaction')
            ->select('product_code', 'location_code', 'lot_no', 'job_type', 'qty', 'reference_no', 'created_at', 'job_date')
            ->orderBy('location_code', 'ASC')
            ->whereYear('job_date', date('Y'))
            ->whereIn('product_id', $productID)
            ->whereIn('location_id', $locationID)
            ->whereIn('lot_no', $lotNo)
            ->get();

        $grouped = $transaction->groupBy(function ($item) {
            return $item->product_code . '|' . $item->location_code . '|' . $item->lot_no;
        });

        $results = $grouped->map(function ($items) use ($header) {
            $stockAwal = $this->getStockAwal($items, $header);

            // Calculate 'pickingan' based on 'reference_no' and 'job_type' 'EXP'
            $pickingan = $items->where('reference_no', $header->job_no)
                ->where('job_type', 'EXP')
                ->sum('qty');

            return [
                'stockAwal' => $stockAwal,
                'pickingan' => $pickingan,
                'pcsan' => $stockAwal - $pickingan,
            ];
        });

        $filtered = [];
        foreach ($transaction->where('reference_no', $header->job_no) as $key => $value) {
            $filtered[$value->product_code . '|' . $value->location_code . '|' . $value->lot_no] = $results[$value->product_code . '|' . $value->location_code . '|' . $value->lot_no];
        }

        // Print results
        $data = [];
        foreach ($filtered as $keys => $values) {
            $productCode = explode('|', $keys)[0];
            $locationCode = explode('|', $keys)[1];
            $lotNo = explode('|', $keys)[2];
            $yangDiAmbil = $values['stockAwal'] - $values['pcsan'];
            $data[] = [
                'product_code' => $productCode,
                'location_code' => $locationCode,
                'lot_no' => $lotNo,
                'stockAwal' => $values['stockAwal'],
                'yangDiAmbil' => $yangDiAmbil,
                'stockAkhir' => $values['pcsan'],
            ];
        }
        $despatch = DB::table('iv_outbound_despatch')
            ->select('outbound_id', 'vehicle_no', 'store_id', 'size_id', 'container_no')
            ->where('outbound_id', $header->id)
            ->get();
        $despatch->map(function ($value) {
            $value->size = DB::table('iv_container_size')->where('id', $value->size_id)->value('size_name');
            $value->tujuan = DB::table('tm_store')->where('id', $value->store_id)->value('store_name');
            return $value;
        });
        $despatch = $despatch->first();

        return view("transaction.outbound.pallet_picking_report_excel", compact('header', 'data', 'getName', 'despatch'));
    }

    private function getStockAwal($items, $header)
    {
        $confirmDate = Carbon::parse($header->confirmed_date);
        $impQty = $items->whereIn('job_type', ['IMP', 'TFRI', 'ADJ+'])->sum('qty');
        $expQty = $items->filter(function ($value) use ($confirmDate, $header) {
            $created_at = Carbon::parse($value->created_at);
            return  $created_at < $confirmDate &&
                $value->reference_no != $header->job_no &&
                $value->job_type == 'EXP';
        })->sum('qty');
        return $impQty - $expQty;
    }
}
