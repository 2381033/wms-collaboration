<?php

namespace App\Exports;

// use Illuminate\Contracts\View\View;

use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
// use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PickingReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $principalId;
    protected $startDate;
    protected $endDate;

    public function __construct($principalId, $startDate, $endDate)
    {
        $this->principalId = $principalId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // ini_set('memory_limit', '2048M');
        $outboundJobList = DB::table('iv_outbound_job')->where('principal_id', $this->principalId)->whereBetween("job_date", [$this->startDate, $this->endDate])->where('confirmed_flag', 'YES')->get();
        $resultList = array();
        foreach ($outboundJobList as $keyList => $valueList) {
            $header = DB::table('iv_outbound_job')->where('id', $valueList->id)->first();
            $getName =  DB::table('iv_principal')->where('id', $header->principal_id)->value('principal_name');
            $pickingan = DB::table("iv_outbound_batch")
                ->select("serial_id", "product_code", "location_code",  "location_id", "qty", "job_no", 'lot_no', "product_id")
                ->where("outbound_id", $valueList->id)
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
            $stockAwalAll = 0;
            $yangDiambilAll = 0;
            $stockAkhirAll = 0;
            $fullPalletAll = 0;
            foreach ($filtered as $keys => $values) {
                $yangDiAmbil = $values['stockAwal'] - $values['pcsan'];
                $stockAwalAll += $values['stockAwal'];
                $yangDiambilAll += $yangDiAmbil;
                $stockAkhirAll += $values['pcsan'];
                if ($values['pcsan'] == 0) {
                    $fullPalletAll++;
                }
            }
            $despatch = DB::table('iv_outbound_despatch')
                ->select('outbound_id', 'vehicle_no', 'store_id', 'size_id', 'container_no')
                ->where('outbound_id', $header->id)
                ->get();
            $despatch->map(function ($value) {
                $value->size = DB::table('iv_container_size')->where('id', $value->size_id)->value('size_name');
                $value->tujuan = DB::table('tm_store')->where('id', $value->store_id)->value('store_name');
                // BY ARI RIZKITA
                $value->city_code = DB::table('tm_store')->where('id', $value->store_id)->value('city_code');
                // BATAS
                return $value;
            });
            $despatch = $despatch->first();
            $confirmDate = date("d/m/Y", strtotime($header->confirmed_date));
            $hasil = array(
                'job_no' => $header->job_no,
                'stock' => $stockAwalAll,
                'book_pick' => $yangDiambilAll,
                'remain' => $stockAkhirAll,
                'full_pallet' => $fullPalletAll,
                'vehicle_no' => $despatch->vehicle_no,
                'vehicle_type' => $despatch->size,
                'destination' => $despatch->tujuan,
                // BY ARI RIZKITA
                'city_code' => $despatch->city_code,
                // BATAS
                'container_no' => $despatch->container_no,
                // 'confirm_date' => $header->confirmed_date
                'confirm_date' => $confirmDate
            );
            array_push($resultList, $hasil);
        }
        // dd($resultList);

        $listData = [];
        $no = 1;
        foreach ($resultList as $value) {
            $destination = $value['destination'] . "-" . $value['city_code'];
            $listData[] = [
                "No" => $no++,
                "Job No" => $value['job_no'],
                "Stock" => $value['stock'],
                "book_pick" => $value['book_pick'],
                "Remain" => $value['remain'],
                "Full Pallet" => $value['full_pallet'],
                "Vehicle No" => $value['vehicle_no'],
                "Vehicle Type" => $value['vehicle_type'],
                "Destination" => $destination,
                "Container No" => $value['container_no'],
                "Confirm Date" => $value['confirm_date']
            ];
        }
        // dd($listData);
        return new Collection($listData);
        // return view("report.picking-transaction.picking-report-excel", compact('header', 'resultList', 'data', 'getName', 'despatch'));
    }

    public function headings(): array
    {
        $header = [
            "No",
            "Job No",
            "Stock",
            "Book Pick",
            'Remain',
            "Full Pallet",
            "Vehicle No",
            "Vehicle Type",
            "Destination",
            "Container No",
            "Confirm Date",
        ];

        return $header;
    }

    private function getStockAwal($items, $header)
    {

        $confirmDate = Carbon::parse($header->confirmed_date);
        // dd($header->confirmed_date);
        $impQty = $items->whereIn('job_type', ['IMP', 'TFRI', 'ADJ+'])->sum('qty');
        // dd($impQty);
        $expQty = $items->filter(function ($value) use ($confirmDate, $header) {
            $created_at = Carbon::parse($value->created_at);
            // dd($confirmDate);

            return  $created_at < $confirmDate &&
                $value->reference_no != $header->job_no &&
                $value->job_type == 'EXP';
        })->sum('qty');
        return $impQty - $expQty;
        // dd($expQty);
    }
}
