<?php

namespace App\Http\Controllers\Transaction\Outbound;

use App\Exports\outboundOrderDetailExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Transaction\Outbound\Detail as outboundDetails;
use App\Models\Master\Location as masterLocation;
use App\Models\Transaction\Outbound\Job as outboundJob;
use App\Models\Transaction\Outbound\Order as outboundOrder;
use App\Imports\OutboundOrderDetailImport as OutboundOrderDetailImports;
use App\Models\Master\Customer as masterCustomer;
use App\Models\Master\Product as masterProduct;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = outboundDetails::from('iv_outbound_detail as a')
                ->select('a.*', 'b.product_name')
                ->join('iv_product as b', 'a.product_id', 'b.id')
                ->where('a.company_id', $company_id)
                ->where('a.outbound_id', $request->outbound_id)
                ->get();

            return datatables()->of($list_data)
                ->editColumn('exp_date', function ($data) {
                    return date('d/m/Y', strtotime($data->exp_date));
                })
                ->editColumn('mfg_date', function ($data) {
                    return date('d/m/Y', strtotime($data->mfg_date));
                })
                ->addColumn('action', function ($data) {
                    $button = "";
                    if ($data->picking_flag == 'No') {
                        if (Gate::allows('gate-access', "warehouse/outbound")) {
                            $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit-detail btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                            $button .= '&nbsp;&nbsp;';
                            $button .= '<button type="button" id="' . $data->id . '" class="delete-detail btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                        }
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = DB::table('iv_outbound_detail as a')
            ->select('a.*', 'b.product_name', 'c.customer_name', 'b.unit_level', 'd.site_name', 'e.area_name', "b.unit_level")
            ->join('iv_product as b', 'a.product_id', 'b.id')
            ->join('iv_customer as c', 'a.customer_id', 'c.id')
            ->leftjoin('iv_site as d', 'a.site_id', 'd.id')
            ->leftjoin('iv_site_area as e', 'a.area_id', 'e.id')
            ->where('a.id', $request->id)
            ->first();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $outbound_id = $request->outbound_order;

        if ($outbound_id > 0) {
            $job_status = outboundJob::find($outbound_id);

            if ($job_status->allocated_flag == 'Yes') {
                return response()->json(['error' => ['Job allocated.']]);
            }
        }

        $messsages = array(
            'order_id_detail.required' => 'Order no field is required.',
            'product_id.required' => 'Product name field is required.',
        );

        $rules = array(
            'order_id_detail' => 'required',
            'product_id' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $qty = ($request->pqty * $request->uppp) + ($request->mqty * $request->muppp) + $request->bqty;

        if ($qty == 0) {
            return response()->json(['error' => ['Quantity cannot be empty!']]);
        }

        $exception = DB::transaction(function () use ($request) {
            $user_id = Auth::user()->username;
            try {
                $id = $request->detail_id;
                $outbound_id = $request->outbound_detail;
                $company_id = Auth::user()->company_id;

                $job = outboundJob::find($outbound_id);
                $order = outboundOrder::find($request->order_id_detail);

                $qty = ($request->pqty * $request->uppp) + ($request->mqty * $request->muppp) + $request->bqty;

                if (isset($id) && !empty($id)) {
                    $detail = outboundDetails::find($id);
                } else {
                    $detail = new outboundDetails;
                }

                $detail->company_id = $company_id;
                $detail->outbound_id = $outbound_id;
                $detail->order_id = $request->order_id_detail;
                $detail->principal_id = $job->principal_id;
                $detail->customer_id = $request->customer_id_detail;
                $detail->job_no = $job->job_no;
                $detail->order_no = $request->order_no_detail;
                $detail->product_id = $request->product_id;
                $detail->product_code = $request->product_code;
                $detail->lot_no = $request->lot_no;
                // $detail->document_ref = $request->document_ref;
                $detail->site_id = $request->site_id;
                $detail->area_id = $request->area_id;
                $detail->location_from_id = $request->location_from_id;
                $detail->location_from = $request->location_from;
                $detail->location_to_id = $request->location_to_id;
                $detail->location_to = $request->location_to;
                $detail->puom = $request->puom;
                $detail->muom = $request->muom;
                $detail->buom = $request->buom;
                $detail->uppp = $request->uppp;
                $detail->muppp = $request->muppp;
                $detail->pqty = $request->pqty;
                $detail->mqty = $request->mqty;
                $detail->bqty = $request->bqty;
                $detail->qty = $qty;
                $detail->user_id = $user_id;

                $detail->save();

                // $job->allocated_flag = 'Yes';
                // $job->allocated_date = \Carbon\Carbon::now();
                // $job->save();

                $order->confirmed_flag = 'Yes';
                $order->save();

                DB::commit();

                $message = ['success' => 'Data Successfully Saved'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function destroy(Request $request)
    {
        try {
            $detail = outboundDetails::where('id', $request->id)->first();

            $detail_all = outboundDetails::where('order_no', $detail->order_no)
                ->where('id', '<>', $detail->id)
                ->get();

            if ($detail_all->count() == 0) {
                $vehicle = outboundOrder::where('order_no', $detail->order_no)->first();

                $vehicle->confirmed_flag = 'No';
                $vehicle->save();
            }

            $detail->delete();

            $data = ['success' => 'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex) {
            $data = ['error' => $ex->getMessage(), 'code' => $ex->getCode()];
        }

        return response()->json($data);
    }

    public function import(Request $request)
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // menangkap file excel
        $file = $request->file('file');

        // membuat nama file unik
        $nama_file = rand() . "." . $file->extension();

        $path = storage_path('app/file/excel/' . $nama_file);
        $request->file('file')->storeAs('file/excel', $nama_file);

        $import = new OutboundOrderDetailImports();
        $rows = $import->toCollection($path);

        $id = $request->job_id;
        $job = outboundJob::find($id);

        $insert = [];
        $errors = [];
        $error_flag = false;
        $line = 1;

        foreach ($rows[0] as $row) {
            $message = [];

            $customer_code = trim($row["customer_code"]);
            $order_no = trim($row["order_no"]);
            $customer_ref = trim($row["customer_ref"]);
            $product_code = trim($row["sku_no"]);
            $qty_1 = $row["qty_1"];
            $qty_2 = $row["qty_2"];
            $qty_3 = $row["qty_3"];
            $location_code = $row['location_code'] ?? '';
            $batch_no = $row["batch_no"];

            $detail = 0;
            $order_count = 0;
            $customer_count = 0;
            $product_count = 0;

            if ($customer_code === null || $product_code === null) {
                exit;
            } else {
                $customer_count = masterCustomer::where("principal_id", $job->principal_id)->where("customer_code", $customer_code)->count();

                $location_error = false;
                $batch_error = false;

                if ($customer_count > 0) {
                    $customer = masterCustomer::where("principal_id", $job->principal_id)->where("customer_code", $customer_code)->first();

                    $order_count = outboundOrder::where("outbound_id", $id)
                        ->where("customer_id", $customer->id)
                        ->where("order_no", $order_no)
                        ->count();

                    if ($order_count > 0) {
                        $order = outboundOrder::where("outbound_id", $id)
                            ->where("customer_id", $customer->id)
                            ->where("order_no", $order_no)
                            ->first();

                        $product_count = masterProduct::where("principal_id", $job->principal_id)
                            ->where("product_code", $product_code)
                            ->count();

                        if ($product_count > 0) {
                            $product = masterProduct::where("principal_id", $job->principal_id)
                                ->where("product_code", $product_code)
                                ->first();


                            $detail = outboundDetails::where("outbound_id", $job->id)
                                ->where("customer_id", $customer->id)
                                ->where("order_id", $order->id)
                                ->where("product_code", $product_code)
                                ->count();


                            // if ( $detail == 0 ) {
                            if (!empty($location_code) && $location_code !== "" && $location_code !== null) {
                                $stock = DB::table("iv_stock_ledger as a")
                                    ->where("a.principal_id", $job->principal_id)
                                    ->where("a.product_code", $product_code)
                                    ->where("a.location_code", $location_code)
                                    ->where("a.qtya", ">", 0)
                                    ->count();
                                if ($stock == 0) {
                                    $location_error = true;
                                } else {
                                    $stock = DB::table("iv_stock_ledger as a")
                                        ->where("a.principal_id", $job->principal_id)
                                        ->where("a.product_code", $product_code)
                                        ->where("a.location_code", $location_code)
                                        ->where("a.qtya", ">", 0)
                                        ->first();

                                    $location_id = $stock->location_id;
                                }

                                $stock = DB::table("iv_stock_ledger as a")
                                    ->where("a.principal_id", $job->principal_id)
                                    ->where("a.product_code", $product_code)
                                    ->where("a.lot_no", $batch_no)
                                    ->where("a.qtya", ">", 0)
                                    ->count();

                                if ($stock == 0 && !empty($batch_no)) {
                                    $batch_error = true;
                                }

                                if ($location_id > 0) {
                                    $loc = masterLocation::find($location_id);

                                    $site_id = $loc->site_id;
                                    $area_id = $loc->area_id;
                                    $location_from_id = $loc->id;
                                    $location_from = $loc->location_code;
                                    $location_to_id = $loc->id;
                                    $location_to = $loc->location_code;
                                }
                            } else {
                                $site_id = null;
                                $area_id = null;
                                $location_from_id = null;
                                $location_from = null;
                                $location_to_id = null;
                                $location_to = null;
                            }
                            // } 
                        }
                    }
                }

                if ($customer_count > 0 && $order_count > 0 && $product_count > 0 && $location_error == false && $batch_error == false) {
                    if (empty($pallet_id) || $pallet_id == "") {
                        $pallet_id = 0;
                    }

                    if (empty($qty_1) || $qty_1 == "") {
                        $qty_1 = 0;
                    }

                    if (empty($qty_2) || $qty_2 == "") {
                        $qty_2 = 0;
                    }

                    if (empty($qty_3) || $qty_3 == "") {
                        $qty_3 = 0;
                    }

                    $qty = ($qty_1 * $product->uppp) + ($qty_2 * $product->muppp) + $qty_3;

                    if ($qty == 0) {
                        $error_flag = true;
                        $message[] = [
                            "Line $line $product_code : Quantity must be greater 0"
                        ];
                    }

                    if ($error_flag == false) {
                        $insert[] = [
                            "company_id" => $job->company_id,
                            "outbound_id" => $id,
                            "order_id" => $order->id,
                            "principal_id" => $job->principal_id,
                            "customer_id" => $customer->id,
                            "job_no" => $job->job_no,
                            "order_no" => $order_no,
                            "document_ref" => $customer_ref,
                            "product_id" => $product->id,
                            "product_code" => $product_code,
                            "pqty" => $qty_1,
                            "mqty" => $qty_2,
                            "bqty" => $qty_3,
                            "qty" => $qty,
                            "puom" => $product->puom,
                            "muom" => $product->muom,
                            "buom" => $product->buom,
                            "uppp" => $product->uppp,
                            "muppp" => $product->muppp,
                            "site_id" => $site_id,
                            "area_id" => $area_id,
                            "location_from_id" => $location_from_id,
                            "location_from" => $location_from,
                            "location_to_id" => $location_to_id,
                            "location_to" => $location_to,
                            "lot_no" => $batch_no !== "" ? $batch_no : null,
                        ];
                    }
                } else {
                    $error_flag = true;
                }

                if ($error_flag == true) {
                    if ($customer_count == 0) {
                        $message[] = [
                            "Line $line $customer_code : customer not found."
                        ];
                    }
                    if ($order_count == 0) {
                        $message[] = [
                            "Line $line $order_no : order number not found."
                        ];
                    }
                    if ($product_count == 0) {
                        $message[] = [
                            "Line $line $product_code : product not found."
                        ];
                    }
                    // if ( $detail > 0 ) {
                    //     $message[] = [
                    //         "Line $line $product_code : Data already exists."
                    //     ];
                    // }
                    if ($location_error) {
                        $message[] = [
                            "Line $line $product_code : location not found."
                        ];
                    }
                    if ($batch_error) {
                        $message[] = [
                            "Line $line $product_code : batch number not found."
                        ];
                    }

                    $errors[] = $message;
                }

                $error_flag = false;
                $line++;
            }
        }

        Storage::delete('/file/excel/' . $nama_file);

        $exception = DB::transaction(function () use ($insert, $errors) {
            try {
                if (count($errors) > 0) {
                    $message = ['error' => $errors];
                } else {
                    outboundDetails::insert($insert);

                    DB::commit();

                    $message = ['success' => 'Data Successfully uploaded'];
                }

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function export($outbound_id)
    {
        $principal = DB::table("iv_outbound_job")
            ->where("id", $outbound_id)
            ->first()->principal_id;

        return Excel::download(new outboundOrderDetailExport($principal), "tempate-outbound.xlsx");
    }
}
