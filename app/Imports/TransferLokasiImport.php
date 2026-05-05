<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Transfer\Job as TransferJob;
use App\Models\Transaction\Transfer\Detail as TransferDetail;

class TransferLokasiImport implements ToCollection, WithHeadingRow
{
    protected $job_id = null;
    public function __construct($job_id)
    {
        $this->job_id = $job_id;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            try {
                foreach ($rows as $val) {
                    if (is_null($val['id']) || $val['id'] == "") {
                        DB::rollBack();
                        Session::flash('error', 'Column ID tidak boleh kosong, periksa kembali file excel');
                        return back();
                    }
                }
                foreach ($rows as $val) {
                    $stock = StockLedger::find($val['id']);
                    if (!is_null($stock)) {
                        if ($stock->product_code != $val['product_code']) {
                            DB::rollBack();
                            Session::flash('error', 'ID ' .  $val['id'] . ' In Stock: ' . $stock->product_code .  ' -> In Excel: ' . $val['product_code'] . ' Periksa kembali file excel');
                            return back();
                        }
                    } else {
                        Session::flash('error', 'Column ID ' .  $val['id'] . ' Tidak Ditemukan, Periksa kembali file excel');
                        return back();
                    }
                }
                foreach ($rows as $val) {
                    if (is_null($val['location_code_to']) || is_null($val['qty_move'])) {
                        DB::rollBack();
                        Session::flash('error', 'Column Location Code to Atau Qty Move tidak boleh kosong..');
                        return back();
                    }
                }
                foreach ($rows as $val) {
                    $stock = StockLedger::find($val['id']);
                    if ($val['qty_move'] > $stock->qtya) {
                        DB::rollBack();
                        Session::flash('error', 'Stock ' . $stock->product_code . ' in system: '  . $stock->qtya . 'CTN ->  in excel: ' .  $val['qty_move'] . 'CTN');
                        return back();
                    }
                }
                foreach ($rows as $val) {
                    $site[] = DB::table('iv_site')->where('site_name', $val['site'])->count();
                    if ($site == 0) {
                        DB::rollBack();
                        Session::flash('error', 'Site Tidak di temukan, silahkan periksa kembali file excel');
                        return back();
                    }
                }
                foreach ($rows as $val) {
                    $stock = StockLedger::find($val['id']);
                    $site_id = DB::table('iv_site')->where('site_name', $val['site'])->value('id');
                    $destLocation = DB::table('iv_location')
                        ->where('site_id', $site_id)
                        ->where('location_code', $val['location_code_to'])
                        ->first();
                    if (is_null($destLocation)) {
                        DB::rollBack();
                        Session::flash('error', 'Location Code ' . $val['location_code_to'] . ' Tidak di temukan, silahkan periksa kembali file excel');
                        return back();
                    }

                    TransferDetail::create([
                        'company_id' => $stock->company_id,
                        'principal_id' => $stock->principal_id,
                        'transfer_id' => $this->job_id,
                        'job_no' => $stock->job_no,
                        'serial_id' => $stock->id,
                        'serial_no' => $stock->serial_no,
                        'product_id' => $stock->product_id,
                        'product_code' => $stock->product_code,
                        'po_number' => $stock->po_number,
                        'lot_no' => $stock->lot_no,
                        'document_ref' => $stock->document_ref,
                        'mfg_date' => $stock->mfg_date,
                        'exp_date' => $stock->exp_date,
                        'manufactur_id' => $stock->manufactur_id,
                        'status_id' => $stock->status_id,
                        'site_id' => $stock->site_id,
                        'area_id' => $stock->area_id,
                        'location_id' => $stock->location_id,
                        'location_code' => $stock->location_code,
                        'puom' => $stock->puom,
                        'muom' => $stock->muom,
                        'buom' => $stock->buom,
                        'uppp' => $stock->uppp,
                        'muppp' => $stock->muppp,
                        'pqty' => $stock->pqty,
                        'mqty' => $stock->mqty,
                        'bqty' => $stock->bqty,
                        'qty' => $val['qty_move'],
                        'actual_pqty' => $val['qty_move'],
                        'actual_mqty' => 0,
                        'actual_bqty' => 0,
                        'actual_qty' => $val['qty_move'],
                        'dest_site_id' => $destLocation->site_id,
                        'dest_area_id' => $destLocation->area_id,
                        'dest_location_id' => $destLocation->id,
                        'dest_location_code' => $destLocation->location_code,
                        'base_unit' => $stock->base_unit,
                        'pallet_qty' => $stock->pallet_qty,
                        'srno' => $stock->serial_no,
                        'entry_date' => date('Y-m-d H:i:s'),
                        // 'status' => $request->product_status
                    ]);
                    $job = TransferJob::find($this->job_id);
                    $job->entry_flag = 'Yes';
                    $job->entry_by = Auth::user()->username;
                    $job->entry_date = \Carbon\Carbon::now();
                    $job->save();
                }
                DB::commit();
                Session::flash('success', 'Good Job, Data has been saved successfully..');
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('error', $e->getMessage() . ' -> Netwok Connection Failed ');
            }
        });
        return back();
    }
}
