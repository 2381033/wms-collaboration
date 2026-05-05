<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Transaction\Adjustment\Job as AdjustmentJob;
use App\Models\Transaction\Adjustment\Batch as AdjustmentBatch;

class AutorizationController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $details = DB::table("iv_adjustment_job as a")
                            ->select("a.*", "b.type_name")
                            ->join("iv_adjustment_type as b", "a.type_id", "b.id")
                            ->where("a.company_id", $company_id)
                            ->whereBetween('a.adjust_date', [$date_from, $date_to])
                            ->where("a.confirmed_flag", $request->confirmed_flag)
                            ->get();

            return datatables()->of($details)
            ->editColumn("adjust_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->adjust_date) );
            })
            ->editColumn("confirmed_flag", function ($data) {
                if ($data->confirmed_flag == "Yes") {
                    $status = "<div class='btn btn-sm btn-success'>Completed</div>";
                } else {
                    $status = "<div class='btn btn-sm btn-danger'>Open</div>";
                }
                return $status;
            })
            ->addColumn("adjust_no", function($data){
                $button = "";
                $button .= "<a href='" . URL("/inventory/stock-adjustment/autorization/view/$data->id") . "' class='btn btn-default btn-sm'><i class='far fa-file'></i> " . $data->adjust_no . "</a>";
                return $button;
            })
            ->rawColumns(["confirmed_flag", "adjust_no"])
            ->addIndexColumn()
            ->make(true);
        }

        return view("transaction.adjustment.autorization.index");
    }

    public function view($id = 0) {
        $job_view = DB::table("iv_adjustment_job as a")
                        ->select("a.*", "b.type_name")
                        ->join("iv_adjustment_type as b", "a.type_id", "b.id")
                        ->where("a.id", $id)
                        ->first();

        $adjustment_type = DB::table("iv_adjustment_type")->where("active", "Yes")->get();

        $data = [
            "job_view" => $job_view,
            "type_list" => $adjustment_type
        ];

        return view("transaction.adjustment.autorization.view", $data);
    }

    public function detail(Request $request) {
        $detail = [];
        if ($request->ajax()) {
            if (!empty($request->adjust_id) && !empty($request->adjust_id)) {
                $detail = DB::table('iv_adjustment_batch as a')
                        ->select('a.*', 'b.principal_name', 'c.product_name', 'd.site_name', 'e.area_name')
                        ->join('iv_principal as b', 'a.principal_id', 'b.id')
                        ->join('iv_product as c', 'a.product_id', 'c.id')
                        ->join('iv_site as d', 'a.site_id', 'd.id')
                        ->leftjoin('iv_site_area as e', 'a.area_id', 'e.id')
                        ->where('a.adjust_id', $request->adjust_id)
                        ->where('a.status_flag', 'No')
                        ->get();
            }

            return datatables()->of($detail)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" required="required" name="confirm_id[]" class="confirm-check" id="' . $data->id . '" value="' . $data->id . '">';
            })
            ->editColumn('exp_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->exp_date) );
            })
            ->editColumn('mfg_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->mfg_date) );
            })
            ->rawColumns(['check'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();

            try {
                $data = $request->confirm_id;

                foreach ($data as $id) {
                    $batch = AdjustmentBatch::find($id);

                    $batch->status_flag = 'Yes';
                    $batch->status_by = $confirmed_by;
                    $batch->status_date = $confirmed_date;
                    $batch->save();
                }

                DB::commit();

                $response = ["success"=>"Successfully"];

                return $response;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $response = ["error"=>$e->getMessage()];

                return $response;
            }
        });

        return response()->json($exception);
    }

    public function upload(Request $request) {
        try {
            $adjustment = AdjustmentJob::findOrFail($request->adjust_id);

            Storage::delete("public/adjustment/". $adjustment->filename);

            if ($request->file("filename") == null || $request->file("filename") == "") {
                return response()->json(["error"=>["File gambar harus diisi."]]);
            }

            if ($request->has("filename")) {
                $imagePath = $request->file("filename");
                $filename = $adjustment->adjust_no . "." . $imagePath->extension();

                $path = $request->file("filename")->storeAs("public/adjustment", $filename);
            }

            $adjustment->filename = $filename;
            $adjustment->save();
        } catch (\Illuminate\Database\QueryException $ex){
            return response()->json([ "error"=> [$ex->getMessage()] ]);
        }

        return response()->json(["success"=>"Upload successfully."]);
    }
}
