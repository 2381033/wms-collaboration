<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Inbound\Job as inboundJob;
use App\Models\Transaction\Inbound\Batch as inboundBatch;
use Illuminate\Support\Facades\Session;
use App\Models\Transaction\Stock\Ledger as stockLedger;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Yajra\DataTables\Facades\DataTables;

class PutawayController extends Controller
{
    public function __construct()
    {
        if (!GlobalHelpers::checkLogin()) {
            return response()->redirectTo("login");
        }
    }

    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            // $list_data = \App\Models\Transaction\Inbound\Detail::from('iv_inbound_detail as a')
            //                 ->select('a.*', 'b.product_name')
            //                 ->join('iv_product as b', 'a.product_id', 'b.id')
            //                 ->where('a.company_id', $company_id)
            //                 ->where('a.inbound_id', $request->inbound_id)
            //                 ->where('a.received_flag', 'Yes')
            //                 ->where('a.putaway_flag', 'No')
            //                 ->get();

            $batch = DB::table('iv_inbound_batch')
                ->where('inbound_id', $request->inbound_id)
                ->count();

            if ($batch > 0) {
                $list_data = DB::table('iv_inbound_per_pallet')
                    ->where('inbound_id', $request->inbound_id)
                    ->whereNull('location_code')
                    ->get();
            } else {
                $list_data = DB::table('iv_inbound_per_pallet')
                    ->where('inbound_id', $request->inbound_id)
                    ->get();
            }


            $list_data->map(function ($value) {
                $value->master_product = DB::table('iv_product')
                    ->where('product_code', $value->product_code)
                    ->first();

                $value->master_detail = DB::table('iv_inbound_detail')
                    ->where('product_code', $value->product_code)
                    ->where('inbound_id', $value->inbound_id)
                    ->first();
            });

