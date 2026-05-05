<?php

namespace App\Http\Controllers\Transaction\Export\Inbound;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Master\Export\Forwarder as ExportForwarder;
use App\Models\Transaction\Export\InboundHeader as ExportInboundHeader;
use App\Models\Transaction\Export\InboundDetail as ExportInboundDetail;
use App\Models\Transaction\Inbound\Job as InboundJob;
use App\Models\Master\Export\Consignee as ExportConsignee;
use App\Models\Master\Export\Shipper as ExportShipper;
use App\Models\Transaction\Export\StockLedger as ExportStockLedger;
use Carbon\Carbon;

class JobController extends Controller
{
    public $menu_name = "export/inbound";

    public function index(Request $request)
    {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $list_data = InboundJob::from('ex_inbound_header as a')
                ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
                ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                ->join("mt_consignee as c", "a.consignee_id", "c.id")
                ->join("mt_shipper as d", "a.shipper_id", "d.id")
                ->where('a.branch_id', $request->branch_id)
                ->whereBetween('a.job_date', [$date_from, $date_to])
                ->where('a.status_flag', $request->status_flag)
                ->orderBy("a.job_no", "desc")
                ->get();

            return datatables()->of($list_data)
                ->editColumn('job_date', function ($data) {
                    return date('d/m/Y', strtotime($data->job_date));
                })
                ->addColumn('job_no', function ($data) {
                    $button = "";
                    $button .= '<a href="' . URL("/export/inbound/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.export.inbound.index");
    }

    public function create($id = 0)
    {
        $header = InboundJob::from('ex_inbound_header as a')
            ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("mt_consignee as c", "a.consignee_id", "c.id")
            ->join("mt_shipper as d", "a.shipper_id", "d.id")
            ->where('a.id', $id)
            ->first();

        $branchme = '';
        if (isset($header)) {
            $branchme = DB::table('mt_branch')
                ->where('id', $header->branch_id)
                ->first();
        }

        $branch_id  = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->pluck('branch_id')->toArray();

        $checker = DB::table('ex_master_checker')
            ->orderBy('name', 'ASC')
            ->where('status', 1)
            ->whereIn('branch_id', $branch_id)
            ->get();

        $branch = DB::table('mt_branch')
            ->whereIn('id', $branch_id)
            ->get();

        $data = [
            "header" => $header,
            'checker' => $checker,
            'branch' => $branch,
            'branchme' => $branchme,
        ];

        return view("transaction.export.inbound.create", $data);
    }

    public function store(Request $request)
    {
        $rules = array(
            'branch_id' => 'required',
            'vehicle_no' => 'required',
            'po_number' => 'required',
            'forwarder_id' => 'required',
            'shipper_name' => 'required',
            'consignee_name' => 'required',
            'destination' => 'required',
            'peb_no' => 'required',
            'aju_no' => 'required',
            'pic_name' => 'required',
            'qty_cargo' => 'required|integer',
            'qty_actual' => 'integer',
            'cbm' => 'required|numeric',
            'weight' => 'required|numeric',
            'total_pallet' => 'required|integer',
            'tgl_bongkar' => 'required',
            'gate_in' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $user_name = Auth::user()->username;
                $id = $request->id;

                $job = ExportInboundHeader::find($id);

                if (!isset($job)) {
                    $job = new ExportInboundHeader();

                    $job_no = $this->getJob($request->branch_id);

                    $job->job_no = $job_no;
                    $job->job_date = \Carbon\Carbon::today();
                }

                $total_pallet = $request->total_pallet;

                // $forwarder_count = DB::table('mt_forwarder as a')
                //             ->where('a.forwarder_name', 'LIKE', "$request->forwarder_name")
                //             ->orderBy("a.forwarder_name")
                //             ->count();

                // if ( $forwarder_count == 0 ) {
                //     $forwarder = new ExportForwarder();
                //     $forwarder->forwarder_name = $request->forwarder_name;
                //     $forwarder->save();

                //     $forwarder_id = $forwarder->id;
                // } else if ( $forwarder_count == 1 ) {
                //     $forwarder_id = ExportForwarder::where("forwarder_name", $request->forwarder_name)->first()->id;
                // } else {
                //     DB::rollBack();

                //     $message = ['error'=>["More than one name of forwarder"]];

                //     return $message;
                // }

                $consignee_count = DB::table('mt_consignee as a')
                    ->orderBy("a.consignee_name")
                    ->where('a.consignee_name', 'LIKE', "$request->consignee_name")
                    ->where('branch_id', $request->branch_id)
                    ->count();

                if ($consignee_count == 0) {
                    $consignee = new ExportConsignee();
                    $consignee->consignee_name = $request->consignee_name;
                    $consignee->branch_id      = $request->branch_id;
                    $consignee->save();

                    $consignee_id = $consignee->id;
                } else if ($consignee_count == 1) {
                    $consignee_id = ExportConsignee::where("consignee_name", $request->consignee_name)->first()->id;
                } else {
                    DB::rollBack();

                    $message = ['error' => ["More than one name of consignee"]];

                    return $message;
                }

                $shipper_count = DB::table('mt_shipper as a')
                    ->where('a.shipper_name', 'LIKE', "$request->shipper_name")
                    ->where('branch_id', $request->branch_id)
                    ->orderBy("a.shipper_name")
                    ->count();

                if ($shipper_count == 0) {
                    $shipper = new ExportShipper();
                    $shipper->shipper_name     = $request->shipper_name;
                    $shipper->branch_id      = $request->branch_id;
                    $shipper->save();

                    $shipper_id = $shipper->id;
                } else if ($shipper_count == 1) {
                    $shipper_id = ExportShipper::where("shipper_name", $request->shipper_name)->first()->id;
                } else {
                    DB::rollBack();

                    $message = ['error' => ["More than one name of shipper"]];

                    return $message;
                }
                $job->branch_id = $request->branch_id;
                $job->vehicle_no = $request->vehicle_no;
                $job->po_number = $request->po_number;
                $job->forwarder_id = $request->forwarder_id;
                $job->shipper_id = $shipper_id;
                $job->consignee_id = $consignee_id;
                $job->peb_no = $request->peb_no;
                $job->destination = $request->destination;
                $job->pic_name = $request->pic_name;
                $job->qty_cargo = $request->qty_cargo;
                $job->qty_actual = $request->qty_actual;
                $job->cbm = $request->cbm;
                $job->weight = $request->weight;
                $job->total_pallet = $total_pallet;
                $job->user_id = $user_name;
                $job->tgl_bongkar = $request->tgl_bongkar;
                $job->aju_no = $request->aju_no;

                $gate_in = Carbon::createFromFormat('d/m/Y H:i', $request->gate_in);
                $job->gate_in = $gate_in->format('Y-m-d H:i');
                $job->save();
                $detail_count = ExportInboundDetail::where("job_id", $job->id)->count();

                $status = "";
                if ($detail_count == 0) {
                    $status = "O";

                    $pallet = 1;
                } else {
                    if ($detail_count <> $total_pallet) {
                        ExportInboundDetail::where("job_id", $job->id)->where("quantity", 0)->delete();

                        $status = "O";

                        $pallet_count = ExportInboundDetail::where("job_id", $job->id)->count();

                        $pallet = $pallet_count == 0 ? 1 : $pallet_count + 1;
                    }
                }

                if ($status == "O") {
                    for ($i = $pallet; $i <= $total_pallet; $i++) {
                        $detail = new ExportInboundDetail();

                        $detail->job_id = $job->id;
                        $detail->serial_no = $request->po_number . "-" . $request->peb_no . "-" . Str::of($i)->padLeft(2, '0');
                        $detail->pallet_id = $i;
                        $detail->quantity = 0;
                        $detail->user_id = $user_name;
                        $detail->save();
                    }
                }

                DB::commit();

                $message = ['success' => url('/export/inbound/create/' . $job->id)];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function getJob($branch_id)
    {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = ExportInboundHeader::where('branch_id', $branch_id)
            ->whereYear('job_date', $year)
            ->whereMonth('job_date', $month)
            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = 'I' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }

    public function submit(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $user_name = Auth::user()->username;

                $job = ExportInboundHeader::find($request->job_id);

                $quantity = ExportInboundDetail::where("job_id", $request->job_id)->sum("quantity");

                if ($quantity != $job->qty_actual) {
                    DB::rollBack();

                    $message = ["error" => "Total Quantity must be same with qty actual."];

                    return $message;
                }

                $detail = ExportInboundDetail::where("job_id", $request->job_id)->get();

                foreach ($detail as $value) {
                    $ledger = new ExportStockLedger();

                    $ledger->branch_id = $job->branch_id;
                    $ledger->job_no = $job->job_no;
                    $ledger->job_date = \Carbon\Carbon::today();
                    $ledger->po_number = $job->po_number;
                    $ledger->vehicle_no = $job->vehicle_no;
                    $ledger->forwarder_id = $job->forwarder_id;
                    $ledger->consignee_id = $job->consignee_id;
                    $ledger->shipper_id = $job->shipper_id;
                    $ledger->destination = $job->destination;
                    $ledger->peb_no = $job->peb_no;
                    $ledger->aju_no = $job->aju_no;
                    $ledger->pic_name = $job->pic_name;
                    $ledger->qty_cargo = $job->qty_actual;
                    $ledger->cbm = $job->cbm;
                    $ledger->weight = $job->weight;
                    $ledger->total_pallet = $job->total_pallet;
                    $ledger->serial_no = $value->serial_no;
                    $ledger->pallet_id = $value->pallet_id;
                    $ledger->quantity = $value->quantity;
                    $ledger->user_id = $user_name;
                    $ledger->save();
                }

                $job->status_flag = 'Confirmed';
                $job->save();

                DB::commit();

                $message = ['success' => "Success"];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
