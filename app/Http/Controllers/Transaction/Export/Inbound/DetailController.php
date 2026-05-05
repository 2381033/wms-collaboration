<?php

namespace App\Http\Controllers\Transaction\Export\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Transaction\Export\InboundHeader as ExportInboundHeader;
use App\Models\Transaction\Export\InboundDetail as ExportInboundDetail;
use App\Models\Transaction\Export\StockLedger as ExportStockLedger;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->job_id) && !empty($request->job_id)) {
                $details = DB::table("ex_inbound_detail as a")
                    ->where("a.job_id", $request->job_id)
                    ->get();
            }

            return datatables()->of($details)
                ->editColumn("quantity", function ($data) {
                    $input = "<input type='hidden' value='" . $data->id . "' name='id[]' class='form-control'/><input type='text' value='$data->quantity' name='quantity[]' class='form-control'/>";

                    return $input;
                })
                ->rawColumns(["quantity"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job_id = $request->job_id;
                $detail_id = $request->id;
                $quantity = $request->quantity;

                $job = ExportInboundHeader::find($job_id);

                $total = 0;
                for ($i = 0; $i < count($detail_id); $i++) {
                    $detail = ExportInboundDetail::find($detail_id[$i]);

                    $detail->quantity = $quantity[$i];
                    $detail->save();

                    $total = $total + $quantity[$i];
                }

                if ($total != $job->qty_actual) {
                    DB::rollBack();

                    $message = ["error" => "Total Quantity must be same with qty actual."];

                    return $message;
                }

                DB::commit();

                $message = ["success" => "Sukses"];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => $e->getMessage()];

                return $message;
            }
        });
        return response()->json($exception);
    }

    public function palletTag($id)
    {
        $view = DB::table("ex_inbound_header as a")
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where("a.id", $id)
            ->first();

        $list_data = DB::table("ex_inbound_detail as a")
            ->where("a.job_id", $id)
            ->where("a.quantity", ">", 0)
            ->get();

        $data = [
            "view" => $view,
            "list_data" => $list_data
        ];

        return view("transaction.export.inbound.barcode", $data);
    }

    public function updateNoPeb()
    {
        return view("transaction.export.update_peb");
    }

    public function getListUpdateNoPeb()
    {
        $data = DB::table('ex_inbound_header as a')
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where('a.branch_id', $this->myBranch())
            ->whereNotNull('a.aju_no')
            ->where('a.peb_no', 0)
            ->where('a.peb_no', '-')
            ->where('a.updated_peb', 'No')
            ->orderBy('a.id', 'DESC')
            ->get();

        return datatables()->of($data)->make(true);
    }

    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    public function updatePeb(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job = ExportInboundHeader::find($request->id);
                $job->peb_no = $request->peb_no;
                $job->updated_peb = 'Yes';
                $job->save();

                DB::table('ex_stock_ledger')
                    ->where('job_no', $job->job_no)
                    ->update([
                        'peb_no' => $request->peb_no
                    ]);
                DB::commit();
                $message = ["message" => "success"];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => $e->getMessage()];

                return $message;
            }
        });
        return response()->json($exception);
    }
}
