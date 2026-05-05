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
                $manual_putaway = $request->manual_putaway ?? 'Yes';
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
                        'ean_code' => $detail_pallet->ean_code,
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
