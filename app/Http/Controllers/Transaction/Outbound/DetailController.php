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
                ->select('a.*', 'b.product_name', 'b.manufactur_code')
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
                    if ($data->picking_flag == 'No'  && is_null($data->manufactur_code)) {
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
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('file');
        $nama_file = rand() . "." . $file->extension();
        $path = storage_path('app/file/excel/' . $nama_file);
        $file->storeAs('file/excel', $nama_file);

        $import = new OutboundOrderDetailImports();
        $rows = $import->toCollection($path);

        $job = outboundJob::find($request->job_id);

        $insert = [];
        $errors = [];
        $line = 1;

        foreach ($rows[0] as $row) {

            $message = [];
            $error_flag = false;

            $customer_code = trim($row["customer_code"] ?? '');
            $order_no      = trim($row["order_no"] ?? '');
            $customer_ref  = trim($row["customer_ref"] ?? '');
            $product_input = trim($row["sku_no"] ?? '');
            $qty_1         = $row["qty_1"] ?? 0;
            $qty_2         = $row["qty_2"] ?? 0;
            $qty_3         = $row["qty_3"] ?? 0;
            $location_code = trim($row["location_code"] ?? '');
            $batch_no      = trim($row["batch_no"] ?? '');

            if (empty($customer_code) || empty($product_input)) {
                $line++;
                continue;
            }

            $customer = masterCustomer::where("principal_id", $job->principal_id)
                ->where("customer_code", $customer_code)
                ->first();

            if (!$customer) {
                $message[] = "Line $line $customer_code : customer not found.";
                $error_flag = true;
            }

            if (!$error_flag) {
                $order = outboundOrder::where("outbound_id", $job->id)
                    ->where("customer_id", $customer->id)
                    ->where("order_no", $order_no)
                    ->first();

                if (!$order) {
                    $message[] = "Line $line $order_no : order number not found.";
                    $error_flag = true;
                }
            }

            if (!$error_flag) {

                $productData = \App\Helpers\ProductResolver::resolve(
                    $job->principal_id,
                    $product_input,
                    'outbound',
                    [$job->branch_id ?? null]
                );

                if (!$productData) {
                    $message[] = "Line $line $product_input : product not found or wrong code type.";
                    $error_flag = true;
                } else {
                    $product     = $productData['product'];
                    // $primaryCode = $product->product_code; // always primary for DB
                }
            }
            if (!$error_flag && !empty($location_code)) {

                $stock = DB::table("iv_stock_ledger")
                    ->where("principal_id", $job->principal_id)
                    ->where("product_id", $product->id)
                    ->where("location_code", $location_code)
                    ->where("qtya", ">", 0)
                    ->first();

                if (!$stock) {
                    $message[] = "Line $line $product_input : location not found.";
                    $error_flag = true;
                } else {

                    $location = masterLocation::find($stock->location_id);

                    $site_id           = $location->site_id ?? null;
                    $area_id           = $location->area_id ?? null;
                    $location_from_id  = $location->id ?? null;
                    $location_from     = $location->location_code ?? null;
                    $location_to_id    = $location->id ?? null;
                    $location_to       = $location->location_code ?? null;
                }

                if (!empty($batch_no)) {
                    $batchCheck = DB::table("iv_stock_ledger")
                        ->where("principal_id", $job->principal_id)
                        ->where("product_id", $product->id)
                        ->where("lot_no", $batch_no)
                        ->where("qtya", ">", 0)
                        ->exists();

                    if (!$batchCheck) {
                        $message[] = "Line $line $product_input : batch number not found.";
                        $error_flag = true;
                    }
                }
            } else {
                $site_id = $area_id = $location_from_id = $location_from =
                    $location_to_id = $location_to = null;
            }
            if (!$error_flag) {

                $qty_1 = $qty_1 ?: 0;
                $qty_2 = $qty_2 ?: 0;
                $qty_3 = $qty_3 ?: 0;

                $qty = ($qty_1 * $product->uppp) +
                    ($qty_2 * $product->muppp) +
                    $qty_3;

                if ($qty <= 0) {
                    $message[] = "Line $line $product_input : Quantity must be greater 0.";
                    $error_flag = true;
                }
            }

            if (!$error_flag) {

                $insert[] = [
                    "company_id"       => $job->company_id,
                    "outbound_id"      => $job->id,
                    "order_id"         => $order->id,
                    "principal_id"     => $job->principal_id,
                    "customer_id"      => $customer->id,
                    "job_no"           => $job->job_no,
                    "order_no"         => $order_no,
                    "document_ref"     => $customer_ref,
                    "product_id"       => $product->id,
                    "product_code"     => $product_input,
                    "pqty"             => $qty_1,
                    "mqty"             => $qty_2,
                    "bqty"             => $qty_3,
                    "qty"              => $qty,
                    "puom"             => $product->puom,
                    "muom"             => $product->muom,
                    "buom"             => $product->buom,
                    "uppp"             => $product->uppp,
                    "muppp"            => $product->muppp,
                    "site_id"          => $site_id,
                    "area_id"          => $area_id,
                    "location_from_id" => $location_from_id,
                    "location_from"    => $location_from,
                    "location_to_id"   => $location_to_id,
                    "location_to"      => $location_to,
                    "lot_no"           => $batch_no ?: null,
                ];
            } else {
                $errors[] = $message;
            }

            $line++;
        }

        Storage::delete('/file/excel/' . $nama_file);

        if (count($errors) > 0) {
            return response()->json(['error' => $errors]);
        }

        outboundDetails::insert($insert);

        return response()->json(['success' => 'Data Successfully uploaded']);
    }


    public function export($outbound_id)
    {
        $principal = DB::table("iv_outbound_job")
            ->where("id", $outbound_id)
            ->first()->principal_id;

        return Excel::download(new outboundOrderDetailExport($principal), "tempate-outbound.xlsx");
    }

    public function getListEAN($job_id)
    {
        $list_data = DB::table("iv_outbound_batch as a")
            ->select(
                "a.id",
                "a.product_code",
                "b.product_name",
                "a.lot_no",
                "a.ean_code",
                "a.pqty",
                "a.mqty",
                "a.remarks",
                "b.puom",
                "b.muom",
                "b.buom",
                "b.uppp",
                "b.muppp",
                "b.volume",
                "b.manufactur_code",
                "b.gross_weight",
                DB::raw("sum(a.qty) as qty"),
                DB::raw("CASE
                        WHEN b.manufactur_code IS NULL THEN a.qty
                        WHEN a.ean_code IS NOT NULL THEN LENGTH(a.ean_code) - LENGTH(REPLACE(a.ean_code, ',', '')) + 1
                        ELSE 0
                    END as ean_code_count")
            )
            ->join("iv_product as b", "a.product_id", "b.id")
            ->where("a.outbound_id", $job_id)
            ->groupBy(
                "a.product_code",
                "b.product_name",
                "a.lot_no",
                "b.puom",
                "b.muom",
                "b.buom",
                "b.uppp",
                "b.muppp",
                "b.volume",
                "b.gross_weight"
            )
            ->get();
        return datatables()->of($list_data)
            ->addIndexColumn()
            ->make(true);
    }

    public function doScanEan($value, $job_id)
    {
        $exception = DB::transaction(function () use ($value, $job_id) {
            try {
                $manufactur_code = $this->extractDigits($value);
                $masterProd = DB::table('iv_product')
                    ->select('manufactur_code', 'product_code')
                    ->where('manufactur_code', $manufactur_code)
                    ->first();
                if (is_null($masterProd)) {
                    $message = ['message' => 'invalid'];
                    DB::rollBack();
                } else {
                    $pluckArr = $this->getDetail($job_id)->pluck('product_code')->toArray();
                    if (in_array($masterProd->product_code, $pluckArr)) {
                        $pluckEan = $this->getDetail($job_id)->pluck('ean_code')->toArray();
                        $filteredData = array_filter($pluckEan, function ($value) {
                            return !is_null($value);
                        });
                        $eanCodes = array_map(function ($value) {
                            return explode(',', $value); // Memisahkan berdasarkan koma
                        }, $filteredData);
                        $flattenedEanCodes = array_merge(...$eanCodes);
                        if (in_array($value, $flattenedEanCodes)) {
                            $message =  ['message' => 'duplicate'];
                            DB::rollBack();
                        } else {
                            $eanCode = DB::table('iv_outbound_batch')
                                ->where('outbound_id', $job_id)
                                ->where('product_code', $masterProd->product_code)
                                ->get()->pluck('ean_code')->toArray();
                            $qty = $this->getDetail($job_id)
                                ->where('product_code', $masterProd->product_code)
                                ->sum('qty');
                            $string = $eanCode[0];  // Ambil ean_code pertama
                            // Periksa apakah ean_code kosong atau null
                            if (empty($string)) {
                                $ean_code_count = 0;
                            } else {
                                // Jika ada nilai ean_code, pisahkan dan hitung jumlahnya
                                $ean_codes = explode(",", $string);
                                $ean_code_count = count($ean_codes);
                            }
                            if ($ean_code_count >= $qty) {
                                $message =  ['message' => 'qty'];
                                DB::rollBack();
                            } else {
                                $eanCode = DB::table('iv_outbound_batch')
                                    ->where('outbound_id', $job_id)
                                    ->where('product_code', $masterProd->product_code)
                                    ->groupBy('product_code')
                                    ->get()->pluck('ean_code')->toArray();
                                array_push($eanCode, $value);
                                // 01084303586758582171202501130082
                                $filteredArray = array_filter($eanCode, function ($val) {
                                    return !empty($val); // Mengecek agar nilai yang kosong atau null dihapus
                                });

                                if (empty($filteredArray)) {
                                    $ean_code = $value;
                                } else {
                                    $ean_code = implode(',', $filteredArray);
                                }
                                DB::table('iv_outbound_batch')
                                    ->where('outbound_id', $job_id)
                                    ->where('product_code', $masterProd->product_code)
                                    ->update([
                                        'ean_code' => $ean_code
                                    ]);

                                DB::commit();
                                $message = ['message' => 'success', 'sku' => $masterProd->product_code];
                            }
                        }
                    }
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

    private function getDetail($job_id)
    {
        $data = DB::table('iv_outbound_batch')->where('outbound_id', $job_id)->get();
        return $data;
    }

    private function extractDigits($input)
    {
        return substr($input, 3, 13);
    }
}
