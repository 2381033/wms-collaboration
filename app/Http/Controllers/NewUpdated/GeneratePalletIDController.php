<?php

namespace App\Http\Controllers\NewUpdated;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Stock\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Session;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GeneratePalletIDController extends Controller
{
    public function index()
    {
        $site_arr = DB::table('users_site')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('site_id')
            ->toArray();

        $location = DB::table('iv_location')
            ->where('active', 'yes')
            ->whereIn('site_id', $site_arr)
            ->get();

        // $sku = Ledger::whereDate('job_date', date('Y-m-d', strtotime("-60 days")))
        $sku = Ledger::whereIn('site_id', $site_arr)
            ->orderBy('product_code', 'ASC')
            ->where('qtya', '!=', 0)
            ->groupBy('product_code')
            ->get();
        // $sku = Ledger::whereDate('job_date', date('Y-m-d', strtotime("-60 days")))
        //     ->whereIn('site_id', $site_arr)
        //     ->groupBy('product_code')
        //     ->get();

        return view("new.generatePalletID.index", compact('sku', 'location'));
    }

    public function postGenerate(Request $request)
    {
        $job = DB::table('iv_stock_ledger_qrcode')
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->max("job_no");
        $type = $request->type_generate;

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }
        $job_no = date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');
        $qrcode = Str::random(8);
        
