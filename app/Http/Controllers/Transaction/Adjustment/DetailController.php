<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Adjustment\Detail as AdjustmentDetail;
use App\Models\Master\Product as MasterProduct;
use App\Models\Master\PalletUnit as MasterPalletUnit;

class DetailController extends Controller
{
    public function index(Request $request) {
        $this->menu_name = "Adjustment";

        $details = [];
        if ($request->ajax()) {
            if (!empty($request->adjust_id) && !empty($request->adjust_id)) {
                $details = DB::table("iv_adjustment_detail as a")
                                ->select("a.*", "b.principal_name", "c.product_name", "d.site_name", "e.area_name")
                                ->join("iv_principal as b", "a.principal_id", "b.id")
                                ->join("iv_product as c", "a.product_id", "c.id")
                                ->leftjoin("iv_site as d", "a.site_id", "d.id")
                                ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
                                ->where("a.adjust_id", $request->adjust_id)
                                ->get();
            }

            return datatables()->of($details)
            ->editColumn("exp_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->exp_date) );
            })
            ->editColumn("mfg_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->mfg_date) );
            })
            ->addColumn("action", function($data){
                $button = "";
                if ($data->picked_flag == "No") {
                    $button .= "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$data->id."' data-original-title='Edit' class='edit btn btn-info btn-sm edit-detail'><i class='far fa-edit'></i></a>";
                    $button .= "&nbsp;&nbsp;";
                    $button .= "<button type='button' name='delete-detail' id='".$data->id."' class='delete-detail btn btn-danger btn-sm'><i class='far fa-trash-alt'></i></button>";
                }
                return $button;
            })
            ->rawColumns(["action"])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = DB::table("iv_adjustment_detail as a")
                    ->select("a.*", "b.principal_name", "c.product_name", "d.site_name", "e.area_name")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_product as c", "a.product_id", "c.id")
                    ->leftjoin("iv_site as d", "a.site_id", "d.id")
                    ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
                    ->where("a.id", $request->id)
                    ->first();

