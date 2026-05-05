<?php

namespace App\Http\Controllers\Transaction\Export\Outbound;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Outbound\Job as OutboundJob;
use App\Models\Transaction\Export\OutboundHeader as ExportOutboundHeader;
use App\Models\Transaction\Inbound\Job as InboundJob;
use App\Models\Transaction\Export\OutboundOrder as ExportOutboundOrder;
use App\Models\Transaction\Export\OutboundDetail as ExportOutboundDetail;

class JobController extends Controller
{
    public $menu_name = "export/outbound";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($request->date_from)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($request->date_to)->format('Y-m-d');

            $list_data = OutboundJob::from('ex_outbound_header as a')
                            ->select('a.*', "b.forwarder_name")
                            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                            ->where('a.branch_id', $request->branch_id)
                            ->whereBetween('a.job_date', [$date_from, $date_to])
                            ->where('a.status_flag', $request->status_flag)
                            ->orderBy("a.job_no", "desc")
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('job_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->job_date) );
                })
                ->addColumn('job_no', function($data){
                    $button = "";
                    $button .= '<a href="' . URL("/export/outbound/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.export.outbound.index");
    }

    public function create($id = 0) {
        $company_id = Auth::user()->company_id;

        $header = InboundJob::from('ex_outbound_header as a')
                        ->select('a.*', "b.forwarder_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->where('a.id', $id)
                        ->first();

        $size_list = DB::table("iv_container_size")->where("company_id", $company_id)->where("active", "Yes")->get();

        $data = [
            "header" => $header,
            "size_list" => $size_list
        ];

        return view("transaction.export.outbound.create", $data);
    }

    public function store(Request $request) {
        $rules = array(
            'branch_id' => 'required',
            'forwarder_id' => 'required',
            'size_id' => 'required',
            'container_no' => 'required',
            'destination' => 'required',
            // 'surveyor_name' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $user_name = Auth::user()->username;
                $id = $request->id;

                $job = ExportOutboundHeader::find($id);

                if (!isset($job)) {
                    $job = new ExportOutboundHeader();

                    $job_no = $this->getJob($request->branch_id);

                    $job->job_no = $job_no;
                    $job->job_date = \Carbon\Carbon::today();
                }

                $job->branch_id = $request->branch_id;
                $job->forwarder_id = $request->forwarder_id;
                $job->size_id = $request->size_id;
                $job->container_no = $request->container_no;
                $job->destination = $request->destination;
                $job->vessel_name = $request->vessel_name;
                $job->surveyor_name = $request->surveyor_name;
                $job->voyage_no = $request->voyage_no;
                $job->remarks = $request->remarks;
                $job->qty_cargo = 0;
                $job->cbm = 0;
                $job->weight = 0;
                $job->total_pallet = 0;
                $job->user_id = $user_name;
                $job->save();

                DB::commit();

                $message = ['success'=>url('/export/outbound/create/' . $job->id)];

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

    private function getJob($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = ExportOutboundHeader::where('branch_id', $branch_id)
                        ->whereYear('job_date', $year)
                        ->whereMonth('job_date', $month)
                        ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = 'O' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $user_name = Auth::user()->username;

                $job = ExportOutboundHeader::find($request->job_id);

                $orders = ExportOutboundOrder::where("job_id", $request->job_id)->get();

                foreach ($orders as $order) {
                    $detail_count = ExportOutboundDetail::where("job_id", $request->job_id)
                                    ->where("order_id", $order->id)
                                    ->where("status_flag", "Confirmed")
                                    ->count();

                    if ( $detail_count == 0 ) {
                        DB::rollBack();

                        $message = ['error'=>"Pallets unprocessed."];

                        return $message;
                    }

                    if ( $detail_count == $order->total_pallet ) {
                        $status = "Full";
                    } else {
                        $status = "Partial";
                    }

                    $order->status_flag = $status;
                    $order->save();
                }

                $job->status_flag = "Confirmed";
                $job->save();

                DB::commit();

                $message = ['success'=>"Success"];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function update(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $job = ExportOutboundHeader::find($request->job_id);

                $job->surveyor_name = $request->surveyor_name;
                $job->vessel_name = $request->vessel_name;
                $job->voyage_no = $request->voyage_no;
                $job->save();

                DB::commit();

                $message = ['success'=>"Success"];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