            return datatables()->of($list_data)
                // ->editColumn('exp_date', function ($data) 
                // {
                //     $exp_date = "";
                //     if (isset($data->master_detail->exp_date)) {
                //         $exp_date = date('d/m/Y', strtotime($data->master_detail->exp_date) );
                //     }
                //     return $exp_date;
                // })
                // ->editColumn('mfg_date', function ($data) 
                // {
                //     $mfg_date = "";
                //     if (isset($data->master_detail->mfg_date)) {
                //         $mfg_date = date('d/m/Y', strtotime($data->master_detail->mfg_date) );
                //     }
                //     return $mfg_date;
                // })
                // ->addColumn('check', function ($data) {
                //     return '<input type="checkbox" required="required" name="putaway_id[]" class="putaway-check" id="' . $data->id . '" value="' . $data->id . '">';
                // })
                // ->rawColumns(['check'])
                // ->addIndexColumn()       
                ->make(true);
        }
    }

    public function submit(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $user_id = Auth::user()->id;
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            try {
                $data = $request->product_code;
                $req_site = $request->site_putaway;
                $req_area = $request->area_putaway;
                // $req_from = $request->location_from;
                // $req_to = $request->location_to;
                // $req_mixed = $request->mixed_pallet;
                $manual_putaway = $request->manual_putaway ?? 'Yes';
                // dd($manual_putaway);

                for ($i = 0; $i < count($request->product_code); $i++) {
                    $detail = DB::table('iv_inbound_detail')
                        ->where('id', $request->packing_id[$i])
                        ->where('inbound_id', $request->inbound_id)
                        ->where('product_code', $request->product_code[$i])
                        ->first();

                    $detail_location = DB::table('iv_location')
                        ->where('id', $request->location_id[$i])
                        ->first();

                    $detail_pallet = DB::table('iv_inbound_per_pallet')
                        ->where('inbound_id', $request->inbound_id)
                        ->where('product_code', $request->product_code[$i])
                        ->where('location_id', $request->location_id[$i])
                        ->where('picking_id', $request->packing_id[$i])
                        ->first();

                    $job = inboundJob::find($request->inbound_id);

                    // $principal = \App\Models\Master\Principal::find($detail->principal_id);
                    // $product = \App\Models\Master\Product::find($detail->product_id);

                    // DB::table('iv_inbound_detail')
                    //     ->where('inbound_id', $request->inbound_id)
                    //     ->where('product_code', $request->product_code[$i])
                    //     ->update([
                    //         'site_id'       => $req_site,
                    //         'area_id'       => $req_area,
                    //         'location_from' => $req_from,
                    //         'location_to'   => $req_to,
                    //         'mixed_pallet'  => $req_mixed,
                    //     ]);

                    $serial_no = $this->serialNumber($detail->company_id, $detail->principal_id);

                    $pqty = ($request->qty[$i]  - ($request->qty[$i] % $detail->uppp)) / $detail->uppp;
                    $mqty = (($detail->mqty % $detail->uppp) - (($detail->mqty % $detail->uppp) % $detail->muppp)) / $detail->muppp;
                    $bqty = $detail->bqty % $detail->uppp % $detail->muppp;

                    $inbound_batchin = [];

                    $inbound_batchin[] = [
                        'inbound_id' => $detail->inbound_id,
                        'packing_id' => $detail->id,
                        'company_id' => $detail->company_id,
                        'principal_id' => $detail->principal_id,
                        'serial_no' => $serial_no,
                        'job_no' => $detail->job_no,
                        'vehicle_no' => $detail->vehicle_no,
                        'product_id' => $detail->product_id,
                        'product_code' => $detail->product_code,
                        'po_number' => $detail->po_number,
                        'lot_no' => $detail->lot_no,
                        'document_ref' => $detail->document_ref,
                        'mfg_date' => $detail->mfg_date,
                        'exp_date' => $detail->exp_date,
                        'manufactur_id' => $detail->manufactur_id,
                        'status_id' => $detail->status_id,
                        'site_id' => $req_site,
                        'area_id' => $req_area,
                        'location_id' => $detail_location->id,
                        'location_code' => $request->location_code[$i],
                        'pallet_id' => $detail->pallet_id,
                        'puom' => $detail->puom,
                        'muom' => $detail->muom,
                        'buom' => $detail->buom,
                        'uppp' => $detail->uppp,
                        'muppp' => $detail->muppp,
                        'pqty' => $pqty,
                        'mqty' => $mqty,
                        'bqty' => $bqty,
                        'qty' => $request->qty[$i],
                        'remarks' => $detail_pallet->remarks ?? NULL,
                        'descrepancy_qty' => $detail_pallet->location_status == 'B' ? $detail_pallet->qty_per_pallet : 0,
                        'pallet_qty' => $request->qty[$i],
                        'base_unit' => $detail->base_unit,
                        'product_status' => $detail->product_status,
                        'manual_putaway' => $manual_putaway,
                        'created_at' => \Carbon\Carbon::now()
                    ];

                    inboundBatch::insert($inbound_batchin);

                    $job->allocated_flag = 'Yes';
                    $job->allocated_by = $confirmed_by;
                    $job->allocated_date = $confirmed_date;
                    $job->save();

                    DB::table('iv_inbound_detail')
                        ->where('inbound_id', $request->inbound_id)
                        ->where('product_code', $request->product_code[$i])
                        ->update([
                            'putaway_flag' => 'Yes',
                            'putaway_by' => $confirmed_by,
                            'putaway_date' => $confirmed_date,
                            'manual_putaway' => $manual_putaway,
                        ]);

                    DB::table('iv_inbound_per_pallet')
                        ->where('inbound_id', $request->inbound_id)
                        ->where('product_code', $request->product_code[$i])
                        ->update([
                            'putaway_by' => $confirmed_by,
                            'putaway_date' => $confirmed_date,
                        ]);

                    // $detail->putaway_flag = 'Yes';
                    // $detail->putaway_by = $confirmed_by;
                    // $detail->putaway_date = $confirmed_date;
                    // $detail->manual_putaway = $manual_putaway;
                    // $detail->save();

                    // $detail->site_id = $req_site;
                    // $detail->area_id = $req_area;
                    // $detail->location_from = $req_from;
                    // $detail->location_to = $req_to;
                    // $detail->mixed_pallet = $req_mixed;
                    // $detail->save();

                    // if ($principal->site()->count() == 0) {
                    //     DB::rollBack();
                    //     $message = ['error'=>'Principal site not define!!!'];

                    //     return $message;
                    // }

                    // $job_class = $job->job_class->class_name;

                    // $site_id = $detail->site_id;

                    // if ( isset($site_id) && !empty($site_id) ) {
                    //     $site = DB::table("iv_site as a")
                    //                 ->select("a.*", "b.type_name")
                    //                 ->leftjoin("iv_site_type as b", "a.type_id", "b.id")
                    //                 ->join('users_site as c', 'a.id', 'c.site_id')
                    //                 ->where('c.user_id', $user_id)
                    //                 ->where("a.id", $site_id)
                    //                 ->first();

                    //     if ( $site->type_name == "Bulk" ) {
                    //         $process_class = "Cross Dock";
                    //     } else {                            
                    //         $process_class = $job_class;
                    //     }
                    // } else {
                    //     $process_class = $job_class;
                    // }

                    // if ( $manual_putaway == "No" && $detail->pallet_id > 0 ) {
                    //     DB::rollBack();
                    //     $message = ['error'=>'Otomatic cannot running, because pallet id must be 0 ( Zero )!!!'];

                    //     return $message;
                    // }

                    // if ( $process_class == "Cross Dock" ) {
                    //     $serial_no = $this->serialNumber($detail->company_id, $detail->principal_id);

                    //     $actual_qty = $detail->actual_qty;

                    //     $summary_qty = 0;
                    //     while ($summary_qty < $actual_qty) {    
                    //         if ( $principal->pallet_capacity_general  == "Yes" ) {
                    //             $site = \App\Models\Master\Site::find($site_id);

                    //             $pallet_unit_count = \App\Models\Master\PalletUnit::where('company_id', $detail->company_id)
                    //                                 ->where('principal_id', $detail->principal_id)
                    //                                 ->where('product_id', $detail->product_id)
                    //                                 ->where('type_id', $site->location_id)
                    //                                 ->where('pallet_qty', '>', 0)
                    //                                 ->count();

                    //             if ($pallet_unit_count == 0) {
                    //                 DB::rollBack();
                    //                 $message = ['error'=>'Pallet unit not define!!!', 'code'=>'pallet', 'product' => $product];

                    //                 return $message;
                    //             }

                    //             $pallet_unit = \App\Models\Master\PalletUnit::select('base_qty')
                    //                         ->where('company_id', $detail->company_id)
                    //                         ->where('principal_id', $detail->principal_id)
                    //                         ->where('product_id', $detail->product_id)
                    //                         ->where('base_qty', '>', 0)
                    //                         ->first();

                    //             $base_qty = $pallet_unit->base_qty;
                    //         } else {
                    //             $base_qty = $detail->actual_qty;
                    //         }

                    //         $summary_qty = $summary_qty + $base_qty;  

                    //         if ($summary_qty <= $actual_qty) {
                    //             $qty = $base_qty;
                    //         } else {
                    //             $summary_qty = $summary_qty - $base_qty;
                    //             $qty = $actual_qty - $summary_qty;
                    //             $summary_qty = $summary_qty + $qty;
                    //         }

                    //         $serial_no = $this->serialNumber($detail->company_id, $detail->principal_id);

                    //         $pqty = ($qty  - ($qty % $detail->uppp)) / $detail->uppp;
                    //         $mqty = (($qty % $detail->uppp) - (($qty % $detail->uppp) % $detail->muppp)) / $detail->muppp;
                    //         $bqty = $qty % $detail->uppp % $detail->muppp;            

                    //         $inbound_batchin = [];

                    //         $inbound_batchin[] = [
                    //             'inbound_id' => $detail->inbound_id,
                    //             'packing_id' => $id,
                    //             'company_id' => $detail->company_id,
                    //             'principal_id' => $detail->principal_id,
                    //             'serial_no' => $serial_no,
                    //             'job_no' => $detail->job_no,
                    //             'vehicle_no' => $detail->vehicle_no,
                    //             'product_id' => $detail->product_id,
                    //             'product_code' => $detail->product_code,
                    //             'po_number' => $detail->po_number,
                    //             'lot_no' => $detail->lot_no,
                    //             'document_ref' => $detail->document_ref,
                    //             'mfg_date' => $detail->mfg_date,
                    //             'exp_date' => $detail->exp_date,
                    //             'manufactur_id' => $detail->manufactur_id,
                    //             'status_id' => $detail->status_id,
                    //             'site_id' => $site_id,
                    //             'area_id' => null,
                    //             'location_id' => null,
                    //             'location_code' => null,
                    //             'puom' => $detail->puom,
                    //             'muom' => $detail->muom,
                    //             'buom' => $detail->buom,
                    //             'uppp' => $detail->uppp,
                    //             'muppp' => $detail->muppp,
                    //             'pqty' => $pqty,
                    //             'mqty' => $mqty,
                    //             'bqty' => $bqty,
                    //             'qty' => $detail->actual_qty,
                    //             'pallet_qty' => $base_qty,
                    //             'base_unit' => $detail->base_unit,
                    //             'product_status' => $detail->product_status,
                    //             'manual_putaway' => $manual_putaway,
                    //             'created_at' => \Carbon\Carbon::now()
                    //         ];

                    //         inboundBatch::insert($inbound_batchin);
                    //     }

                    //     $job->allocated_flag = 'Yes';
                    //     $job->allocated_by = $confirmed_by;
                    //     $job->allocated_date = $confirmed_date;
                    //     $job->save();

                    //     $detail->putaway_flag = 'Yes';
                    //     $detail->putaway_by = $confirmed_by;
                    //     $detail->putaway_date = $confirmed_date;
                    //     $detail->manual_putaway = $manual_putaway;
                    //     $detail->save();                        
                    // } else {
                    //     $actual_qty = $request->qty[$i];

                    //     $site_id = "%";
                    //     $area_id = "%";

                    //     if (!empty($detail->site_id) && isset($detail->site_id)) {
                    //         $site_id = $detail->site_id;
                    //     }

                    //     if (!empty($detail->area_id) && isset($detail->area_id)) {
                    //         $area_id = $detail->area_id;
                    //     }

                    //     if (!empty($detail->location_from) && !empty($detail->location_to)) {
                    //         $location_from = $detail->location_from;
                    //         $location_to = $detail->location_to;
                    //     } else { 
                    //         if (!empty($detail->location_from) && empty($detail->location_to)) {
                    //             $location_from = $detail->location_from;
                    //             $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                    //         } else if (empty($detail->location_from) && !empty($detail->location_to)) {
                    //             $location_from = "";
                    //             $location_to = $detail->location_to;
                    //         } else {
                    //             $location_from = "";
                    //             $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                    //         }
                    //     }

                    //     if ( $detail->mixed_pallet == "Yes" ) {
                    //         $location_status = "M";
                    //     } else {
                    //         $location_status = "E";
                    //     }

                    //     if ( $detail->pallet_id > 0 ) {   
                    //         $location_status = "M";
                    //     }
                    // dd($manual_putaway);

                    // if ($actual_qty > 0) {
                    //     $summary_qty = 0;
                    //     while ($summary_qty < $actual_qty) {       
                    //         if ( $manual_putaway == "No" ) {   
                    //             if ( $location_status == "E" ) {
                    //                 $location = \App\Models\Master\Location::from('iv_location as a')
                    //                             ->select("a.*")
                    //                             ->join('users_site as b', 'a.site_id', 'b.site_id')
                    //                             ->join('iv_principal_site as c', 'a.site_id', 'c.site_id')
                    //                             ->where('b.user_id', $user_id)
                    //                             ->where('c.principal_id', $detail->principal_id)
                    //                             ->where('a.company_id', $detail->company_id)
                    //                             ->where(DB::raw("COALESCE(a.site_id, 0)"), "LIKE", $site_id)
                    //                             ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    //                             ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    //                             ->where('a.status_code', $location_status)
                    //                             ->where('a.active', 'Yes')
                    //                             ->first();

                    //                 if ( !isset($location) ) {
                    //                     DB::rollBack();
                    //                     $message = ['error'=>'Location is fully!!!'];

                    //                     return $message;
                    //                 }
                    //             } else {                                
                    //                 $pallet_used = DB::table("iv_inbound_batch as a")
                    //                                 ->select("a.location_id")
                    //                                 ->where('a.inbound_id', $detail->inbound_id)
                    //                                 ->where("a.pallet_id", "<>", $detail->pallet_id);

                    //                 $stock = DB::table("iv_stock_ledger as a")
                    //                             ->select("a.location_id")
                    //                             ->join("iv_location as b", "a.location_id", "b.id")
                    //                             ->where("b.status_code", "M")
                    //                             ->where("a.qtys", ">", 0)
                    //                             ->where('a.company_id', $detail->company_id)
                    //                             ->where('a.principal_id', $detail->principal_id)
                    //                             ->where(DB::raw("COALESCE(a.site_id, 0)"), "LIKE", $site_id)
                    //                             ->groupBy("a.location_id")
                    //                             ->union($pallet_used)
                    //                             ->get();

                    //                 $data =  [];
                    //                 foreach ($stock as $value) {
                    //                     if ( isset($value->location_id) ) {
                    //                         $data[] = $value->location_id;
                    //                     }
                    //                 }

                    //                 $location = \App\Models\Master\Location::from('iv_location as a')
                    //                             ->select("a.*")
                    //                             ->join('users_site as b', 'a.site_id', 'b.site_id')
                    //                             ->join('iv_principal_site as c', 'a.site_id', 'c.site_id')
                    //                             ->where('b.user_id', $user_id)
                    //                             ->where('c.principal_id', $detail->principal_id)
                    //                             ->where('a.company_id', $detail->company_id)
                    //                             ->where(DB::raw("COALESCE(a.site_id, 0)"), "LIKE", $site_id)
                    //                             ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                    //                             ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                    //                             ->where('a.status_code', $location_status)
                    //                             ->where('a.active', 'Yes')
                    //                             // ->whereNotIn("a.id", $data)
                    //                             ->first();

                    //                 if ( $manual_putaway == "No" ) {   
                    //                     if ( !isset($location) ) {
                    //                         DB::rollBack();
                    //                         $message = ['error'=>'Location mixed is not define!!!'];

                    //                         return $message;
                    //                     }
                    //                 }
                    //             }

                    //             if ( $detail->pallet_id > 0 ) {
                    //                 $pallet_id = DB::table("iv_inbound_batch as a")
                    //                                 ->select("a.location_id")
                    //                                 ->where('a.inbound_id', $detail->inbound_id)
                    //                                 ->where("a.pallet_id", $detail->pallet_id)
                    //                                 ->groupBy("a.location_id")
                    //                                 ->first();

                    //                 if ( isset($pallet_id) ) {
                    //                     $location = \App\Models\Master\Location::find($pallet_id->location_id);

                    //                     // if ( !isset($location) ) {
                    //                     //     DB::rollBack();
                    //                     //     $message = ['error'=>'Location mixed is not define ( Pallet ID )!!!'];

                    //                     //     return $message;
                    //                     // }
                    //                 }                                                
                    //             }    
                    //         }

                    //         if ( $manual_putaway == "No" ) {
                    //             if ( $location_status == "E" ) {
                    //                 if ( $principal->pallet_capacity_racking  == "Yes" ) {
                    //                     $pallet_unit_count = \App\Models\Master\PalletUnit::where('company_id', $detail->company_id)
                    //                                         ->where('principal_id', $detail->principal_id)
                    //                                         ->where('product_id', $detail->product_id)
                    //                                         ->where('type_id', $location->type_id)
                    //                                         ->where('pallet_qty', '>', 0)
                    //                                         ->count();

                    //                     if ($pallet_unit_count == 0) {
                    //                         DB::rollBack();
                    //                         $message = ['error'=>'Pallet unit not define!!!', 'code'=>'pallet', 'product' => $product];

                    //                         return $message;
                    //                     }

                    //                     $pallet_unit = \App\Models\Master\PalletUnit::select('base_qty')
                    //                                 ->where('company_id', $detail->company_id)
                    //                                 ->where('principal_id', $detail->principal_id)
                    //                                 ->where('product_id', $detail->product_id)
                    //                                 ->where('type_id', $location->type_id)
                    //                                 ->where('base_qty', '>', 0)
                    //                                 ->first();

                    //                     $base_qty = $pallet_unit->base_qty;
                    //                 } else {
                    //                     $base_qty = $detail->actual_qty;
                    //                 }
                    //             } else {                             
                    //                 if ( $principal->pallet_capacity_bulk  == "Yes" ) {
                    //                     $pallet_unit_count = \App\Models\Master\PalletUnit::where('company_id', $detail->company_id)
                    //                                             ->where('principal_id', $detail->principal_id)
                    //                                             ->where('product_id', $detail->product_id)
                    //                                             ->where('type_id', $location->type_id)
                    //                                             ->where('pallet_qty', '>', 0)
                    //                                             ->count();

                    //                     if ($pallet_unit_count == 0) {
                    //                         DB::rollBack();
                    //                         $message = ['error'=>'Pallet unit not define!!!', 'code'=>'pallet', 'product' => $product];

                    //                         return $message;
                    //                     }

                    //                     $pallet_unit = \App\Models\Master\PalletUnit::select('base_qty')
                    //                                     ->where('company_id', $detail->company_id)
                    //                                     ->where('principal_id', $detail->principal_id)
                    //                                     ->where('product_id', $detail->product_id)
                    //                                     ->where('base_qty', '>', 0)
                    //                                     ->first();

                    //                     $base_qty = $pallet_unit->base_qty;
                    //                 } else {
                    //                     $base_qty = $detail->actual_qty;
                    //                 }
                    //             }
                    //         } else {                                
                    //             if ( $principal->pallet_capacity_bulk  == "Yes" ) {
                    //                 $pallet_unit_count = \App\Models\Master\PalletUnit::where('company_id', $detail->company_id)
                    //                                         ->where('principal_id', $detail->principal_id)
                    //                                         ->where('product_id', $detail->product_id)
                    //                                         ->where('type_id', $location->type_id)
                    //                                         ->where('pallet_qty', '>', 0)
                    //                                         ->count();

                    //                 if ($pallet_unit_count == 0) {
                    //                     DB::rollBack();
                    //                     $message = ['error'=>'Pallet unit not define!!!', 'code'=>'pallet', 'product' => $product];

                    //                     return $message;
                    //                 }

                    //                 $pallet_unit = \App\Models\Master\PalletUnit::select('base_qty')
                    //                                 ->where('company_id', $detail->company_id)
                    //                                 ->where('principal_id', $detail->principal_id)
                    //                                 ->where('product_id', $detail->product_id)
                    //                                 ->where('base_qty', '>', 0)
                    //                                 ->first();

                    //                 $base_qty = $pallet_unit->base_qty;
                    //             } else {
                    //                 $base_qty = $detail->actual_qty;
                    //             }
                    //         }

                    //         $site_id = null;
                    //         $area_id = null;
                    //         $location_id = null;
                    //         $location_code = null;

                    //         if ( $manual_putaway == "No" ) {
                    //             if ( isset($location) ) {
                    //                 $site_id = $location->site_id;
                    //                 $area_id = $location->area_id;
                    //                 $location_id = $location->id;
                    //                 $location_code = $location->location_code;                            

                    //                 if ($location->status_code == 'E') {
                    //                     $location->status_code = 'R';
                    //                     $location->save();
                    //                 }
                    //             } else {
                    //                 DB::rollBack();
                    //                 $message = ['error'=>'Location not define!!!'];

                    //                 return $message;
                    //             }
                    //         }

                    //         $summary_qty = $summary_qty + $base_qty;  

                    //         if ($summary_qty <= $actual_qty) {
                    //             $qty = $base_qty;
                    //         } else {
                    //             $summary_qty = $summary_qty - $base_qty;
                    //             $qty = $actual_qty - $summary_qty;
                    //             $summary_qty = $summary_qty + $qty;
                    //         }
                    //     }
                    // }

                    // if ( $detail->discrepancy_qty > 0 ) {
                    //     if ( $principal->site_bad > 0 ) {
                    //         $site_bad = \App\models\Master\Site::find($principal->site_bad);

                    //         if ( isset($site_bad) ) {
                    //             if ( $site_bad->type_id == 1 ) {
                    //                 $location = DB::table("users_site as a")
                    //                                 ->join("iv_location as b", "a.site_id", "b.site_id")
                    //                                 ->where("a.user_id", $user_id)
                    //                                 ->where("b.site_id", $principal->site_bad)
                    //                                 ->where("b.status_code", "B")
                    //                                 ->first();

                    //                 if ( ! isset($location) ) { 
                    //                     DB::rollBack();
                    //                     $message = ['error'=>'Site / Location bad not define!!!'];

                    //                     return $message;
                    //                 }

                    //                 $site_id = $location->site_id;
                    //                 $area_id = $location->area_id;
                    //                 $location_id = $location->id;
                    //                 $location_code = $location->location_code;
                    //             } else {                                        
                    //                 $site_id = $principal->site_bad;
                    //                 $area_id = null;
                    //                 $location_id = null;
                    //                 $location_code = null;
                    //             }
                    //         } else {
                    //             DB::rollBack();
                    //             $message = ['error'=>'Site / Location bad not define!!!'];

                    //             return $message;
                    //         }
                    //     } else {
                    //         $location = DB::table("users_site as a")
                    //                         ->join("iv_location as b", "a.site_id", "b.site_id")
                    //                         ->where("a.user_id", $user_id)
                    //                         ->where("b.status_code", "B")
                    //                         ->first();

                    //         if ( !isset($location) ) { 
                    //             DB::rollBack();
                    //             $message = ['error'=>'Site / Location bad not define!!!'];

                    //             return $message;
                    //         }

                    //         $site_id = $location->site_id;
                    //         $area_id = $location->area_id;
                    //         $location_id = $location->id;
                    //         $location_code = $location->location_code;
                    //     }

                    //     $serial_no = $this->serialNumberBad($detail->company_id, $detail->principal_id);

                    //     $qty = $detail->discrepancy_qty;          
                    //     $pqty = ($qty  - ($qty % $detail->uppp)) / $detail->uppp;
                    //     $mqty = (($qty % $detail->uppp) - (($qty % $detail->uppp) % $detail->muppp)) / $detail->muppp;
                    //     $bqty = $qty % $detail->uppp % $detail->muppp;

                    //     $inbound_batchin = [];

                    //     $inbound_batchin[] = [
                    //         'inbound_id' => $detail->inbound_id,
                    //         'packing_id' => $id,
                    //         'company_id' => $detail->company_id,
                    //         'principal_id' => $detail->principal_id,
                    //         'serial_no' => $serial_no,
                    //         'job_no' => $detail->job_no,
                    //         'vehicle_no' => $detail->vehicle_no,
                    //         'product_id' => $detail->product_id,
                    //         'product_code' => $detail->product_code,
                    //         'po_number' => $detail->po_number,
                    //         'lot_no' => $detail->lot_no,
                    //         'document_ref' => $detail->document_ref,
                    //         'mfg_date' => $detail->mfg_date,
                    //         'exp_date' => $detail->exp_date,
                    //         'manufactur_id' => $detail->manufactur_id,
                    //         'status_id' => $detail->status_id,
                    //         'site_id' => $site_id,
                    //         'area_id' => $area_id,
                    //         'location_id' => $location_id,
                    //         'location_code' => $location_code,
                    //         'pallet_id' => 0,
                    //         'puom' => $detail->puom,
                    //         'muom' => $detail->muom,
                    //         'buom' => $detail->buom,
                    //         'uppp' => $detail->uppp,
                    //         'muppp' => $detail->muppp,
                    //         'pqty' => $pqty,
                    //         'mqty' => $mqty,
                    //         'bqty' => $bqty,
                    //         'qty' => $qty,
                    //         'pallet_qty' => $qty,
                    //         'base_unit' => $detail->base_unit,
                    //         'product_status' => "Damage",
                    //         'manual_putaway' => $manual_putaway,
                    //         'created_at' => \Carbon\Carbon::now()
                    //     ];

                    //     inboundBatch::insert($inbound_batchin);
                    // }

                    // }
                }
                DB::commit();
                $message = ['success' => 'Data Successfully Saved'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function serialNumber($company_id, $principal_id)
    {
        $date = \Carbon\Carbon::today();
        $year = $date->year;
        $month = $date->month;

        $serial = inboundBatch::where('company_id', $company_id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)->max("serial_no");

        if (is_null($serial)) {
            $last_number = 0;
        } else {
            $last_number = substr($serial, 7, 5);
        }

        $increment = $last_number + 1;
        $serial_no = 'I' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(5, '0');

        return $serial_no;
    }

    private function serialNumberBad($company_id, $principal_id)
    {
        $date = \Carbon\Carbon::today();
        $year = $date->year;
        $month = $date->month;

        $serial = inboundBatch::where('company_id', $company_id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)->max("serial_no");

        if (is_null($serial)) {
            $last_number = 0;
        } else {
            $last_number = substr($serial, 7, 5);
        }

        $increment = $last_number + 1;
        $serial_no = 'B' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(5, '0');

        return $serial_no;
    }

    public function startPutaway($inbound_id, $product_id, $picking_id)
    {
        $site_arr = DB::table('users_site')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('site_id')
            ->toArray();

        $location = DB::table('iv_location as a')
            ->select('a.*', 'b.site_name')
            ->join('iv_site as b', 'b.id', 'a.site_id')
            ->where('a.active', 'yes')
            ->whereIn('site_id', $site_arr)
            ->get();

        return view('transaction.inbound.start_putaway', compact('location', 'inbound_id', 'picking_id'));
    }

    public function getListPutaway($picking_id)
    {
        $exception = DB::transaction(function () use ($picking_id) {
            try {
                $data = DB::table('iv_inbound_per_pallet')
                    ->where('picking_id', $picking_id)
                    ->get();
                $data->map(function ($value) use ($picking_id) {
                    $value->detail = DB::table('iv_inbound_detail')
                        ->where('id', $picking_id)
                        ->first();
                });
                $message = ['data' => $data];
                return $message;
            } catch (\Exception $e) {
                $message = ["error" => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function getLocationAvail($inbound_id)
    {
        $principal_id = DB::table('iv_inbound_job')
            ->where('id', $inbound_id)->value('principal_id');


        $location_avail = DB::table('iv_stock_ledger')
            ->orderBy('location_code', 'ASC')
            ->select('location_code')
            ->where('qtya', 0)
            ->where('principal_id', $principal_id)
            ->get()->groupBy('location_code');
        $data = [];
        foreach ($location_avail as $key => $value) {
            $data[] =  $key;
        }
        return DataTables::of($data)->addColumn('location', function ($value) {
            return $value;
        })
            ->rawColumns(["location"])
            ->make(true);
    }


    public function scanPalletTag($qrcode, $id, $product_code)
    {
        $data = DB::table('iv_inbound_detail')
            ->where('qrcode', $qrcode)
            ->first();
        $id_per_pallet = DB::table('iv_inbound_per_pallet')
            ->where('id', $id)
            ->first()->id ?? '-';

        return response()->json([
            'data' => $data,
            'id_per_pallet' => $id_per_pallet,
            'status' => 'ok'
        ]);
    }

    public function postScanPalletTag(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                DB::table('iv_inbound_per_pallet')
                    ->where('id', $request->id_per_pallet)
                    ->update([
                        'qrcode' => $request->qrcode,
                        'scan_pallet_tag' => 'Yes'
                    ]);
                DB::commit();
                return 'success';
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ["error" => [$e->getMessage()]];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function editLocation(Request $request)
    {
        DB::transaction(function () use ($request) {
            try {
                $detail = DB::table('iv_location')
                    ->where('id', $request->location_id)
                    ->first();
                if ($detail->site_id == 3 && $detail->status_code != 'B') {  //validasi double location khusus belawan
                    $ledger = stockLedger::where('location_id', $detail->id)->where('qtys', '>', 0)->count();
                    if ($ledger > 0) {
                        DB::rollBack();
                        Session::flash('error', 'Double Location, Please choise another location.');
                        return back();
                    } else {
                        DB::table('iv_inbound_per_pallet')
                            ->where('id', $request->id_per_pallet)
                            ->update([
                                'location_id' => $detail->id,
                                'location_code' => $request->location_code,
                                'location_status' => $detail->status_code,
                                'remarks'   => $request->has('remarks_damage') ? $request->remarks_damage : NULL
                            ]);
                        DB::commit();
                        Session::flash('success', 'Data Has been saved successfully.');
                    }
                } else {
                    DB::table('iv_inbound_per_pallet')
                        ->where('id', $request->id_per_pallet)
                        ->update([
                            'location_id' => $detail->id,
                            'location_code' => $request->location_code,
                            'location_status' => $detail->status_code,
                            'remarks'   => $request->has('remarks_damage') ? $request->remarks_damage : NULL
                        ]);
                    DB::commit();
                    Session::flash('success', 'Data Has been saved successfully.');
                    return back();
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('error', $e->getMessage());
                return back();
            }
        });
        return back();
    }
}
