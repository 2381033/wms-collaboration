<?php

namespace App\Http\Controllers\Transaction\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Transfer\Detail as TransferDetail;
use App\Models\Transaction\Transfer\Job as TransferJob;
use App\Imports\TransferLokasiImport as TransferLokasiImport;


class DetailController extends Controller
{
    public function index(Request $request) {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->transfer_id) && !empty($request->transfer_id)) {
                $details = DB::table('iv_transfer_detail as a')
                            ->select('a.*', 'b.product_name', 'c.site_name', 'd.area_name', 'e.site_name as dest_site_name', 'f.area_name as dest_area_name')
                            ->join('iv_product as b', 'a.product_id', 'b.id')
                            ->leftjoin('iv_site as c', 'a.site_id', 'c.id')
                            ->leftjoin('iv_site_area as d', 'a.area_id', 'd.id')
                            ->leftjoin('iv_site as e', 'a.dest_site_id', 'e.id')
                            ->leftjoin('iv_site_area as f', 'a.dest_area_id', 'f.id')
                            ->where('a.transfer_id', '=', $request->transfer_id)
                            ->get();
            }

            return datatables()->of($details)
            ->editColumn('exp_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->exp_date) );
            })
            ->editColumn('mfg_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->mfg_date) );
            })
            ->addColumn('action', function($data){
                $button = "";
                if ($data->picked_flag == 'No') {
                    $button .= '<button type="button" id="'.$data->id.'" class="delete-detail btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                }
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function stockList(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $branch_id = $request->branch_id;
        $principal_id = $request->principal_id;
        $product_code = $request->product_code;
        $site_id = $request->site_id;
        $area_id = $request->area_id;
        $location_from = "";
        $location_to = "zzzzzzzzzzzzzzz";
        $status = $request->stock_status;

        if (!is_null($request->location_from) && !is_null($request->location_to)) {
            $location_from = $request->location_from;
            $location_to = $request->location_to;
        } else if (!is_null($request->location_from) && is_null($request->location_to)) {
            $location_from = $request->location_from;
            $location_to = "zzzzzzzzzzzzzzz";
        }

        if (is_null($product_code) || empty($product_code)) {
            $product_code = "%";
        }

        if (is_null($site_id) || empty($site_id)) {
            $site_id = "%";
        }

        if (is_null($area_id) || empty($area_id)) {
            $area_id = "%";
        }

        if ($status == "N") {
            $stock_status = "<";
        } else {
            $stock_status = "=";
        }

        if ($request->ajax()) {
            $stock = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.id",
                            "a.serial_no",
                            DB::raw("convert((a.qtya  - (a.qtya % c.uppp)) / c.uppp, int) as pqty"),
                            DB::raw("convert(((a.qtya % c.uppp) - ((a.qtya % c.uppp) % c.muppp)) / c.muppp, int) as mqty"),
                            DB::raw("a.qtya % c.uppp % c.muppp as bqty"),
                            "c.puom", "c.muom", "c.buom", "a.exp_date", "a.mfg_date",
                            "a.lot_no", "a.product_code", "c.product_name", "d.site_name", "e.area_name", "a.location_code")
                        ->join("users_site as b", "a.site_id", "b.site_id")
                        ->join("iv_product as c", "a.product_id", "c.id")
                        ->join("iv_site as d", "a.site_id", "d.id")
                        ->leftJoin("iv_site_area as e", "a.area_id", "e.id")
                        ->where("b.user_id", $user_id)
                        ->where("a.company_id", $company_id)
                        ->where("a.branch_id", $branch_id)
                        ->where("a.principal_id", $principal_id)
                        ->where(DB::raw("COALESCE(a.product_code, '')"), "like", $product_code)
                        ->where(DB::raw("COALESCE(a.site_id, 0)"), "LIKE", $site_id)
                        ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                        ->wherebetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                        ->where("a.qtya", ">", 0)
                        ->where("a.freeze_flag", "No")
                        ->get();

            return datatables()->of($stock)
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
                $button .= "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$data->id."' data-original-title='Edit' class='edit-stock btn btn-info btn-sm'><i class='far fa-check-square'></i></a>";
                return $button;
            })
            ->rawColumns(["action"])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = DB::table("iv_stock_ledger as a")
                    ->select(
                        DB::raw("convert((a.qtya  - (a.qtya % c.uppp)) / c.uppp, int) as pqty"),
                        DB::raw("convert(((a.qtya % c.uppp) - ((a.qtya % c.uppp) % c.muppp)) / c.muppp, int) as mqty"),
                        DB::raw("a.qtya % c.uppp % c.muppp as bqty"), "c.puom", "c.muom", "c.buom", "a.mfg_date", "a.exp_date", "a.lot_no", "a.id",
                        "c.uppp", "c.muppp", "c.product_name", "d.site_name", "e.area_name", "a.location_code", "b.principal_name", "c.unit_level", "a.status")
                    ->join("iv_principal as b", "a.principal_id", "b.id")
                    ->join("iv_product as c", "a.product_id", "c.id")
                    ->leftjoin("iv_site as d", "a.site_id", "d.id")
                    ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
                    ->where("a.id", $request->id)
                    ->first();

        return response()->json($data);
    }

    public function store(Request $request) {
        $messsages = array(
            'dest_site_id.required'=>'Destination site field is required.',
            // 'dest_area_id.required'=>'Destination area field is required.',
            // 'dest_location_id.required'=>'Destination location field is required.',
        );

        $rules = array(
            'dest_site_id' => 'required',
            // 'dest_area_id' => 'required',
            // 'dest_location_id' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $actual_qty = ( $request->actual_pqty * $request->uppp ) + ( $request->actual_mqty * $request->muppp ) + $request->actual_bqty;

        if ($actual_qty == 0) {
            return response()->json(['error'=>['Actual quantity cannot be empty!']]);
        }

        $site = DB::table("iv_site as a")
                    ->select("b.type_name")
                    ->join("iv_site_type as b", "a.type_id", "b.id")
                    ->where("a.id", $request->dest_site_id)
                    ->first();

        if ($site->type_name == "Racking") {
            $messsages = array(
                'dest_area_id.required'=>'Destination area field is required.',
                'dest_location_id.required'=>'Destination location field is required.',
            );

            $rules = array(
                'dest_area_id' => 'required',
                'dest_location_id' => 'required',
            );

            $validator = \Validator::make($request->all(), $rules,$messsages);

            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }

        $company_id = Auth::user()->company_id;
        $entry_by = Auth::user()->user_name;
        $id = $request->detail_id;
        $transfer_id = $request->transfer_detail;
        $serial_id = $request->serial_id;
        $entry_date = \Carbon\Carbon::now();

        $job = TransferJob::find($transfer_id);
        $stock = StockLedger::find($serial_id);

        $qty = ( $request->pqty * $request->uppp ) + ( $request->mqty * $request->muppp ) + $request->bqty;

        $serial_use = DB::table("iv_transfer_detail")->where('transfer_id', $transfer_id)->where('serial_id', $serial_id)->get()->count();

        if ( $qty > $stock->qtya ) {
            $message = ['error'=>['Quantity tidak boleh lebih besar dari stock yaitu : ' . $stock->qtya]];
            return response()->json($message);
        }
        // if ($serial_use >= 1) {
        //     $message = ['error'=>['Stock already selected.']];
        //     return response()->json($message);
        // }
        try {
            TransferDetail::updateOrCreate(['id' => $id],
            [
                'company_id'=>$company_id,
                'principal_id'=>$stock->principal_id,
                'transfer_id'=>$transfer_id,
                'job_no' => $stock->job_no,
                'serial_id' => $serial_id,
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
                'pqty' => $request->pqty,
                'mqty' => $request->mqty,
                'bqty' => $request->bqty,
                'qty' => $qty,
                'actual_pqty' => $request->actual_pqty,
                'actual_mqty' => $request->actual_mqty,
                'actual_bqty' => $request->actual_bqty,
                'actual_qty' => $actual_qty,
                'dest_site_id' => $request->dest_site_id,
                'dest_area_id' => $request->dest_area_id,
                'dest_location_id' => $request->dest_location_id,
                'dest_location_code' => $request->dest_location_code,
                'base_unit' => $stock->base_unit,
                'pallet_qty' => $stock->pallet_qty,
                'srno' => $stock->serial_no,
                'entry_date' => $entry_date,
                // 'status' => $request->product_status
            ]);

            $job->entry_flag = 'Yes';
            $job->entry_by = $entry_by;
            $job->entry_date = \Carbon\Carbon::now();
            $job->save();

            $stock->status = $request->product_status;
            $stock->save();

            $message = ['success'=>'Added new records.'];
        }
        catch(\Exception $e) {
            $message = ['error'=>$e->getMessage()];
        }

        return response()->json($message);
    }

    public function destroy(Request $request)
    {
        try {
            TransferDetail::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }

    public function upload(Request $request) {
        $this->validate($request, [
            'excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('excel');
        Excel::import(new TransferLokasiImport($request->job_id), $file);
        return back();
    }
}
