<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class StockTransferImport implements ToCollection, WithHeadingRow
{
    public function  __construct($type)
    {
        $this->type = $type;
    }

    public function collection(Collection $collection)
    {
        $id_sku = [];
        foreach ($collection as $row) {
            $master = null;
            $job_date = intval($row['job_date']);

            $master = DB::table('iv_stock_ledger')
                ->where('product_code', $row['sku'])
                ->where('location_code', $row['location_from'])
                ->whereDate('job_date', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($job_date)->format('Y-m-d'))
                ->where('qtya', '>', 0)
                ->first();

            if ($master == null) {
                Session::flash('error', 'Ada SKU yang tidak sesuai dengan lokasi atau tanggal kedatangan, periksa kembali excel yang kamu upload..');
                return back();
            } else {
                if ($row['qty'] > $master->qtya) {
                    Session::flash('error', 'Qty Melebihi Stok Actual Yang Ada, Periksa Stock Report..');
                    return back();
                } else {
                    $id_sku[] = $master->id;
                }
            }
        }

        $job = \app\Models\Transaction\Transfer\Job::where('company_id', Auth::user()->company_id)
            ->whereYear('job_date', date('Y'))
            ->whereMonth('job_date', date('m'))
            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }
        $job_no = '3' . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        DB::table('iv_transfer_job')->insert([
            'company_id' => Auth::user()->company_id,
            'branch_id' => DB::table('sm_user_branch')->where('user_id', Auth::user()->id)->first()->branch_id ?? '0',
            'principal_id' =>  DB::table('users_principal')->where('user_id', Auth::user()->id)->first()->principal_id ?? '0',
            'job_no' => $job_no,
            'job_date' => date('Y-m-d H:i:s'),
            'description' => 'Transfer Stok By Excel Tanggal ' . date('Y-m-d H:i:s'),
            'entry_flag' => 'Yes',
            'entry_date' => date('Y-m-d H:i:s'),
            'confirmed_flag' => 'Yes',
            'confirmed_by' => Auth::user()->username,
            'confirmed_date' => date('Y-m-d H:i:s'),
            'user_id' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        //GET TRANSFER ID
        $transfer_id = DB::table('iv_transfer_job')
            ->orderBy('id', 'DESC')
            ->where('user_id', Auth::user()->id)
            ->first()->id;

        for ($i = 0; $i < count($id_sku); $i++) {
            $master = DB::table('iv_stock_ledger')
                ->where('id', $id_sku[$i])
                ->first();
            // dd($master);

            $stok_transfer = $master->qtya - $collection[$i]['qty'];

            $location_detail = DB::table('iv_location')
                ->where('location_code', $collection[$i]['location_to'])
                ->first();

            //insert to transfer stok
            DB::table('iv_transfer_detail')->insert([
                'company_id' => $master->company_id,
                'principal_id' => $master->principal_id,
                'transfer_id' => $transfer_id,
                'job_no' => $master->job_no,
                // 'serial_id' => $master->serial_id,
                'serial_no' => $master->serial_no,
                'product_id' => $master->product_id,
                'product_code' => $master->product_code,
                'po_number' => $master->po_number,
                'lot_no' => $master->lot_no,
                'document_ref' => $master->document_ref,
                'mfg_date' => $master->mfg_date,
                'exp_date' => $master->exp_date,
                'manufactur_id' => $master->manufactur_id,
                'status_id' => $master->status_id,
                'site_id' => $master->site_id,
                'area_id' => $master->area_id,
                'location_id' => $master->location_id,
                'location_code' => $master->location_code,
                'puom' => $master->puom,
                'muom' => $master->muom,
                'buom' => $master->buom,
                'uppp' => $master->uppp,
                'muppp' => $master->muppp,
                'pqty' => $master->pqty,
                'mqty' => $master->mqty,
                'bqty' => $master->bqty,
                'qty' => $collection[$i]['qty'],
                'actual_pqty' => $collection[$i]['qty'],
                'actual_mqty' => $collection[$i]['qty'],
                'actual_bqty' => $collection[$i]['qty'],
                'actual_qty' => $collection[$i]['qty'],
                'dest_site_id' => $master->site_id,
                'dest_area_id' => $master->area_id,
                'dest_location_id' => $location_detail->id,
                'dest_location_code' => $location_detail->location_code,
                'base_unit' => $master->base_unit,
                'pallet_qty' => $master->pallet_qty,
                'srno' => $master->serial_no,
                // 'entry_date' => date('Y-m-d H:i:s'),
                'picked_flag' => 'Yes',
                'picked_by' => Auth::user()->username,
                'picked_date' => date('Y-m-d H:i:s'),
                'confirmed_flag' => 'Yes',
                'confirmed_by' => Auth::user()->username,
                'confirmed_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            //insert dulu data baru di stock ledgernya
            DB::table('iv_stock_ledger')
                ->insert([
                    'branch_id' => $master->branch_id,
                    'company_id' => $master->company_id,
                    'principal_id' => $master->principal_id,
                    'serial_no' => $master->serial_no,
                    'srno' => $master->srno,
                    'job_no' => $master->job_no,
                    'job_date' => $master->job_date,
                    'vehicle_no' => $master->vehicle_no,
                    'line_no' => $master->line_no,
                    'product_id' => $master->product_id,
                    'product_code' => $master->product_code,
                    'po_number' => $master->po_number,
                    'lot_no' => $master->lot_no,
                    'document_ref' =>  $master->document_ref,
                    'mfg_date' => $master->mfg_date,
                    'exp_date' => $master->exp_date,
                    'manufactur_id' => $master->manufactur_id,
                    'status_id' => $master->status_id,
                    'site_id' => $master->site_id,
                    'area_id' => $master->area_id,
                    'location_id' => $location_detail->id,
                    'location_code' => $collection[$i]['location_to'],
                    'puom' => $master->puom,
                    'muom' => $master->muom,
                    'buom' => $master->buom,
                    'uppp' => $master->uppp,
                    'muppp' => $master->muppp,
                    'pqty' => $master->pqty,
                    'mqty' => $master->mqty,
                    'bqty' => $master->bqty,
                    'qtyr' => $master->qtyr,
                    'qtys' => $collection[$i]['qty'],
                    'qtyp' => 0,
                    'qtya' => $collection[$i]['qty'],
                    'pallet_qty' => $master->pallet_qty,
                    'base_unit' => $master->base_unit,
                    'reference_no' => $master->reference_no,
                    'freeze_flag' => $master->freeze_flag,
                    'freeze_by' =>   $master->freeze_by,
                    'freeze_date' => $master->freeze_date,
                    'freeze_reason' => $master->freeze_reason,
                    'user_id' => $master->user_id,
                    'created_at' => $master->created_at,
                    'updated_at' => $master->updated_at,
                    'status' => $collection[$i]['status'],
                ]);

            //update dulu master stok ledgernya
            DB::table('iv_stock_ledger')
                ->where('id', $id_sku[$i])
                ->update([
                    'qtya' => $stok_transfer,
                    'qtys' => $stok_transfer,
                    'status' => $collection[$i]['status'],
                ]);
        }
        Session::flash('success', 'Proses berhasil di lakukan..');
    }
}
