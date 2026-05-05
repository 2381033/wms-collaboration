<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Exports\inboundPackingExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Master\Product as MasterProduct;
use App\Models\Transaction\Inbound\Job as InboundJob;
use App\Models\Transaction\Inbound\Vehicle as InboundVehicle;
use App\Models\Transaction\Inbound\Detail as InboundDetail;
use App\models\Master\StockStatus as MasterStockStatus;

class DetailController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = InboundDetail::from('iv_inbound_detail as a')
                            ->select('a.*', 'b.product_name')
                            ->join('iv_product as b', 'a.product_id', 'b.id')
                            ->where('a.company_id', $company_id)
                            ->where('a.inbound_id', $request->inbound_id)
                            ->get();

            return datatables()->of($list_data)
            ->editColumn('exp_date', function ($data)
            {
                $exp_date = "";
                if (isset($data->exp_date)) {
                    $exp_date = date('d/m/Y', strtotime($data->exp_date) );
                }
                return $exp_date;
            })
            ->editColumn('mfg_date', function ($data)
            {
                $mfg_date = "";
                if (isset($data->mfg_date)) {
                    $mfg_date = date('d/m/Y', strtotime($data->mfg_date) );
                }
                return $mfg_date;
            })
            ->addColumn('action', function($data){
                $button = "";
                if ($data->received_flag == 'No') {
                    if (Gate::allows('gate-access', "warehouse/inbound")) {
                        $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit-packing btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" id="'.$data->id.'" class="delete-packing btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }
                }
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function edit(Request $request) {
        $data = InboundDetail::from('iv_inbound_detail as a')
                    ->select('a.*', 'b.product_name', 'b.unit_level')
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->where('a.id', $request->id)
                    ->first();

        return response()->json($data);
    }

    public function store(Request $request) {
        $inbound_id = $request->inbound_packing;

        if ($inbound_id > 0) {
            $job_status = InboundJob::find($inbound_id);

            // if ($job_status->received_flag == 'Yes') {
            //     return response()->json(['error'=>['Job Received.']]);
            // }
        }

        $messsages = array(
            'vehicle_packing.required'=>'Vehicle no field is required.',
            'product_id.required'=>'Product name field is required.',
        );

        $rules = array(
            'vehicle_packing' => 'required',
            'product_id' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $product = MasterProduct::find($request->product_id);

        if ($product->batch_flag == "Yes") {
            $messsages = array(
                'lot_no.required'=>'Batch number field is required.',
            );

            $rules = array(
                'lot_no' => 'required',
            );

            $validator = \Validator::make($request->all(), $rules,$messsages);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }

        if ($product->expired_flag == "Yes") {
            $messsages = array(
                'mfg_date.required'=>'Mfg date field is required.',
                'exp_date.required'=>'Exp date field is required.',
            );

            $rules = array(
                'mfg_date' => 'required',
                'exp_date' => 'required',
            );

            $validator = \Validator::make($request->all(), $rules,$messsages);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }

        $qty = ( $request->pqty * $request->uppp ) + ( $request->mqty * $request->muppp ) + $request->bqty;

        if ($qty == 0) {
            return response()->json(['error'=>['Quantity cannot be empty!']]);
        }

        $exception = DB::transaction(function () use ($request) {
            $user_id = Auth::user()->username;
            $vehicle = InboundVehicle::where('inbound_id', '=', $request->inbound_packing)
                        ->where('vehicle_no', '=', $request->vehicle_packing)->first();

            try {
                $id = $request->packing_id;
                $inbound_id = $request->inbound_packing;
                $company_id = Auth::user()->company_id;

                $job = InboundJob::find($inbound_id);

                $qty = ( $request->pqty * $request->uppp ) + ( $request->mqty * $request->muppp ) + $request->bqty;

                $actual_pqty = ( $qty - ( $qty % $request->uppp )) / $request->uppp;
                $actual_mqty = (( $qty % $request->uppp ) - ($qty % $request->uppp % $request->muppp)) / $request->muppp;
                $actual_bqty = $qty % $request->uppp % $request->muppp;

                if (isset($id) && !empty($id)) {
                    $detail = InboundDetail::find($id);
                } else {
                    $detail = new InboundDetail;
                }

                $mfg_date = null;
                if (isset($request->mfg_date) && !empty($request->mfg_date)) {
                    $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->mfg_date);
                    $mfg_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');
                }

                $exp_date = null;
                if (isset($request->exp_date) && !empty($request->exp_date)) {
                    $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->exp_date);
                    $exp_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');
                }

                $detail->company_id = $company_id;
                $detail->inbound_id = $inbound_id;
                $detail->principal_id = $job->principal_id;
                $detail->job_no = $job->job_no;
                $detail->vehicle_no = $request->vehicle_packing;
                $detail->product_id = $request->product_id;
                $detail->product_code = $request->product_code;
                $detail->po_number = $request->po_number;
                $detail->lot_no = $request->lot_no;
                $detail->document_ref = $request->document_ref;
                $detail->pallet_id = $request->pallet_id;
                $detail->mfg_date = $mfg_date;
                $detail->exp_date = $exp_date;
                $detail->puom = $request->puom;
                $detail->muom = $request->muom;
                $detail->buom = $request->buom;
                $detail->uppp = $request->uppp;
                $detail->muppp = $request->muppp;
                $detail->pqty = $request->pqty;
                $detail->mqty = $request->mqty;
                $detail->bqty = $request->bqty;
                $detail->qty = $qty;
                $detail->actual_pqty = $actual_pqty;
                $detail->actual_mqty = $actual_mqty;
                $detail->actual_bqty = $actual_bqty;
                $detail->actual_qty = $qty;
                $detail->manufactur_id = $request->manufactur_id;
                $detail->status_id = $request->status_id;
                $detail->user_id = $user_id;

                $detail->save();

                $vehicle->confirmed_flag = 'Yes';
                $vehicle->save();

                DB::commit();

                $message = ['success'=>'Data Successfully Saved'];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function destroy(Request $request) {
        try {
            $detail = InboundDetail::where('id',$request->id)->first();

            $detail_all = InboundDetail::where('vehicle_no', $detail->vehicle_no)
                                ->where('id', '<>', $detail->id)
                                ->get();

            if ($detail_all->count() == 0 ) {
                $vehicle = InboundVehicle::where('vehicle_no', $detail->vehicle_no)->first();

                $vehicle->confirmed_flag = 'No';
                $vehicle->save();
            }

            $detail->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }

    public function import(Request $request) {
		// validasi
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);

		// menangkap file excel
		$file = $request->file('file');

		// membuat nama file unik
		$nama_file = rand().".".$file->extension();

        $path = storage_path('app/file/excel/' . $nama_file);
        $request->file('file')->storeAs('file/excel', $nama_file);

        $import = new \App\Imports\InboundPackingImport();
        $rows = $import->toCollection($path);

        $id = $request->job_id;
        $job = InboundJob::find($id);

        $insert = [];
        $errors = [];
        $error_flag = false;
        $line = 1;
        foreach ($rows[0] as $row) {
            $message = [];

            $vehicle_no = $row["vehicle_no"];
            $product_code = $row["sku_no"];
            $batch_no = $row["batch_no"];
            $mfg_date = !empty($row["mfg_date"]) ? \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["mfg_date"]))->format("Y-m-d") : null;
            $exp_date = !empty($row["exp_date"]) ? \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["exp_date"]))->format("Y-m-d") : null;
            $status = $row["status"];
            $qty_1 = $row["qty_1"];
            $qty_2 = $row["qty_2"];
            $qty_3 = $row["qty_3"];
            $pallet_id = $row["pallet_id"];

            $vehicle = InboundVehicle::where("inbound_id", $id)
                            ->where("vehicle_no", $vehicle_no)
                            ->count();

            $product = MasterProduct::where("principal_id", $job->principal_id)
                            ->where("product_code", $product_code)
                            ->first();

            $stock_status = MasterStockStatus::where("principal_id", $job->principal_id)
                            ->where("status_name", $status)
                            ->first();

            $detail = DB::table("iv_inbound_detail as a")
                        ->Where("a.inbound_id", $id)
                        ->Where("a.product_code", $product_code)
                        ->Where(DB::raw("COALESCE(a.lot_no, '')"), $batch_no)
                        ->Where(DB::raw("COALESCE(a.mfg_date, null)"), $mfg_date)
                        ->Where(DB::raw("COALESCE(a.exp_date, null)"), $exp_date)
                        ->count();

            $stock_id = isset($stock_status) ? $stock_status->id : null;

            if ( $vehicle > 0 && isset($product) && $detail == 0 ) {
                $qty = ( $qty_1 * $product->uppp ) + ( $qty_2 * $product->muppp ) + $qty_3;

                if ( $qty == 0 ) {
                    $error_flag = true;
                    $message[] = [
                        "Line $line $product_code : quantity cannot be empty"
                    ];
                }

                if ( $product->batch_flag == "Yes" ) {
                    if ( empty($batch_no) || !isset($batch_no) ) {
                        $error_flag = true;
                        $message[] = [
                            "Line $line $product_code : Batch number cannot be empty"
                        ];
                    }
                }

                if ( $product->expired_flag == "Yes" ) {
                    if ( empty($mfg_date) || !isset($mfg_date) || empty($exp_date) || !isset($exp_date) ) {
                        $error_flag = true;
                        $message[] = [
                            "Line $line $product_code : Mfg / Exp date cannot be empty"
                        ];
                    }
                }

                if ( empty($pallet_id) || $pallet_id == "" ) {
                    $pallet_id = 0;
                }

                if ( empty($qty_2) || $qty_2 == "" ) {
                    $qty_2 = 0;
                }

                if ( empty($qty_3) || $qty_3 == "" ) {
                    $qty_3 = 0;
                }

                if ( $error_flag == false ) {
                    $insert[] = [
                        "company_id" => $job->company_id,
                        "inbound_id" => $id,
                        "principal_id" => $job->principal_id,
                        "job_no" => $job->job_no,
                        "vehicle_no" => $vehicle_no,
                        "product_id" => $product->id,
                        "product_code" => $product_code,
                        "po_number" => $row["do_no"],
                        "lot_no" => $batch_no,
                        "document_ref" => $row["document_ref"],
                        "mfg_date" => $mfg_date,
                        "exp_date" => $exp_date,
                        "status_id" => $stock_id,
                        "pallet_id" => $pallet_id,
                        "pqty" => $qty_1,
                        "mqty" => $qty_2,
                        "bqty" => $qty_3,
                        "qty" => $qty,
                        "actual_pqty" => $qty_1,
                        "actual_mqty" => $qty_2,
                        "actual_bqty" => $qty_3,
                        "actual_qty" => $qty,
                        "puom" => $product->puom,
                        "muom" => $product->muom,
                        "buom" => $product->buom,
                        "uppp" => $product->uppp,
                        "muppp" => $product->muppp,
                    ];
                }
            } else {
                $error_flag = true;
            }

            if ( $error_flag == true ) {
                if ( $vehicle == 0 ) {
                    $message[] = [
                        "Line $line $product_code : vehicle number not found"
                    ];
                }
                if ( !isset($product) ) {
                    $message[] = [
                        "Line $line $product_code : product not found"
                    ];
                }
                if ( $detail > 0 ) {
                    $message[] = [
                        "Line $line $product_code : Data already exists"
                    ];
                }

                $errors[] = $message;
            }

            $error_flag = false;
            $line++;
        }

        Storage::delete('/file/excel/'. $nama_file);

        $exception = DB::transaction(function () use ($insert, $errors) {
            try {
                \App\models\Transaction\Inbound\Detail::insert($insert);

                DB::commit();

                if (count($errors) > 0) {
                    $message = ['error'=>$errors];
                } else {
                    $message = ['success'=>'Data Successfully uploaded'];
                }

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
	}

    public function export(Request $request) {
		return Excel::download(new inboundPackingExport($request->inbound_id), "tempate-inbound.xlsx");
    }
}