        return response()->json($data);
    }

    public function store(Request $request) {
        $status_flag = $request->status_flag;

        if ($status_flag == "New") {

            $messsages = array(
                // "po_number.required"=>"PO number field is required.",
                // "lot_no.required"=>"Batch number field is required.",
                "site_id.required"=>"Site field is required.",
                "area_id.required"=>"Area field is required.",
                "location_code.required"=>"Location field is required.",
                // "mfg_date.required"=>"Mfg date field is required.",
                // "exp_date.required"=>"Exp date field is required.",
            );

            $rules = array(
                // "po_number" => "required",
                // "lot_no" => "required",
                "site_id" => "required",
                "area_id" => "required",
                "location_code" => "required",
                // "mfg_date" => "required",
                // "exp_date" => "required",
            );

            $validator = \Validator::make($request->all(), $rules,$messsages);

            if ($validator->fails()) {
                return response()->json(["error"=>$validator->errors()->all()]);
            }

        }

        $actual_qty = ( $request->actual_pqty * $request->uppp ) + ( $request->actual_mqty * $request->muppp ) + $request->actual_bqty;

        if ($actual_qty == 0) {
            return response()->json(["error"=>["Quantity cannot be empty!"]]);
        }

        try {
            $company_id = Auth::user()->company_id;
            $id = $request->detail_id;
            $adjust_id = $request->adjust_id;
            $status_flag = $request->status_flag;
            $entry_date = \Carbon\Carbon::now();

            if ($status_flag == "Exist") {
                $serial_id = $request->serial_id;
                $stock = StockLedger::find($serial_id);

                $pqty = ($stock->qtya  - ($stock->qtya % $stock->uppp)) / $stock->uppp;
                $mqty = (($stock->qtya % $stock->uppp) - (($stock->qtya % $stock->uppp) % $stock->muppp)) / $stock->muppp;
                $bqty = $stock->qtya % $stock->uppp % $stock->muppp;
                $qty = ( $pqty * $stock->uppp ) + ( $mqty * $stock->muppp ) + $bqty;

                AdjustmentDetail::updateOrCreate(["id" => $id],
                [
                    "company_id"=>$company_id,
                    "principal_id"=>$stock->principal_id,
                    "adjust_id"=>$adjust_id,
                    "status_flag"=>$status_flag,
                    "adjust_type"=>$request->adjust_type,
                    "job_no" => $stock->job_no,
                    "serial_id" => $serial_id,
                    "serial_no" => $stock->serial_no,
                    "product_id" => $stock->product_id,
                    "product_code" => $stock->product_code,
                    "po_number" => $stock->po_number,
                    "lot_no" => $stock->lot_no,
                    "document_ref" => $stock->document_ref,
                    "mfg_date" => $stock->mfg_date,
                    "exp_date" => $stock->exp_date,
                    "manufactur_id" => $stock->manufactur_id,
                    "status_id" => $stock->status_id,
                    "site_id" => $stock->site_id,
                    "area_id" => $stock->area_id,
                    "location_id" => $stock->location_id,
                    "location_code" => $stock->location_code,
                    "puom" => $stock->puom,
                    "muom" => $stock->muom,
                    "buom" => $stock->buom,
                    "uppp" => $stock->uppp,
                    "muppp" => $stock->muppp,
                    "pqty" => $pqty,
                    "mqty" => $mqty,
                    "bqty" => $bqty,
                    "qty" => $qty,
                    "actual_pqty" => $request->actual_pqty,
                    "actual_mqty" => $request->actual_mqty,
                    "actual_bqty" => $request->actual_bqty,
                    "actual_qty" => $actual_qty,
                    "base_unit" => $stock->base_unit,
                    "pallet_qty" => $stock->pallet_qty,
                    "entry_date" => $entry_date
                ]);
            } else if ($status_flag == "New") {
                $product_id = $request->product_id;
                $product = MasterProduct::find($product_id);

                $pallet_unit = MasterPalletUnit::where("company_id", $company_id)
                                ->where("principal_id", $product->principal_id)
                                ->where("product_id", $product_id)
                                ->first();

                if ( !isset($pallet_unit) ) {
                    $pallet_qty = 0;
                } else {
                    $pallet_qty = $pallet_unit->base_qty;
                }

                AdjustmentDetail::updateOrCreate(["id" => $id],
                [
                    "company_id"=>$company_id,
                    "principal_id"=>$product->principal_id,
                    "adjust_id"=>$adjust_id,
                    "status_flag"=>"New",
                    "adjust_type"=>"Plus",
                    "job_no" => "",
                    "serial_id" => 0,
                    "serial_no" => "",
                    "product_id" => $product_id,
                    "product_code" => $product->product_code,
                    "po_number" => $request->po_number,
                    "lot_no" => $request->lot_no,
                    "document_ref" => $request->lot_no,
                    "mfg_date" => $request->mfg_date,
                    "exp_date" => $request->exp_date,
                    "manufactur_id" => $request->manufactur_id,
                    "status_id" => $request->status_id,
                    "site_id" => $request->site_id,
                    "area_id" => $request->area_id,
                    "location_id" => $request->location_id,
                    "location_code" => $request->location_code,
                    "puom" => $product->puom,
                    "muom" => $product->muom,
                    "buom" => $product->buom,
                    "uppp" => $product->uppp,
                    "muppp" => $product->muppp,
                    "pqty" => 0,
                    "mqty" => 0,
                    "bqty" => 0,
                    "qty" => 0,
                    "actual_pqty" => $request->actual_pqty,
                    "actual_mqty" => $request->actual_mqty,
                    "actual_bqty" => $request->actual_bqty,
                    "actual_qty" => $actual_qty,
                    "base_unit" => 0,
                    "pallet_qty" => $pallet_qty,
                    "entry_date" => $entry_date
                ]);
            }

            $message = ["success"=>"Data Successfully Saved"];

            return $message;
        }
        catch(\Exception $e) {
            $message = ["error"=>[$e->getMessage()]];
        }

        return response()->json($message);
    }

    public function destroy(Request $request)
    {
        $request = AdjustmentDetail::find($request->id);

        $exception = DB::transaction(function () use ($request) {
            try {
                if ($request->picked_flag == "No") {
                    $request->delete();
                }

                DB::commit();
                $message = ["success"=>"Succesfully."];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
