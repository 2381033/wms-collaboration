<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as guzzle;
use GuzzleHttp\Exception\BadResponseException;

use App\Models\Transaction\Inbound\Batch as inboundBatch;
use App\Models\Master\Location as masterLocation;
use App\Models\Master\Product as masterProduct;
use App\Models\Master\PalletUnit as masterPalletUnit;
use App\Models\Transaction\Inbound\Job as inboundJob;
use App\Models\Transaction\Stock\Ledger as stockLedger;
use App\Models\Transaction\Stock\Transaction as Transaction;
use App\Models\Transaction\Inbound\Detail as inboundDetail;
use Session;


class BatchController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = inboundBatch::from('iv_inbound_batch as a')
                ->select('a.*', 'b.product_name', "d.class_name")
                ->join('iv_product as b', 'a.product_id', 'b.id')
                ->join('iv_inbound_job as c', 'a.inbound_id', 'c.id')
                ->join('iv_job_class as d', 'c.class_id', 'd.id')
                ->where('a.company_id', $company_id)
                ->where('a.inbound_id', $request->inbound_id)
                ->where('a.confirmed_flag', 'No')
                ->get();

            return datatables()->of($list_data)
                ->editColumn('exp_date', function ($data) {
                    $exp_date = "";
                    if (isset($data->exp_date)) {
                        $exp_date = date('d/m/Y', strtotime($data->exp_date));
                    }
                    return $exp_date;
                })
                ->editColumn('mfg_date', function ($data) {
                    $mfg_date = "";
                    if (isset($data->mfg_date)) {
                        $mfg_date = date('d/m/Y', strtotime($data->mfg_date));
                    }
                    return $mfg_date;
                })
                ->addColumn('action', function ($data) {
                    $button = '<a href="javascript:void(0)" onClick="getEditLokasiBatch(' . $data->id . ')" data-id="' . $data->id . '" class="btn btn-dark btn-sm"> Edit</a>';
                    return $button;
                })
                ->addColumn('check', function ($data) {
                    $check = "";
                    if ($data->location_code == null && $data->site_id == null) {
                        $check .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Edit" class="edit-location btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                    } else {
                        if ($data->class_name == "Manual Put Away" && $data->location_code !== null) {
                            $check = '<input type="checkbox" required="required" name="confirm_id[]" class="confirm-check" id="' . $data->id . '" value="' . $data->id . '">';
                        } else if ($data->class_name !== 'Manual Put Away') {
                            $check = '<input type="checkbox" required="required" name="confirm_id[]" class="confirm-check" id="' . $data->id . '" value="' . $data->id . '">';
                        }
                    }
                    return $check;
                })
                ->rawColumns(['action', 'check'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = DB::table('iv_inbound_batch as a')
            ->select('a.*', 'b.product_name', 'b.unit_level', 'c.site_name', 'd.area_name')
            ->join('iv_product as b', 'a.product_id', 'b.id')
            ->leftJoin('iv_site as c', 'a.site_id', 'c.id')
            ->leftJoin('iv_site_area as d', 'a.area_id', 'd.id')
            ->where('a.id', $request->id)
            ->first();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'location_code_confirm.required' => 'Location field is required.',
        );

        $rules = array(
            'location_code_confirm' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            $batch = inboundBatch::find($request->batch_id);

            try {
                $batch->site_id = $request->site_id_confirm;
                $batch->area_id = $request->area_id_confirm;
                $batch->location_id = $request->location_id_confirm;
                $batch->location_code = $request->location_code_confirm;

                $batch->save();

                $location = masterLocation::find($request->location_id_confirm);

                if ($location->status_code == 'E') {
                    $location->status_code = 'R';
                    $location->save();
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

    public function pallet(Request $request)
    {
        $product_id = $request->product_id;

        if ($request->ajax()) {
            $list_data = DB::table("iv_location_type as a")
                ->select("a.id", "a.description", "b.pallet_qty")
                ->leftJoin("iv_pallet_unit as b", function ($join) use ($product_id) {
                    $join->on("b.type_id", "a.id")
                        ->where("b.product_id", $product_id);
                })
                ->where('a.active', 'Yes')
                ->get();

            return datatables()->of($list_data)
                ->editColumn("pallet_qty", function ($data) {
                    if (empty($data->pallet_qty)) {
                        $pallet_qty = 0;
                    } else {
                        $pallet_qty = $data->pallet_qty;
                    }

                    $input = "<input type='hidden' value='" . $data->id . "' name='id[]' class='form-control'/><input type='text' value='$pallet_qty' name='pallet_qty[]' class='form-control'/>";

                    return $input;
                })
                ->rawColumns(['pallet_qty'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function palletStore(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $id = $request->id;
                $product_id = $request->product_id_pallet;
                $pallet_qty = $request->pallet_qty;

                for ($i = 0; $i < count($id); $i++) {
                    $product = masterProduct::find($product_id);

                    $pallet = masterPalletUnit::where("company_id", $product->company_id)
                        ->where("principal_id", $product->principal_id)
                        ->where("product_id", $product_id)
                        ->where("type_id", $id[$i])
                        ->first();

                    $base_qty = $product->uppp * $pallet_qty[$i];

                    if (isset($pallet)) {
                        $pallet->pallet_qty = $pallet_qty[$i];
                        $pallet->base_qty = $base_qty;
                        $pallet->uom = $product->puom;
                    } else {
                        $pallet = new masterPalletUnit();

                        $pallet->company_id = $product->company_id;
                        $pallet->principal_id = $product->principal_id;
                        $pallet->product_id = $product->id;
                        $pallet->type_id = $id[$i];
                        $pallet->pallet_qty = $pallet_qty[$i];
                        $pallet->base_qty = $base_qty;
                        $pallet->uom = $product->puom;
                    }

                    $pallet->save();
                }

                DB::commit();

                $message = ["success" => "Sukses"];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function submit(Request $request)
    {
        $user_id = Auth::user()->id;
        $confirmed_by = Auth::user()->username;
        $confirmed_date = \Carbon\Carbon::now();
        $job_date = \Carbon\Carbon::today();
        try {
            $dataapi = $request->confirm_id;
            $data = $request->confirm_id;
            $principal = DB::table('iv_inbound_job')
                ->where('id', $request->inbound_id)
                ->value('principal_id');
            if ($principal == 32) { //32 adalah mostrans
                $location = array();
                for ($i = 0; $i < count($request->location_code); $i++) {
                    $location[] =  [
                        'location' => $request->location_code[$i],
                        'status'   => $request->location_status[$i],
                    ];
                }
                $location          = collect($location);
                $not_damage_area   = $location->where('status', '!=', 'B')->count();
                $location_code     = $location->where('status', '!=', 'B')->pluck('location')->toArray();

                //check jika inputan lokasi double
                if ($not_damage_area > count(array_unique($location_code))) {
                    DB::rollBack();
                    $message = ['error' => 'Periksa inputan, terdapat lokasi yang double..'];
                    return $message;
                }
            }

            $arrid = '';
            foreach ($data as $key => $value) {
                if (strlen($arrid) > 0) {
                    $arrid .= "," . $value;
                } else {
                    $arrid .= $value;
                }
            }

            $stockPerMinQty = DB::select("CALL sp_inbound_batch_confirmation(?,?,?)", array($arrid, $user_id, $confirmed_by));
            $message = ['success' => 'Data Successfully Saved'];
            $dataapi = $request->confirm_id;
            $this->sendAPI($dataapi);
            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
            $message = ['error' => $e->getMessage()];
            return $message;
        }
        return response()->json($message);
    }

    public function submit2(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $user_id = Auth::user()->id;
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            $job_date = \Carbon\Carbon::today();
            $year = $job_date->year;
            $month = $job_date->month;
            try {
                // $dataapi = $request->confirm_id;
                // $this->sendAPI($dataapi);
                // die('Dreaming');
                $dataapi = $request->confirm_id;
                $data = $request->confirm_id;
                $principal = DB::table('iv_inbound_job')
                    ->where('id', $request->inbound_id)
                    ->value('principal_id');
                if ($principal == 32) { //32 adalah mostrans
                    $location = array();
                    for ($i = 0; $i < count($request->location_code); $i++) {
                        $location[] =  [
                            'location' => $request->location_code[$i],
                            'status'   => $request->location_status[$i],
                        ];
                    }
                    $location          = collect($location);
                    $not_damage_area   = $location->where('status', '!=', 'B')->count();
                    $location_code     = $location->where('status', '!=', 'B')->pluck('location')->toArray();

                    //check jika inputan lokasi double
                    if ($not_damage_area > count(array_unique($location_code))) {
                        DB::rollBack();
                        $message = ['error' => 'Periksa inputan, terdapat lokasi yang double..'];
                        return $message;
                    }
                }

                foreach ($data as $key => $id) {
                    $batch = inboundBatch::find($id);
                    $job = inboundJob::find($batch->inbound_id);

                    if ($principal == 32) {
                        //validasi on stock ledger jika bukan floor
                        $validasi_loc = DB::table('iv_stock_ledger')
                            ->where('location_id', $batch->location_id)
                            ->where('area_id', $batch->area_id)
                            ->where('qtya', '>', 0)
                            ->where('status', 'G')
                            ->first();
                        if ($validasi_loc != null) {
                            DB::rollBack();
                            $message = ['error' => 'Lokasi ' . $validasi_loc->location_code . ' Full, Sudah Terisi SKU : ' . $validasi_loc->product_code];
                            return $message;
                        }
                    }


                    if ($batch->manual_putaway == 'Yes') {
                        $serial_no = $this->serialNumber($job->company_id, $job->principal_id);

                        $batch->serial_no = $serial_no;
                        $batch->save();
                    }

                    $product = masterProduct::find($batch->product_id);

                    if ($product->freeze_flag == "Yes") {
                        $freeze_flag = "Yes";
                        $freeze_by = $confirmed_by;
                        $freeze_date = $confirmed_date;
                        $freeze_reason = "Freeze by product master.";
                    } else {
                        $freeze_flag = "No";
                        $freeze_by = null;
                        $freeze_date = null;
                        $freeze_reason = null;
                    }

                    $site = DB::table("iv_site as a")
                        ->select("a.*", "b.type_name")
                        ->leftjoin("iv_site_type as b", "a.type_id", "b.id")
                        ->join('users_site as c', 'a.id', 'c.site_id')
                        ->where('c.user_id', $user_id)
                        ->where("a.id", $batch->site_id)
                        ->first();


                    if ($site->type_name == "Bulk") {
                        $area_id = null;
                        $location_id = null;
                        $location_code = null;
                    } else {
                        $location = masterLocation::find($batch->location_id);

                        if ($location->status_code == "R") {
                            $location->status_code = 'F';
                            $location->save();
                        }

                        $area_id = $batch->area_id;
                        $location_id = $batch->location_id;
                        $location_code = $batch->location_code;
                    }

                    $stock_ledger = [];
                    $stock_ledger[] = [
                        'company_id' => $batch->company_id,
                        'branch_id' => $job->branch_id,
                        'principal_id' => $batch->principal_id,
                        'serial_no' => $batch->serial_no,
                        'srno' => $batch->serial_no,
                        'line_no' => $id,
                        'job_no' => $batch->job_no,
                        'job_date' => $job_date,
                        'vehicle_no' => $batch->vehicle_no,
                        'product_id' => $batch->product_id,
                        'product_code' => $batch->product_code,
                        'po_number' => $batch->po_number,
                        'lot_no' => $batch->lot_no,
                        'document_ref' => $batch->document_ref,
                        'mfg_date' => $batch->mfg_date,
                        'exp_date' => $batch->exp_date,
                        'manufactur_id' => $batch->manufactur_id,
                        'status_id' => $batch->status_id,
                        'site_id' => $batch->site_id,
                        'area_id' => $batch->area_id,
                        'location_id' => $batch->location_id,
                        'location_code' => $batch->location_code,
                        'puom' => $batch->puom,
                        'muom' => $batch->muom,
                        'buom' => $batch->buom,
                        'uppp' => $batch->uppp,
                        'muppp' => $batch->muppp,
                        'pqty' => $batch->pqty,
                        'mqty' => $batch->mqty,
                        'bqty' => $batch->bqty,
                        'qtyr' => $batch->qty,
                        'qtys' => $batch->qty,
                        'qtya' => $batch->qty,
                        'qtyp' => 0,
                        'pallet_qty' => $batch->pallet_qty,
                        'base_unit' => $batch->base_unit,
                        'reference_no' => $batch->job_no,
                        'freeze_flag' => $freeze_flag,
                        'freeze_by' => $freeze_by,
                        'freeze_date' => $freeze_date,
                        'freeze_reason' => $freeze_reason,
                        'created_at' => \Carbon\Carbon::now(),
                        'status' => $batch->remarks == null ? 'G' : 'B',
                    ];

                    $transaction = [];
                    $transaction[] = [
                        'company_id' => $batch->company_id,
                        'branch_id' => $job->branch_id,
                        'principal_id' => $batch->principal_id,
                        'serial_no' => $batch->serial_no,
                        'srno' => $batch->serial_no,
                        'line_no' => $id,
                        'job_no' => $batch->job_no,
                        'job_date' => $job_date,
                        'job_type' => 'IMP',
                        'product_id' => $batch->product_id,
                        'product_code' => $batch->product_code,
                        'po_number' => $batch->po_number,
                        'lot_no' => $batch->lot_no,
                        'document_ref' => $batch->document_ref,
                        'mfg_date' => $batch->mfg_date,
                        'exp_date' => $batch->exp_date,
                        'manufactur_id' => $batch->manufactur_id,
                        'status_id' => $batch->status_id,
                        'site_id' => $batch->site_id,
                        'area_id' => $batch->area_id,
                        'location_id' => $batch->location_id,
                        'location_code' => $batch->location_code,
                        'puom' => $batch->puom,
                        'muom' => $batch->muom,
                        'buom' => $batch->buom,
                        'uppp' => $batch->uppp,
                        'muppp' => $batch->muppp,
                        'pqty' => $batch->pqty,
                        'mqty' => $batch->mqty,
                        'bqty' => $batch->bqty,
                        'qty' => $batch->qty,
                        'base_unit' => $batch->base_unit,
                        'reference_no' => $batch->job_no,
                        'created_at' => \Carbon\Carbon::now()
                    ];

                    stockLedger::insert($stock_ledger);
                    Transaction::insert($transaction);

                    // if ($batch->manual_putaway == 'No') {
                    $inboundDetail = inboundDetail::find($batch->packing_id);

                    $inboundDetail->confirmed_flag = 'Yes';
                    $inboundDetail->confirmed_by = $confirmed_by;
                    $inboundDetail->confirmed_date = $confirmed_date;
                    $inboundDetail->save();
                    // }

                    $batch->confirmed_flag = 'Yes';
                    $batch->confirmed_by = $confirmed_by;
                    $batch->confirmed_date = $confirmed_date;
                    $batch->save();
                }

                if ($batch->manual_putaway == 'No') {
                    $detail = inboundDetail::where('inbound_id', $batch->inbound_id)->where('confirmed_flag', 'No')->get();

                    if ($detail->count() == 0) {
                        $batchin = inboundBatch::where('inbound_id', $batch->inbound_id)->where('confirmed_flag', 'No')->get();

                        if ($batchin->count() == 0) {
                            $job = inboundJob::find($batch->inbound_id);

                            $job->confirmed_flag = 'Yes';
                            $job->confirmed_by = $confirmed_by;
                            $job->confirmed_date = $confirmed_date;
                            $job->save();
                        }
                    }
                } else {
                    $batchin = inboundBatch::where('inbound_id', $batch->inbound_id)->where('confirmed_flag', 'No')->get();

                    if ($batchin->count() == 0) {
                        $job = inboundJob::find($batch->inbound_id);

                        $job->received_flag = 'Yes';
                        $job->received_by = $confirmed_by;
                        $job->received_date = $confirmed_date;

                        $job->confirmed_flag = 'Yes';
                        $job->confirmed_by = $confirmed_by;
                        $job->confirmed_date = $confirmed_date;
                        $job->save();
                    }
                }

                DB::commit();

                $message = ['success' => 'Data Successfully Saved'];
                if ($batch->principal_id == 32) {
                    $dataapi = $request->confirm_id;
                    $this->sendAPI($dataapi);
                }

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

    private function sendAPI($data)
    {
        $Mostrans = DB::table("iv_principal as a")
            ->select('id', 'principal_name', 'short_name')
            ->where("active", "Yes")
            ->where("short_name", "Mostrans")
            ->orderBy("a.id", "asc")
            ->orderBy("b.branch_id", "asc")
            ->count();
        if ($Mostrans > 0) {
            $datasend = array();
            $jsondatasend = '';
            foreach ($data as $id) {
                $batch = inboundBatch::find($id);
                $logs = DB::table("iv_epm_api_logs")
                    ->where("activity", "INBOUND")
                    ->where("activity_id", $batch->inbound_id)
                    ->where("send_status", "Y");
                $logcount = $logs->count();
                if ($batch->inbound_id > 0 && $logcount > 0) {
                    $logdata = $logs->first();
                    $job = inboundJob::find($batch->inbound_id);
                    $qty_on_log = 0;
                    $log_details = DB::table("iv_epm_api_log_details")
                        ->where("header_id", $logdata->id)
                        ->where("product_code", $batch->product_code)
                        ->where('lot_no', $batch->lot_no)
                        ->where("rqty", 0)
                        ->first();
                    $qty_on_receive = 0;
                    if (isset($log_details->mqty)) {
                        $qty_on_log = $log_details->mqty;
                        $all_qty_on_log = $log_details->mqty;
                        $allqtyreceipt = DB::table('iv_inbound_batch')
                            ->where('inbound_id', $batch->inbound_id)
                            ->where('product_code', $batch->product_code)
                            ->where('lot_no', $batch->lot_no)
                            ->sum('pqty');
                        if (isset($allqtyreceipt)) {
                            $qty_on_receive = $allqtyreceipt;
                        }
                    }

                    $location_code = explode('.', $batch->location_code);
                    $row = $location_code[0];
                    $bin = $location_code[1];
                    $lvl = $location_code[2];
                    // $location = DB::table('iv_location')->where('location_code', $batch->location_code)->where('active', 'Yes')->get(['id', 'location_code']);
                    $rec_date = ($job->received_date) ? $job->received_date : $job->entry_date;
                    $qty_receipt = $batch->pqty * $batch->muppp;
                    // $qty_descrepancy = ($batch->descrepancy_qty > 0) ? ($batch->descrepancy_qty * $batch->muppp) : 0;
                    $manufactur = DB::table("iv_manufactur")->where("id", $batch->manufactur_id)->first();
                    $manufactur_code = (isset($manufactur->manufactur_code)) ? $manufactur->manufactur_code : 'DC1';
                    $destination = "MD1";
                    $shipment = DB::table("iv_inbound_detail")
                        ->where("inbound_id", $batch->inbound_id)
                        ->where("product_id", $batch->product_id)
                        ->where("lot_no", $batch->lot_no)
                        ->first();
                    $remarks = "";

                    $locationstatus = DB::table('iv_location')->where('id', $batch->location_id)->where('location_code', $batch->location_code)->first();
                    $qty_damage = 0;
                    $qty_tampung = $qty_receipt;
                    if (isset($locationstatus->status_code)) {
                        if ($locationstatus->status_code == 'B') {
                            $qty_receipt = 0;
                            $qty_damage = $qty_tampung;
                            $remarks .= "Terdapat demage QTY sebanyak $qty_damage $batch->muom. ";
                        }
                    }
                    $qty_descrepancy = $qty_on_log - ($qty_on_receive * $batch->muppp);
                    if ($qty_descrepancy != 0) {
                        $remarks .= "Terdapat perbedaan QTY shipment dengan QTY Receipt sebanyak $qty_descrepancy $batch->muom, silahkan hubungi administrator MKT";
                        if (isset($log_details->mqty)) {
                            DB::table('iv_epm_api_log_details')->where('id', $log_details->id)
                                ->update([
                                    'rqty' => $qty_descrepancy
                                ]);
                        }
                    }
                    // $qtyshipmentfromEPM = $shipment->pqty * $shipment->muppp;
                    $data = [
                        "receipt_date" => date("d-m-Y H:i:m", strtotime($rec_date)),
                        "receipt_qty" => "$qty_receipt",
                        "reject_qty" => "$qty_descrepancy",
                        "receipt_uom" => "$batch->muom",
                        "to_row" => "$row",
                        "to_bin" => "$bin",
                        "to_level" => "$lvl",
                        "remarks" => "$remarks",
                        "shipping_org" => "$manufactur_code",
                        "destination_org" => "$destination",
                        "shipment_number" => "$shipment->po_number",
                        "item_code" => "$batch->product_code",
                        "lot_number" => "$batch->lot_no",
                        "shipment_qty" => "$all_qty_on_log",
                        "shipment_uom" => "$shipment->muom",
                        "damage_qty" => "$qty_damage"
                    ];
                    array_push($datasend, json_encode($data));
                    if (strlen($jsondatasend) > 1) {
                        $jsondatasend .= "," . json_encode($data);
                    } else {
                        $jsondatasend .= json_encode($data);
                    }
                }
            }
            if (strlen($jsondatasend) > 1) {
                // dd($datasend, json_encode($datasend), $jsondatasend, json_encode($jsondatasend), "[" . $jsondatasend . "]");
                $client = new guzzle();
                $urlapi = 'https://egate.enseval.com/api/Principal/MiniDC/MKTReceiving';
                try {
                    $response = $client->request('POST', $urlapi, [
                        'headers' => [
                            'accept' => '/',
                            'Content-Type' => 'application/json',
                            'Authorization' => 'dGVzdDp0ZXN0'
                        ],
                        'body' => "[" . $jsondatasend . "]"
                    ]);
                    DB::beginTransaction();
                    $saveresponse = DB::table('iv_epm_response_api')->insert([
                        'activity' => 'INBOUND',
                        'activity_id' => $batch->inbound_id,
                        'job_no' => $job->job_no,
                        'status' => $response->getStatusCode(),
                        'body' => "[" . $jsondatasend . "]",
                        'error' => $response->getBody()->getContents(),
                        'created_date' => \Carbon\Carbon::now()
                    ]);
                    if ($saveresponse) {
                        DB::commit();
                    } else {
                        DB::rollback();
                    }
                } catch (BadResponseException $ex) {
                    $response = $ex->getResponse();
                    $jsonBody = (string) $response->getBody();
                    DB::beginTransaction();
                    $saveresponse = DB::table('iv_epm_response_api')->insert([
                        'activity' => 'INBOUND',
                        'activity_id' => $batch->inbound_id,
                        'job_no' => $job->job_no,
                        'status' => $response->getStatusCode(),
                        'body' => "[" . $jsondatasend . "]",
                        'error' => $jsonBody,
                        'created_date' => \Carbon\Carbon::now()
                    ]);
                    if ($saveresponse) {
                        DB::commit();
                    } else {
                        DB::rollback();
                    }
                }
            }
            return 'sukses';
        }
        return 0;
    }

    public function getEditLokasiBatch($id_batch)
    {
        $data = inboundBatch::find($id_batch);
        return response()->json($data);
    }

    public function postEditLokasiBatch(Request $request)
    {
        $location = DB::table('iv_location')->where('id', $request->location_id)->first();
        DB::table('iv_inbound_batch')
            ->where('id', $request->batch_id)
            ->update([
                'location_id' => $location->id,
                'location_code' => $location->location_code,
            ]);

        Session::flash('success', 'Locations saved successfully..');
        return back();
    }
}