        if($type == 'lama'){
            for ($i = 0; $i < count($request->product_code); $i++) {
                $product_code_request = explode(',', $request->product_code[$i])[0];
                $id_stok_request      = explode(',', $request->product_code[$i])[1];

                $master_ledger = DB::table('iv_stock_ledger')->where('id', $id_stok_request)->first();
                $stok_aktual = $master_ledger->qtya;

                $stok_transfer   = $stok_aktual - $request->qty[$i];
                if($request->qty[$i] > $stok_aktual){
                    Session::flash('error', 'Qty Melebihi Stok Yang Ada');
                    return back();
                }else{
                    if($stok_transfer == 0){
                    //update master ledger
                     DB::table('iv_stock_ledger')
                        ->where('id', $id_stok_request)
                        ->update([
                            'location_id'   => null,
                            'location_code' => $request->location_code,
                            'qrcode'        => $qrcode,
                        ]);

                        //add to historycal
                        DB::table('iv_stock_ledger_qrcode')
                        ->insert([
                            'job_no' => $job_no,
                            'id_stok' => $id_stok_request,
                            'product_code' => $product_code_request,
                            'type' => 'in',
                            'qrcode' => $qrcode,
                            'qty' => $request->qty[$i],
                            'location_code_from' => $request->location_code,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->name
                        ]);
                    }else{
                        DB::table('iv_stock_ledger')
                        ->insert([
                            'branch_id' => $master_ledger->branch_id,
                            'company_id' => $master_ledger->company_id,
                            'principal_id' => $master_ledger->principal_id,
                            'serial_no' => $master_ledger->serial_no,
                            'srno' => $master_ledger->srno,
                            'job_no' => $master_ledger->job_no,
                            'job_date' => date("Y-m-d H:i:s"),
                            'vehicle_no' => $master_ledger->vehicle_no,
                            'line_no' => $master_ledger->line_no,
                            'product_id' => $master_ledger->product_id,
                            'product_code' => $master_ledger->product_code,
                            'po_number' => $master_ledger->po_number,
                            'lot_no' => $master_ledger->lot_no,
                            'document_ref' =>  $master_ledger->document_ref,
                            'mfg_date' => $master_ledger->mfg_date,
                            'exp_date' => $master_ledger->exp_date,
                            'manufactur_id' => $master_ledger->manufactur_id,
                            'status_id' => $master_ledger->status_id,
                            'site_id' => $master_ledger->site_id,
                            'area_id' => $master_ledger->area_id,
                            'location_id' => null,
                            'location_code' => $request->location_code,
                            'puom' => $master_ledger->puom,
                            'muom' => $master_ledger->muom,
                            'buom' => $master_ledger->buom,
                            'uppp' => $master_ledger->uppp,
                            'muppp' => $master_ledger->muppp,
                            'pqty' => $master_ledger->pqty,
                            'mqty' => $master_ledger->mqty,
                            'bqty' => $master_ledger->bqty,
                            'qtyr' => $master_ledger->qtyr,
                            'qtys' => $request->qty[$i],
                            'qtyp' => $master_ledger->qtyp,
                            'qtya' => $request->qty[$i],
                            'pallet_qty' => $master_ledger->pallet_qty,
                            'base_unit' => $master_ledger->base_unit,
                            'reference_no' => $master_ledger->reference_no,
                            'freeze_flag' => $master_ledger->freeze_flag,
                            'freeze_by' =>   $master_ledger->freeze_by,
                            'freeze_date' => $master_ledger->freeze_date,
                            'freeze_reason' => $master_ledger->freeze_reason,
                            'user_id' => $master_ledger->user_id,
                            'created_at' => $master_ledger->created_at,
                            'updated_at' => $master_ledger->updated_at,
                            'qrcode' => $qrcode,
                        ]);
    
                    DB::table('iv_stock_ledger')->where('id', $id_stok_request)
                        ->update([
                            'qtys' => $stok_transfer,
                            'qtya' => $stok_transfer,
                        ]);

                        //add to historycal
                        DB::table('iv_stock_ledger_qrcode')
                        ->insert([
                            'job_no' => $job_no,
                            'id_stok' => $id_stok_request,
                            'product_code' => $product_code_request,
                            'type' => 'in',
                            'qrcode' => $qrcode,
                            'qty' => $request->qty[$i],
                            'location_code_from' => $request->location_code,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->name
                        ]);
                    }
                }
            }
        }else{

        }
        return redirect('inventory/generatePalletID/print/' . Crypt::encrypt($job_no));
    }

    public function postSKUParsial(Request $request)
    {
        $master = DB::table('iv_stock_ledger_qrcode')->where('job_no', $request->job_no)->first();

        $job = DB::table('iv_stock_ledger_qrcode')
        ->whereYear("created_at", date('Y'))
        ->whereMonth("created_at", date('m'))
        ->max("job_no");
        $type = $request->type_generate;
        
        if($type == 'lama'){
            for ($i = 0; $i < count($request->product_code); $i++) {
                $product_code_request = explode(',', $request->product_code[$i])[0];
                $id_stok_request      = explode(',', $request->product_code[$i])[1];

                $master_ledger = DB::table('iv_stock_ledger')->where('id', $id_stok_request)->first();
                $stok_aktual = $master_ledger->qtya;

                $stok_transfer   = $stok_aktual - $request->qty[$i];
                if($request->qty[$i] > $stok_aktual){
                    Session::flash('error', 'Qty Melebihi Stok Yang Ada');
                    return back();
                }else{
                    if($stok_transfer == 0){
                    //update master ledger
                    DB::table('iv_stock_ledger')
                        ->where('id', $id_stok_request)
                        ->update([
                            'location_id'   => null,
                            'location_code' => $request->location_code,
                            'qrcode'        => $master->qrcode,
                        ]);

                        //add to historycal
                        DB::table('iv_stock_ledger_qrcode')
                        ->insert([
                            'job_no' => $master->job_no,
                            'id_stok' => $id_stok_request,
                            'product_code' => $product_code_request,
                            'type' => 'in',
                            'qrcode' => $master->qrcode,
                            'qty' => $request->qty[$i],
                            'location_code_from' => $master->location_code_from,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->name
                        ]);
                    }else{
                        DB::table('iv_stock_ledger')
                        ->insert([
                            'branch_id' => $master_ledger->branch_id,
                            'company_id' => $master_ledger->company_id,
                            'principal_id' => $master_ledger->principal_id,
                            'serial_no' => $master_ledger->serial_no,
                            'srno' => $master_ledger->srno,
                            'job_no' => $master_ledger->job_no,
                            'job_date' => date("Y-m-d H:i:s"),
                            'vehicle_no' => $master_ledger->vehicle_no,
                            'line_no' => $master_ledger->line_no,
                            'product_id' => $master_ledger->product_id,
                            'product_code' => $master_ledger->product_code,
                            'po_number' => $master_ledger->po_number,
                            'lot_no' => $master_ledger->lot_no,
                            'document_ref' =>  $master_ledger->document_ref,
                            'mfg_date' => $master_ledger->mfg_date,
                            'exp_date' => $master_ledger->exp_date,
                            'manufactur_id' => $master_ledger->manufactur_id,
                            'status_id' => $master_ledger->status_id,
                            'site_id' => $master_ledger->site_id,
                            'area_id' => $master_ledger->area_id,
                            'location_id' => $master_ledger->location_id,
                            'location_code' => $request->location_to,
                            'puom' => $master_ledger->puom,
                            'muom' => $master_ledger->muom,
                            'buom' => $master_ledger->buom,
                            'uppp' => $master_ledger->uppp,
                            'muppp' => $master_ledger->muppp,
                            'pqty' => $master_ledger->pqty,
                            'mqty' => $master_ledger->mqty,
                            'bqty' => $master_ledger->bqty,
                            'qtyr' => $master_ledger->qtyr,
                            'qtys' => $request->qty[$i],
                            'qtyp' => $master_ledger->qtyp,
                            'qtya' => $request->qty[$i],
                            'pallet_qty' => $master_ledger->pallet_qty,
                            'base_unit' => $master_ledger->base_unit,
                            'reference_no' => $master_ledger->reference_no,
                            'freeze_flag' => $master_ledger->freeze_flag,
                            'freeze_by' =>   $master_ledger->freeze_by,
                            'freeze_date' => $master_ledger->freeze_date,
                            'freeze_reason' => $master_ledger->freeze_reason,
                            'user_id' => $master_ledger->user_id,
                            'created_at' => $master_ledger->created_at,
                            'updated_at' => $master_ledger->updated_at,
                            'qrcode' => $master->qrcode,
                        ]);

                    DB::table('iv_stock_ledger')->where('id', $id_stok_request)
                        ->update([
                            'qtys' => $stok_transfer,
                            'qtya' => $stok_transfer,
                        ]);

                        //add to historycal
                        DB::table('iv_stock_ledger_qrcode')
                        ->insert([
                            'job_no' => $master->job_no,
                            'id_stok' => $id_stok_request,
                            'product_code' => $product_code_request,
                            'type' => 'in',
                            'qrcode' => $master->qrcode,
                            'qty' => $request->qty[$i],
                            'location_code_from' => $master->location_code_from,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->name
                        ]);
                    }
                }
            }
        }else{

        }
        Session::flash('success', 'Data has been generated successfully');
        return back();
    }

    public function print($job_no)
    {
        $job_no = Crypt::decrypt($job_no);
        $data =  DB::table('iv_stock_ledger_qrcode')->where('job_no', $job_no)->get();
        $qr = QrCode::size(130)->generate($data[0]->qrcode);

        $id_stok = $data->pluck('id_stok')->toArray();
        $list_sku =  DB::table('iv_stock_ledger')->whereIn('id', $id_stok)->get();
        $list_sku->map(function ($value) {
            $value->principal = DB::table('iv_principal')->where('id', $value->principal_id)->first()->principal_name ?? '-';
            $value->product_name = DB::table('iv_product')->where('id', $value->product_id)->first()->product_name ?? '-';
            return $value;
        });
        // dd($list_sku);

        return view("new.generatePalletID.print", compact('data', 'qr', 'list_sku'));
    }


    public function masterData()
    {
        $data =  DB::table('iv_stock_ledger_qrcode')->groupBy('job_no')->get();
        $site_arr = DB::table('users_site')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('site_id')
            ->toArray();

        $location = DB::table('iv_location')
            ->where('active', 'yes')
            ->whereIn('site_id', $site_arr)
            ->get();


        // $sku = Ledger::whereDate('job_date', date('Y-m-d', strtotime("-60 days")))
        $sku = Ledger::whereIn('site_id', $site_arr)
            ->orderBy('product_code', 'ASC')
            ->groupBy('product_code')
            ->get();

        return view("new.generatePalletID.master_data", compact('data', 'sku'));
    }

    public function deleteSKU($id)
    {
        DB::table('iv_stock_ledger_qrcode')
            ->where('id', $id)
            ->delete();

        Session::flash('success', 'Data has been deleted successfully');
        return back();
    }

    public function showListSKU($job_no)
    {
        $data =  DB::table('iv_stock_ledger_qrcode')->where('job_no', $job_no)->get();
        $data->map(function ($value) {
            $value->tgl_dibuat = explode(' ', $value->created_at)[0];
            $value->now = date('Y-m-d');
        });

        return response()->json([
            'data' => $data,
        ]);
    }

    public function encryptqr($job_no)
    {
        $job_no = Crypt::encrypt($job_no);
        return redirect('inventory/generatePalletID/print/' . $job_no);
    }

    public function scan()
    {
        return view("new.generatePalletID.scan");
    }

    public function doScan($qr)
    {
        $data = DB::table('iv_stock_ledger_qrcode')
        ->orderBy('id', 'ASC')
        ->where('qrcode', $qr)
        ->get();
        $data->map(function ($value){
            $value->stok = DB::table('iv_stock_ledger')->where('id', $value->id_stok)->first()->qtya ?? '-';
        });

        return response()->json([
            'data' => $data,
        ]);
    }

    public function dispatchJob()
    {
        return view("new.generatePalletID.dispatch");
    }

    public function getDispatchSKU($qr)
    {
        $data = DB::table('iv_stock_ledger_qrcode')
                ->where('qrcode', $qr)
                ->where('qty', '!=', 0)
                ->get();
        $data->map(function ($value){
            $value->stok = DB::table('iv_stock_ledger')->where('id', $value->id_stok)->first()->qtya ?? '-';
        });

        return response()->json([
            'data' => $data,
        ]);
    }

    public function postDispatch(Request $request)
    {
        for ($i = 0; $i < count($request->qty); $i++) {
            $masterqr = DB::table('iv_stock_ledger_qrcode')
                        ->where('qrcode', $request->qrcode[$i])
                        ->where('id', $request->id[$i])
                        ->first();
            $validasi = $masterqr->qty;

            $master = DB::table('iv_stock_ledger')
                        ->where('id', $masterqr->id_stok)
                        ->first();

            if ($request->qty[$i] > $validasi) {
                Session::flash('error', 'Inputan Melebihi Stok Yang Ada di Pallet..');
                return back();
            } else {
                $stok_aktual = $master->qtya - $request->qty[$i];

                //update qty in stock ledger
                DB::table('iv_stock_ledger')
                ->where('id', $masterqr->id_stok)
                ->update([
                    'qtys' =>  $stok_aktual,
                    'qtya' =>  $stok_aktual,
                ]);

                //insert to table qrcode
                DB::table('iv_stock_ledger_qrcode')
                ->insert([
                    'id_stok'            => $masterqr->id_stok,
                    'type'               => 'out',
                    'job_no'             => $masterqr->job_no,
                    'product_code'       => $masterqr->product_code,
                    'location_code_from' => $masterqr->location_code_from,
                    'qrcode'             => $masterqr->qrcode,
                    'created_at' => $masterqr->created_at,
                    'created_by' => $masterqr->created_by,
                    'dispatch_qty' => $request->qty[$i],
                    'dispatch_at'  => date('Y-m-d H:i:s'),
                    'dispatch_by'  => Auth::user()->name,
                ]);

                //update status jika stok aktual sama dengan 0
                if($stok_aktual == 0){
                    //update qty in ledger qrcode
                    DB::table('iv_stock_ledger_qrcode')
                        ->where('id_stok', $masterqr->id_stok)
                        ->update([
                            'status'       => 0,
                        ]);
                }
            }
        }
        Session::flash('success', 'Your Dispatch been successfully..');
        return back();
    }

    public function typeGenerate($value){
        $site_arr = DB::table('users_site')
        ->where('user_id', Auth::user()->id)
        ->get()->pluck('site_id')
        ->toArray();

        if($value == 'lama'){
            $data = Ledger::whereIn('site_id', $site_arr)
                ->orderBy('location_code', 'ASC')
                ->where('qtya', '!=', 0)
                ->where('location_code', '!=', 'FLOOR-1')
                ->whereNull('qrcode')
                ->get();

            return response()->json([
                'data' => $data
            ]);
        }else{

        }
    }

    public function checkidMaster($id){
        $data = DB::table('iv_stock_ledger_qrcode')
                    ->where('id', $id)
                    ->get();
       echo $data;
    }

    public function cariData($tgl_mulai, $tgl_selesai){
        dd($tgl_mulai, $tgl_selesai);
    }
}
